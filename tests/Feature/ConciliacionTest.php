<?php

namespace Tests\Feature;

use App\Models\Pago;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConciliacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    protected function caso2026()
    {
        $saldo = DB::selectOne("
            SELECT s.id, s.cliente_id, s.Monto AS monto
            FROM saldos s
            WHERE s.Detalle = 'Pago de deuda' AND DATE(s.created_at) >= '2026-01-01'
              AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = s.id)
            LIMIT 1
        ");
        $this->assertNotNull($saldo, 'Se necesita un cobro suelto 2026 para la prueba');
        $venta = Venta::where('cliente_id', $saldo->cliente_id)->orderBy('id')->get()
            ->first(function ($v) {
                if (!$v->salida) {
                    return false;
                }
                return round($v->salida->Total - Pago::where('venta_id', $v->id)->sum('monto'), 2) > 0;
            });
        $this->assertNotNull($venta, 'Se necesita una venta pendiente para la prueba');
        return [$saldo, $venta];
    }

    public function test_index_carga()
    {
        $this->actingAs(User::first());
        $this->get('/conciliacion')->assertOk();
    }

    public function test_pendientes_devuelve_sugerencias()
    {
        $this->actingAs(User::first());
        [$saldo] = $this->caso2026();
        $response = $this->get("/conciliacion/pendientes/{$saldo->cliente_id}");
        $response->assertOk();
        $response->assertJsonStructure(['cliente', 'cobros', 'ventas_pendientes', 'sin_identificar']);
        $this->assertNotEmpty($response->json('cobros'));
        $this->assertNotEmpty($response->json('ventas_pendientes'));
        $this->assertNotNull($response->json('cobros.0.sugerencia.venta_id'));
    }

    public function test_asignar_crea_pago_enlazado()
    {
        $this->actingAs(User::first());
        [$saldo, $venta] = $this->caso2026();
        $pend = round($venta->salida->Total - Pago::where('venta_id', $venta->id)->sum('monto'), 2);
        $monto = min(1, $pend, (float) $saldo->monto);
        $sumaAntes = (float) Pago::where('venta_id', $venta->id)->sum('monto');
        $response = $this->post('/conciliacion', [
            'cliente_id' => $saldo->cliente_id,
            'venta_id' => $venta->id,
            'monto' => $monto,
            'saldo_id' => $saldo->id,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertEquals($sumaAntes + $monto, (float) Pago::where('venta_id', $venta->id)->sum('monto'));
        $nuevo = Pago::where('saldo_id', $saldo->id)->orderByDesc('id')->first();
        $this->assertNotNull($nuevo);
        $this->assertEquals($venta->id, $nuevo->venta_id);
    }

    public function test_asignar_rechaza_monto_mayor_al_pendiente()
    {
        $this->actingAs(User::first());
        [$saldo, $venta] = $this->caso2026();
        $pend = round($venta->salida->Total - Pago::where('venta_id', $venta->id)->sum('monto'), 2);
        $response = $this->post('/conciliacion', [
            'cliente_id' => $saldo->cliente_id,
            'venta_id' => $venta->id,
            'monto' => $pend + 1000,
            'saldo_id' => $saldo->id,
        ]);
        $response->assertSessionHasErrors('monto');
    }

    public function test_asignar_rechaza_venta_de_otro_cliente()
    {
        $this->actingAs(User::first());
        [$saldo] = $this->caso2026();
        $otra = Venta::whereNotNull('cliente_id')->where('cliente_id', '!=', $saldo->cliente_id)->first();
        $response = $this->post('/conciliacion', [
            'cliente_id' => $saldo->cliente_id,
            'venta_id' => $otra->id,
            'monto' => 1,
            'saldo_id' => $saldo->id,
        ]);
        $response->assertSessionHasErrors('venta_id');
    }
}

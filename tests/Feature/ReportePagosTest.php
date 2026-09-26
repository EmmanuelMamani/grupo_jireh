<?php

namespace Tests\Feature;

use App\Models\Ingreso;
use App\Models\Pago;
use App\Models\PagoProveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportePagosTest extends TestCase
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

    public function test_pago_rechaza_venta_de_otro_cliente()
    {
        $this->actingAs(User::first());
        $otra = Venta::whereNotNull('cliente_id')->where('cliente_id', '!=', 75)->first();
        $response = $this->post('/saldos', ['cliente' => 75, 'monto' => 1, 'venta_id' => $otra->id]);
        $response->assertSessionHasErrors('venta_id');
    }

    public function test_pago_imputa_fifo_y_suma_exacta()
    {
        $this->actingAs(User::first());
        $primeraPendiente = Venta::where('cliente_id', 75)->orderBy('id')->get()
            ->first(function ($v) {
                if (!$v->salida) {
                    return false;
                }
                return round($v->salida->Total - Pago::where('venta_id', $v->id)->sum('monto'), 2) > 0;
            });
        $this->assertNotNull($primeraPendiente, 'Se necesita una venta pendiente para la prueba');
        $sumaAntes = (float) Pago::where('cliente_id', 75)->sum('monto');
        $response = $this->post('/saldos', ['cliente' => 75, 'monto' => 10]);
        $response->assertSessionHasNoErrors();
        $this->assertEquals($sumaAntes + 10, (float) Pago::where('cliente_id', 75)->sum('monto'));
        $nuevo = Pago::where('cliente_id', 75)->orderByDesc('id')->first();
        $this->assertEquals($primeraPendiente->id, $nuevo->venta_id);
    }

    public function test_pagar_lote_rechaza_monto_excesivo()
    {
        $this->actingAs(User::first());
        $lote = Ingreso::where('Pagado', 0)->first();
        $this->assertNotNull($lote, 'Se necesita un lote impago para la prueba');
        $response = $this->post("/pagar_lote/{$lote->id}", ['monto' => 999999999]);
        $response->assertSessionHasErrors('monto');
        $this->assertEquals(0, PagoProveedor::where('ingreso_id', $lote->id)->count());
    }

    public function test_pagar_lote_parcial_registra_pago_y_cuenta()
    {
        $this->actingAs(User::first());
        $lote = Ingreso::where('Pagado', 0)->first();
        $this->assertNotNull($lote, 'Se necesita un lote impago para la prueba');
        $response = $this->post("/pagar_lote/{$lote->id}", ['monto' => 1, 'fecha' => '2024-05-06']);
        $response->assertSessionHasNoErrors();
        $pp = PagoProveedor::where('ingreso_id', $lote->id)->first();
        $this->assertNotNull($pp);
        $this->assertEquals(1, (float) $pp->monto);
        $this->assertEquals('2024-05-06', $pp->fecha);
        $this->assertNotNull($pp->cuenta_id);
        $this->assertEquals(0, (int) Ingreso::find($lote->id)->Pagado);
    }

    public function test_todo_pago_tiene_origen()
    {
        $huerfanos = DB::table('pagos')
            ->whereNull('venta_id')->whereNull('saldo_id')->whereNull('cuenta_id')
            ->count();
        $this->assertEquals(0, $huerfanos, 'Hay pagos sin ningún enlace de origen');
    }

    public function test_reporte_incluye_nuevos_bloques()
    {
        $this->actingAs(User::first());
        $response = $this->post('/estado-cuentas/reporte', [
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2024-12-31',
        ]);
        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonStructure([
            'ventas_costos_totales' => ['ventas', 'costo', 'utilidad', 'margen'],
            'cobranza' => ['cobrado_total', 'cobrado_del_periodo', 'pendiente_total', 'ventas_con_pendiente'],
            'proveedores' => ['lotes', 'compras_total', 'pagado_rango', 'deuda_total'],
            'totales' => ['total_salida'],
        ]);
    }
}

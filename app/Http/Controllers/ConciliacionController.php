<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConciliacionController extends Controller
{
    const DESDE = '2026-01-01';

    public function index(){
        $candidatos = DB::select("
            SELECT s.cliente_id AS cliente_id, s.id AS saldo_id, s.Monto AS monto
            FROM saldos s
            WHERE s.Detalle = 'Pago de deuda' AND DATE(s.created_at) >= ?
        ", [self::DESDE]);
        $map = [];
        foreach ($candidatos as $row) {
            $disp = $this->disponibleSaldo($row->saldo_id);
            if ($disp > 0) {
                if (!isset($map[$row->cliente_id])) {
                    $map[$row->cliente_id] = ['n' => 0, 'monto' => 0];
                }
                $map[$row->cliente_id]['n']++;
                $map[$row->cliente_id]['monto'] = round($map[$row->cliente_id]['monto'] + $disp, 2);
            }
        }
        $ids = array_keys($map);
        $clientes = $ids ? Cliente::whereIn('id', $ids)->orderBy('Nombre')->get() : collect();
        return view('conciliacion', ['clientes' => $clientes, 'resumen' => $map, 'desde' => self::DESDE]);
    }

    protected function disponibleSaldo($saldoId){
        $saldo = DB::table('saldos')->where('id', $saldoId)->first();
        if (!$saldo) {
            return 0;
        }
        $usado = (float) Pago::where('saldo_id', $saldoId)->sum('monto');
        return round($saldo->Monto - $usado, 2);
    }

    protected function disponibleCuenta($cuentaId){
        $cuenta = DB::table('cuentas')->where('id', $cuentaId)->first();
        if (!$cuenta) {
            return 0;
        }
        $usado = (float) Pago::where('cuenta_id', $cuentaId)->sum('monto');
        return round($cuenta->Monto - $usado, 2);
    }

    protected function nombreCuenta($detalle){
        return trim(mb_substr($detalle, 20));
    }

    public function pendientes($clienteId){
        $cliente = Cliente::findOrFail($clienteId);

        $saldos = DB::select("
            SELECT s.id, s.Monto AS monto, DATE(s.created_at) AS fecha
            FROM saldos s
            WHERE s.cliente_id = ? AND s.Detalle = 'Pago de deuda' AND DATE(s.created_at) >= ?
            ORDER BY s.created_at
        ", [$clienteId, self::DESDE]);

        $cuentas = DB::select("
            SELECT c.id, c.Monto AS monto, c.Fecha AS fecha, c.Detalle AS detalle
            FROM cuentas c
            WHERE c.Detalle = ? AND c.Fecha >= ?
            ORDER BY c.Fecha
        ", ['Pago de de saldo de ' . $cliente->Nombre, self::DESDE]);

        $nombres = Cliente::pluck('Nombre')->all();
        $sinIdentificar = DB::select("
            SELECT c.id, c.Monto AS monto, c.Fecha AS fecha, c.Detalle AS detalle
            FROM cuentas c
            WHERE c.Detalle LIKE 'Pago de de saldo de %' AND c.Fecha >= ?
            ORDER BY c.Fecha DESC LIMIT 200
        ", [self::DESDE]);
        $bolsa = [];
        foreach ($sinIdentificar as $c) {
            $existe = false;
            foreach ($nombres as $nn) {
                if ($nn === $this->nombreCuenta($c->detalle)) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $disp = $this->disponibleCuenta($c->id);
                if ($disp > 0) {
                    $bolsa[] = [
                        'cuenta_id' => $c->id, 'monto' => (float) $c->monto,
                        'disponible' => $disp, 'fecha' => $c->fecha,
                        'nombre' => $this->nombreCuenta($c->detalle),
                    ];
                }
            }
        }

        $cobros = [];
        foreach ($saldos as $s) {
            $disp = $this->disponibleSaldo($s->id);
            if ($disp > 0) {
                $cobros[] = ['tipo' => 'saldo', 'saldo_id' => $s->id, 'cuenta_id' => null,
                    'monto' => (float) $s->monto, 'disponible' => $disp, 'fecha' => $s->fecha];
            }
        }
        foreach ($cuentas as $c) {
            $disp = $this->disponibleCuenta($c->id);
            if ($disp > 0) {
                $cobros[] = ['tipo' => 'cuenta', 'saldo_id' => null, 'cuenta_id' => $c->id,
                    'monto' => (float) $c->monto, 'disponible' => $disp, 'fecha' => (string) $c->fecha];
            }
        }
        usort($cobros, function ($a, $b) {
            return $b['disponible'] <=> $a['disponible'];
        });

        $ventas = Venta::where('cliente_id', $clienteId)->with('salida')->orderBy('id')->get();
        $pendientes = [];
        foreach ($ventas as $v) {
            if (!$v->salida) {
                continue;
            }
            $pend = round($v->salida->Total - (float) Pago::where('venta_id', $v->id)->sum('monto'), 2);
            if ($pend > 0) {
                $pendientes[] = ['id' => $v->id, 'fecha' => substr((string) $v->created_at, 0, 10),
                    'total' => (float) $v->salida->Total, 'pendiente' => $pend,
                    'contado' => (bool) $v->salida->al_contado];
            }
        }

        foreach ($cobros as &$cobro) {
            $cobro['sugerencia'] = null;
            foreach ($pendientes as $vp) {
                $cobro['sugerencia'] = ['venta_id' => $vp['id'],
                    'monto' => min($cobro['disponible'], $vp['pendiente'])];
                break;
            }
        }

        return response()->json([
            'cliente' => ['id' => $cliente->id, 'nombre' => $cliente->Nombre],
            'cobros' => $cobros,
            'ventas_pendientes' => $pendientes,
            'sin_identificar' => $bolsa,
        ]);
    }

    public function asignar(Request $request){
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'venta_id' => 'required|exists:ventas,id',
            'monto' => 'required|numeric|gt:0',
            'saldo_id' => 'nullable|exists:saldos,id',
            'cuenta_id' => 'nullable|exists:cuentas,id',
        ]);

        $venta = Venta::find($request->venta_id);
        if (!$venta || !$venta->salida || $venta->cliente_id != $request->cliente_id) {
            return redirect()->back()->withErrors(['venta_id' => 'La venta no pertenece al cliente.'])->withInput();
        }
        $pendVenta = round($venta->salida->Total - (float) Pago::where('venta_id', $venta->id)->sum('monto'), 2);
        if ($request->monto > $pendVenta) {
            return redirect()->back()->withErrors(['monto' => 'El monto supera el pendiente de la venta (Bs ' . number_format($pendVenta, 2) . ').'])->withInput();
        }
        if (!$request->saldo_id && !$request->cuenta_id) {
            return redirect()->back()->withErrors(['monto' => 'Debes elegir al menos un cobro (saldo o cuenta).'])->withInput();
        }

        $fecha = null;
        if ($request->saldo_id) {
            $saldo = DB::table('saldos')->where('id', $request->saldo_id)->first();
            if (!$saldo || $saldo->cliente_id != $request->cliente_id || $saldo->Detalle !== 'Pago de deuda') {
                return redirect()->back()->withErrors(['saldo_id' => 'El saldo no es un cobro válido del cliente.'])->withInput();
            }
            if (substr((string) $saldo->created_at, 0, 10) < self::DESDE) {
                return redirect()->back()->withErrors(['saldo_id' => 'Solo se concilian cobros desde ' . self::DESDE . '.'])->withInput();
            }
            if ($request->monto > $this->disponibleSaldo($request->saldo_id)) {
                return redirect()->back()->withErrors(['monto' => 'El monto supera el disponible del cobro.'])->withInput();
            }
            $fecha = substr((string) $saldo->created_at, 0, 10);
        }
        if ($request->cuenta_id) {
            $cuenta = DB::table('cuentas')->where('id', $request->cuenta_id)->first();
            if (!$cuenta || $cuenta->Detalle === null || stripos($cuenta->Detalle, 'Pago de de saldo de ') !== 0) {
                return redirect()->back()->withErrors(['cuenta_id' => 'La cuenta no es un cobro válido.'])->withInput();
            }
            if ((string) $cuenta->Fecha < self::DESDE) {
                return redirect()->back()->withErrors(['cuenta_id' => 'Solo se concilian cobros desde ' . self::DESDE . '.'])->withInput();
            }
            $cliente = Cliente::find($request->cliente_id);
            $nombreCuenta = $this->nombreCuenta($cuenta->Detalle);
            $esDelCliente = ($nombreCuenta === $cliente->Nombre);
            if (!$esDelCliente) {
                $coincideAlguno = Cliente::where('Nombre', $nombreCuenta)->exists();
                if ($coincideAlguno) {
                    return redirect()->back()->withErrors(['cuenta_id' => 'Ese cobro pertenece a otro cliente (' . $nombreCuenta . ').'])->withInput();
                }
            }
            if ($request->monto > $this->disponibleCuenta($request->cuenta_id)) {
                return redirect()->back()->withErrors(['monto' => 'El monto supera el disponible del cobro.'])->withInput();
            }
            $fecha = $fecha ?? (string) $cuenta->Fecha;
        }

        DB::beginTransaction();
        try {
            $pago = new Pago();
            $pago->venta_id = $venta->id;
            $pago->saldo_id = $request->saldo_id;
            $pago->cuenta_id = $request->cuenta_id;
            $pago->cliente_id = $request->cliente_id;
            $pago->monto = $request->monto;
            $pago->fecha = $fecha;
            $pago->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('conciliacion')->with('registrar', 'ok');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\pagoRequest;
use App\Http\Requests\saldoRequest;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Pago;
use App\Models\Saldo;
use App\Models\Venta;
use Illuminate\Http\Request;
use App\Models\Zona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaldoController extends Controller
{
    public function vistaRegistro(){
        $clientes=Cliente::all()->where('Activo',1);
        $zonas=Zona::all();
        return view("saldo_pasado",["clientes"=> $clientes,"zonas"=>$zonas]);
    }

    public function registro(saldoRequest $request){
        $saldo=new Saldo();
        $consulta=0;
        if(Saldo::all()->where("cliente_id",$request->cliente)->isNotEmpty()){
            $consulta=Saldo::all()->where("cliente_id",$request->cliente)->last()->Saldo;
        }
        $saldo->Monto= $request->monto;
        $saldo->Saldo= $request->monto + $consulta;
        $saldo->Detalle= $request->motivo;
        $saldo->cliente_id= $request->cliente;
        $saldo->save();
        return redirect()->route('saldo_pasado')->with('registrar', 'ok');
    }

    public function vistaReporte(){
    }
    public function vistaPago() {
        $zonas = Zona::all();
        return view("saldos", ['zonas' => $zonas]);
    }
    public function clientesPorZona($zonaId)
    {
        $clientes = DB::table('clientes as c')
            ->join(DB::raw('
            (SELECT cliente_id, saldo
             FROM saldos
             WHERE (cliente_id, created_at) IN (
                 SELECT cliente_id, MAX(created_at)
                 FROM saldos
                 GROUP BY cliente_id
             )
            ) as s
        '), 's.cliente_id', '=', 'c.id')
            ->where('c.zona_id', $zonaId)
            ->where('s.saldo', '>', 0)
            ->orderBy('c.nombre')
            ->select('c.id', 'c.nombre', 's.saldo')
            ->get();

        return response()->json($clientes);
    }


    public function ventasPendientes($clienteId){
        $ventas = Venta::where('cliente_id', $clienteId)->with('salida')->orderBy('id')->get();
        $out = [];
        foreach ($ventas as $venta) {
            if (!$venta->salida) {
                continue;
            }
            $pendiente = $this->pendienteVenta($venta);
            if ($pendiente > 0) {
                $out[] = [
                    'id' => $venta->id,
                    'fecha' => substr((string) $venta->created_at, 0, 10),
                    'total' => $venta->salida->Total,
                    'pendiente' => $pendiente,
                    'contado' => (bool) $venta->salida->al_contado,
                ];
            }
        }
        return response()->json($out);
    }

    protected function pendienteVenta($venta){
        if (!$venta->salida) {
            return 0;
        }
        $pagado = Pago::where('venta_id', $venta->id)->sum('monto');
        return max(0, round($venta->salida->Total - $pagado, 2));
    }

    protected function imputarPago($clienteId, $monto, $fecha, $saldoId, $cuentaId, $ventaIdPrioritaria = null){
        $restante = round($monto, 2);
        $ventas = Venta::where('cliente_id', $clienteId)->orderBy('id')->get();
        if ($ventaIdPrioritaria) {
            $ventas = $ventas->sortBy(function ($v) use ($ventaIdPrioritaria) {
                return $v->id == $ventaIdPrioritaria ? 0 : 1;
            })->values();
        }
        foreach ($ventas as $venta) {
            if ($restante <= 0) {
                break;
            }
            $pendiente = $this->pendienteVenta($venta);
            if ($pendiente <= 0) {
                continue;
            }
            $asignado = min($restante, $pendiente);
            $pago = new Pago();
            $pago->venta_id = $venta->id;
            $pago->saldo_id = $saldoId;
            $pago->cuenta_id = $cuentaId;
            $pago->cliente_id = $clienteId;
            $pago->monto = $asignado;
            $pago->fecha = $fecha;
            $pago->save();
            $restante = round($restante - $asignado, 2);
        }
        if ($restante > 0) {
            $pago = new Pago();
            $pago->venta_id = null;
            $pago->saldo_id = $saldoId;
            $pago->cuenta_id = $cuentaId;
            $pago->cliente_id = $clienteId;
            $pago->monto = $restante;
            $pago->fecha = $fecha;
            $pago->save();
        }
    }

    public function Pago(pagoRequest $request){
        $request->validate([
            'venta_id' => 'nullable|exists:ventas,id',
        ]);
        $ultimo = Saldo::all()->where("cliente_id",$request->cliente)->last();
        $pasado = $ultimo ? $ultimo->Saldo : 0;
        if ($request->monto > $pasado ) {
            return redirect()->back()->withErrors(['monto' => 'El monto ingresado excede el saldo actual del cliente.'])->withInput();
        }
        if ($request->venta_id) {
            $ventaElegida = Venta::find($request->venta_id);
            if (!$ventaElegida || $ventaElegida->cliente_id != $request->cliente) {
                return redirect()->back()->withErrors(['venta_id' => 'La venta seleccionada no pertenece al cliente.'])->withInput();
            }
        }
        DB::beginTransaction();
        try {
            $saldo=new Saldo();
            $saldo->cliente_id=$request->cliente;
            $saldo->Monto=$request->monto;
            $saldo->Saldo=$pasado - $request->monto;
            $saldo->Detalle="Pago de deuda";
            $saldo->save();
            $cuenta=new Cuenta();
            $usuario= Auth::user();
            $cuenta->Monto=$request->monto;
            $cuenta->user_id=$usuario->id;
            $fecha=date('Y-m-d');
            $cuenta->Fecha=$fecha;
            $cuenta->Detalle="Pago de de saldo de " . $saldo->cliente->Nombre;
            $cuenta->save();

            $this->imputarPago($request->cliente, $request->monto, $fecha, $saldo->id, $cuenta->id, $request->venta_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('saldos')->with('registrar', 'ok');
    }
}

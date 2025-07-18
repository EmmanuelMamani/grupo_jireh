<?php

namespace App\Http\Controllers;

use App\Http\Requests\pagoRequest;
use App\Http\Requests\saldoRequest;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Saldo;
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


    public function Pago(pagoRequest $request){
        $pasado=Saldo::all()->where("cliente_id",$request->cliente)->last()->Saldo;
        $saldo=new Saldo();
        $saldo->cliente_id=$request->cliente;
        $saldo->Monto=$request->monto;
        if ($request->monto > $pasado ) {
            return redirect()->back()->withErrors(['monto' => 'El monto ingresado excede el saldo actual del cliente.'])->withInput();
        }
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
        return redirect()->route('saldos')->with('registrar', 'ok');
    }
}

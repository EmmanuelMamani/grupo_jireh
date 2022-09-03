<?php

namespace App\Http\Controllers;

use App\Http\Requests\pagoRequest;
use App\Http\Requests\saldoRequest;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaldoController extends Controller
{
    public function vistaRegistro(){
        $clientes=Cliente::all();
        return view("saldo_pasado",["clientes"=> $clientes]);
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
    public function vistaPago(){
        $clientes=Cliente::all();
        return view("saldos",["clientes"=>$clientes]);
    }
    public function Pago(pagoRequest $request){
        $pasado=Saldo::all()->where("cliente_id",$request->cliente)->last()->Saldo;
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
        return redirect()->route('saldos')->with('registrar', 'ok');
    }
}

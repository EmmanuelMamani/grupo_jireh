<?php

namespace App\Http\Controllers;

use App\Http\Requests\saldoRequest;
use App\Models\Cliente;
use App\Models\Saldo;
use Illuminate\Http\Request;

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
}

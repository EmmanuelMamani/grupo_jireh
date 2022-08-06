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
        $saldo->Monto= $request->monto*-1;
        $saldo->Saldo= $request->monto*-1;
        $saldo->Detalle= $request->motivo;
        $saldo->cliente_id= $request->cliente;
        $saldo->save();
        return redirect()->route('saldo_pasado')->with('registrar', 'ok');
    }

    public function vistaReporte(){
        
    }
}
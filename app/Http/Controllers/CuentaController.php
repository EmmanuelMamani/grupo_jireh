<?php

namespace App\Http\Controllers;

use App\Http\Requests\cuentaRequest;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuentaController extends Controller
{
    //
    public function vistaRegistro(){
        return view("registro_gasto");
    }
    
    public function registro(cuentaRequest $request){
        $cuenta=new Cuenta();
        $usuario= Auth::user();
        $cuenta->Monto=$request->monto*-1;
        $cuenta->Detalle=$request->detalle;
        $cuenta->user_id=$usuario->id;
        $cuenta->save();
        return redirect()->route('registro_gasto')->with('registrar', 'ok');

    }

    public function vistaReporte(){
      $fecha=date("d-m-Y");
      echo($fecha);
    }
}

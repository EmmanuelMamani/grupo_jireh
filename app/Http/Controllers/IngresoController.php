<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\loteRule;
class IngresoController extends Controller
{
    public function vistaRegistro(){
        $productos= Producto::all();
        return view("registro_lote",["productos"=>$productos]);
    }
    
    public function registro(loteRule $request){
        $lote=new Ingreso();
        $lote->Proveedor= $request->proveedor;
        $lote->CantMoldes= $request->moldes;
        $lote->Peso= $request->peso;
        $lote->Precio= $request->costo;
        $lote->producto_id= $request->producto;
        $lote->save();
        return redirect()->route('registro_lote')->with('registrar', 'ok');

    }

    public function vistaReporte(){
        
    }
}

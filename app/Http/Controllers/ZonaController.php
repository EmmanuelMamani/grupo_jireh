<?php

namespace App\Http\Controllers;
use App\Models\Zona;
use App\Http\Requests\zonaRule;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function vistaRegistro(){
        return view('registro_zona');
    }
    
    public function registro(zonaRule $request){

        $zona=new Zona();
        $zona->Nombre=$request->Nombre;
        $zona->save();
        return redirect()->route('registro_zona')->with('registrar', 'ok');
    }

    public function vistaEliminar(){

    }
    
    public function eliminar(){

    }

    public function vistaReporte(){
        
    } 
}

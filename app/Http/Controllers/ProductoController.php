<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\productoRule;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function vistaRegistro(){
        return view('registro_producto');
    }
    
    public function registro(productoRule $request){
        $nombre=str_replace(" ","",$request->nombre);
        $resultado=DB::table('productos')->whereRaw("REPLACE(Nombre,' ','') LIKE '".$nombre."'")->where('Activo',0)->get();
        if($resultado->isEmpty()){
            $producto = new Producto();
            $producto->Nombre= $request->nombre;
            $producto->Tipo= $request->tipo;
            $producto->save();
        }else{
            $producto=DB::table('productos')->whereRaw("REPLACE(Nombre,' ','') LIKE '".$nombre."'")->update(['Nombre'=>$request->nombre,'Activo'=>1]);
           
        }
       
        return redirect()->route('registro_producto')->with('registrar', 'ok');
    }

    public function vistaEliminar(){

    }
    
    public function eliminar($id){
        $producto=Producto::find($id);
        $producto->Activo=0;
        $producto->save();
        return redirect()->route("reporte_producto")->with('eliminar','ok');
    }

    public function vistaReporte(){
        $productos= Producto::all()->where("Activo",1);
        return view("reporte_producto",["productos"=>$productos]);
    }
}

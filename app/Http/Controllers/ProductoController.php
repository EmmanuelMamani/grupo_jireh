<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\productoRule;
class ProductoController extends Controller
{
    public function vistaRegistro(){
        return view('registro_producto');
    }
    
    public function registro(productoRule $request){
        $producto = new Producto();
        $producto->Nombre= $request->nombre;
        $producto->Tipo= $request->tipo;
        $producto->save();
        return redirect()->route('registro_producto')->with('registrar', 'ok');
    }

    public function vistaEliminar(){

    }
    
    public function eliminar(){

    }

    public function vistaReporte(){
        
    }
}

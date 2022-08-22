<?php

namespace App\Http\Controllers;

use App\Http\Requests\listaRequest;
use App\Models\Cliente;
use App\Models\Lista;
use App\Models\Producto;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListaController extends Controller
{
    //
    public function VistaRegistro(){
        $zonas=Zona::all();
        $clientes=Cliente::all();
        $productos=Producto::all();
        return view("lista_pedidos",["zonas"=>$zonas,"clientes"=>$clientes,"productos"=>$productos]);
    }
    public function Registro(listaRequest $request){
        $lista=new Lista();
        $lista->cliente_id=$request->cliente;
        $lista->producto_id=$request->producto;
        $lista->user_id=Auth::user()->id;
        $lista->Unidades=$request->unidades;
        $lista->save();
        return redirect()->route('registro_lista')->with('registrar','ok');
    }
    public function reporte(){
        $listas=Lista::all()->where("user_id",Auth::user()->id);
        return view("lista_reporte",["listas"=>$listas]);
    }

    public function eliminar($id){
        $lista=Lista::find($id);
        $lista->delete();
        return redirect()->route('lista_reporte')->with('eliminar','ok');
    }
}

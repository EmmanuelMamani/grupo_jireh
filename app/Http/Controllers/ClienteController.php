<?php

namespace App\Http\Controllers;

use App\Http\Requests\clienteRequest;
use App\Models\Cliente;
use App\Models\Zona;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function vistaRegistro(){
        $zonas=Zona::all();
        return view("registro_cliente",["zonas" => $zonas]);
    }
    
    public function registro(clienteRequest $request){
        $cliente= new Cliente();
        $cliente->Nombre= $request->nombre;
        $cliente->Direccion= $request->direccion;
        $cliente->zona_id=$request->zona;
        $cliente->Telefono= $request->telefono;
        $cliente->save();
        return redirect()->route('registro_cliente')->with('registrar', 'ok');

    }

    public function vistaEliminar(){

    }
    
    public function eliminar($id){
        $cliente=Cliente::find($id);
        $cliente->Activo=0;
        $cliente->save();
        return redirect()->route('reporte_cliente')->with('eliminar', 'ok');
    }

    public function vistaReporte(){
        $clientes= Cliente::all()->where("Activo",1);
        return view("reporte_cliente",['clientes'=>$clientes]);
    }
}

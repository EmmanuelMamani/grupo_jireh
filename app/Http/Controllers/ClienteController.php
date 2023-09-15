<?php

namespace App\Http\Controllers;

use App\Http\Requests\clienteRequest;
use App\Models\Cliente;
use App\Models\Zona;
use Illuminate\Http\Request;
use App\Http\Requests\editar_clienteRequest;

class ClienteController extends Controller
{
    public function vistaRegistro(){
        $zonas=Zona::all();
        return view("registro_cliente",["zonas" => $zonas]);
    }
    
    public function registro(clienteRequest $request){
        $existe=Cliente::where('Telefono',$request->telefono)->get();
        if($existe->isEmpty()){
            $cliente= new Cliente();
            $cliente->Nombre= $request->nombre;
            $cliente->Direccion= $request->direccion;
            $cliente->zona_id=$request->zona;
            $cliente->Telefono= $request->telefono;
            $cliente->save();
        }else{
            $cliente=Cliente::firstwhere('Telefono',$request->telefono);
            $cliente->Nombre=$request->nombre;
            $cliente->Activo=true;
            $cliente->Direccion=$request->direccion;
            $cliente->zona_id=$request->zona;
            $cliente->save();
        }
       
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
        $total=0;
        foreach ($clientes as $cliente){
            if($cliente->saldos->isNotEmpty()){
                $total+= $cliente->saldos->last()->Saldo;
            }
        }
        return view("reporte_cliente",['clientes'=>$clientes, 'total' =>$total]);
    }

    public function vistaEditar($id){
        $cliente= Cliente::find($id);
        $zonas=Zona::all();
        return view('editar_cliente',['cliente'=>$cliente,'zonas'=>$zonas]);
    }
    public function editar(editar_clienteRequest $request, $id)
    {
        $cliente = Cliente::find($id);
        $cliente->Nombre = $request->nombre;
        $cliente->Direccion = $request->direccion;
        $cliente->zona_id = $request->zona;
    
        if ($request->mapa != null) {
            $cliente->direccion_map = $request->mapa;
        }
    
        if ($request->hasFile('tienda')) {
            $fi = $request->file('tienda');
            
            foreach ($fi as $fil) {
                $tipo_ext = $fil->getClientOriginalExtension();
                if (in_array($tipo_ext, ['jpeg', 'jpg', 'png', 'gif', 'svg'])) {
                    // Lee el contenido binario de la imagen
                    $imagenBinaria = file_get_contents($fil->getRealPath());
    
                    // Usar dd() para inspeccionar la imagen antes de guardarla
                    dd($imagenBinaria);
    
                    // Asigna la imagen binaria al campo Blob en la base de datos
                    $cliente->tienda = $imagenBinaria;
                }
            }
        }
    
        $cliente->save();
        //return redirect()->route('reporte_cliente')->with('editar', 'ok');
    }
    
}
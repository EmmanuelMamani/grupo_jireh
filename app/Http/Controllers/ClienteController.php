<?php

namespace App\Http\Controllers;

use App\Http\Requests\clienteRequest;
use App\Models\Cliente;
use App\Models\Zona;
use Illuminate\Http\Request;
use App\Http\Requests\editar_clienteRequest;
use Nette\Utils\Image;
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
        $clientes = Cliente::select([
            'id', 
            'Nombre', 
            'zona_id',
            'Telefono',
            'direccion_map',
            'Activo',
            \DB::raw('CASE WHEN tienda IS NOT NULL THEN "si" ELSE "no" END as getTienda')
        ])
        ->where('Activo', 1)
        ->get();
        $zonas=Zona::all();
        $zona_saldo = [];
        foreach($zonas as $zona){
            $zona_saldo[$zona->id]=0;
        }
        $total=0;
        foreach ($clientes as $cliente){
            if($cliente->saldos->isNotEmpty()){
                $total+= $cliente->saldos->last()->Saldo;
                $zona_saldo[$cliente->zona_id]+= $cliente->saldos->last()->Saldo;
            }
        }
        return view("reporte_cliente",['clientes'=>$clientes, 'total' =>$total,'zonas'=>$zonas,'zona_saldo'=>$zona_saldo]);
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
        $cliente->Telefono = $request->telefono;
        if ($request->mapa != null) {
            $cliente->direccion_map = $request->mapa;
        }
    
        if ($request->hasFile('tienda')) {
            $imagen = $request->file('tienda');
            $tipo_ext=$imagen->getClientOriginalExtension();
            if($tipo_ext == "jpeg" || $tipo_ext == "jpg" || $tipo_ext == "png" || $tipo_ext == "gif" || $tipo_ext == "svg"){
                $archivo=$imagen->getClientOriginalName();
                $file=Image::fromFile($imagen)->resize(300, null);
                $cliente->tienda=$file;
            }
        }
    
        $cliente->save();
        return redirect()->route('reporte_cliente')->with('editar', 'ok');
    }
    
    public function ver_tienda($id){
        $cliente = Cliente::find($id);
        return view('ver_tienda',['cliente'=>$cliente]);
    }
}
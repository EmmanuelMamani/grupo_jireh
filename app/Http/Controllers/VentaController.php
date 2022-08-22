<?php

namespace App\Http\Controllers;

use App\Http\Requests\devolucionRequest;
use App\Http\Requests\ventaRapidaRequest;
use App\Http\Requests\ventaRequest;
use App\Models\Asignacion;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Ingreso;
use App\Models\Producto;
use App\Models\Saldo;
use App\Models\Salida;
use App\Models\Venta;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Image;
use DateTime;
use DateTimeZone;

class VentaController extends Controller
{
    public function vistaRegistro(){
        $zonas=Zona::all();
        $clientes=Cliente::all();
        $productos=Producto::all();
        $asignaciones=Asignacion::where('asignado_id',Auth::user()->id)->where('CantMoldes','>',0)->get();
        return view("venta",['zonas'=>$zonas,'clientes'=>$clientes,'productos'=>$productos,'lotes'=>$asignaciones]);

    }
    
    public function registro(ventaRequest $request){

        $salida=new Salida();
        $salida->CantMoldes=$request->cantidad_moldes;
        $salida->Peso=$request->peso;
        $salida->Precio=$request->precio;
        $total=0;
        $tipo=Producto::all()->where('id',$request->producto)["1"]->Tipo;
        if($tipo=="Por kilo"){
            $total=$request->peso*$request->precio;
        }else{
            $total=$request->peso*$request->cantidad_moldes;
        }
        if($request->tipo==0){
            $salida->Total=round($total,2);
        }else{
            $salida->Total=round($total,0);
        }
        
        $salida->save();
       
        $venta=new Venta();
        $venta->cliente_id = $request->cliente; 
        $venta->user_id=Auth::user()->id;
        $venta->ingreso_id=$request->lote;
        $venta->salida_id=$salida->id;

        $venta->save();

        $fi=$request->file('comprobante');
        
        foreach ($fi as $fil) {
            $tipo=$fil->getClientOriginalExtension();
            if($tipo == "jpeg" || $tipo == "jpg" || $tipo == "png" || $tipo == "gif" || $tipo == "svg"){
                $archivo=$fil->getClientOriginalName();
                $file=Image::fromFile($fil)->resize(300, null);
    
                $prueba=new Comprobante();
                $prueba->venta_id= $venta->id;
                $prueba->Comprobante=$file;
                $prueba->save();
            }
            
        }

        $asignacion=Asignacion::where('asignado_id',Auth::user()->id)->where('ingreso_id',$request->lote)->get();
        
        $asignacion[0]->CantMoldes=$asignacion[0]->CantMoldes - $request->cantidad_moldes;
        $asignacion[0]->save();
        
        $saldo=new Saldo();
        $saldo->Monto=$total;
        $saldoActual=Saldo::all()->where('cliente_id',$request->cliente)->last();
       
        if($saldoActual==""){
            $saldo->Saldo=$total;
        }else{
            $saldo->Saldo=$saldoActual->Saldo + $total;
        }
        
        $saldo->Detalle="Pre-Venta";
        $saldo->cliente_id= $request->cliente;

        $saldo->save();

       return redirect()->route('venta')->with('registrar','ok');
    }
    public function vistaRegistroRapido(){
        $productos=Producto::all();
        $asignaciones=Asignacion::where('asignado_id',Auth::user()->id)->where('CantMoldes','>',0)->get();
        return view("venta_rapida",['productos'=>$productos,'lotes'=>$asignaciones]);
    }

    public function registroRapido(ventaRapidaRequest $request){
        
        $salida=new Salida();
        $salida->CantMoldes=$request->cantidad_moldes;
        $salida->Peso=$request->peso;
        $salida->Precio=$request->precio;
        $total=0;
        $tipo=Producto::all()->where('id',$request->producto)["1"]->Tipo;
        if($tipo=="Por Kilo"){
            $total=$request->peso*$request->precio;
        }else{
            $total=$request->peso*$request->cantidad_moldes;
        }
        if($request->centavos==0){
            $salida->Total=round($total,2);
        }else{
            $salida->Total=round($total,0);
        }

        $salida->save();
       
        $venta=new Venta();
        $venta->cliente_id = $request->cliente; 
        $venta->user_id=Auth::user()->id;
        $venta->ingreso_id=$request->lote;
        $venta->salida_id=$salida->id;

        $venta->save();
        
        $asignacion=Asignacion::where('asignado_id',Auth::user()->id)->where('ingreso_id',$request->lote)->get();
        
        $asignacion[0]->CantMoldes=$asignacion[0]->CantMoldes - $request->cantidad_moldes;
        $asignacion[0]->save();

        return redirect()->route('venta_rapida')->with('registrar','ok');
    }
    
    public function vistaReporte(){
        $ventas=Venta::all();
        return view("reporte_ventas",["ventas"=>$ventas]);
    }
    public function detalle($id){
        $venta=Venta::find($id);
        $comprobantes=Comprobante::where("venta_id",$id)->get();
        return view("detalle_venta",["venta"=>$venta,"comprobantes"=>$comprobantes]);
    }
    public function VistaDevolucion($id){
        $venta=Venta::find($id);
        return view("devolucion",["venta"=>$venta]);
    }
    public function Devolucion(Request $request, $id){
        $venta=Venta::find($id);
        $salida=$venta->salida;
        $request->validate([
            "unidades"=>"required|integer|lte:". $salida->CantMoldes,
            "monto"=>"required|numeric"
        ]);
        $cuenta=$venta->cliente->saldos->last();
        $salida->Total=$salida->Total - $request->monto;
        $salida->CantMoldes= $salida->CantMoldes - $request->unidades;
        $saldo= new Saldo();
        $saldo->Monto= $request->monto;
        $saldo->Saldo= $cuenta->Saldo - $request->monto;
        $saldo->Detalle="Devolucion";
        $saldo->cliente_id=$venta->cliente_id;/*
        $saldo->save();
        $salida->save();
        $lote=Asignacion::all()->where("ingreso_id",$venta->ingreso_id)->where("asignado_id",$venta->user_id)->last();
        $lote->CantMoldes=$lote->CantMoldes + $request->unidades;
        $lote->save();
        return redirect()->route('reporte_ventas')->with('registrar','ok');*/
    }
}

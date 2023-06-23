<?php

namespace App\Http\Controllers;

use App\Http\Requests\devolucionRequest;
use App\Http\Requests\requestEditVenta;
use App\Http\Requests\ventaRapidaRequest;
use App\Http\Requests\ventaRequest;
use App\Models\Asignacion;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Cuenta;
use App\Models\Ingreso;
use App\Models\Lista;
use App\Models\Merma;
use App\Models\Producto;
use App\Models\Saldo;
use App\Models\Salida;
use App\Models\Venta;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Image;
use PDF;


class VentaController extends Controller
{
   

    public function vistaRegistro(){
        $zonas=Zona::all();
        $clientes=Cliente::all()->where("Activo",1);
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
        $tipo=Producto::all()->where('id',$request->producto)->last()->Tipo;
        
        
        if($tipo=="Por Kilo"){
            $total=$request->peso*$request->precio;
        }else{
            $total=$request->precio*$request->cantidad_moldes;
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
            $tipo_ext=$fil->getClientOriginalExtension();
            if($tipo_ext == "jpeg" || $tipo_ext == "jpg" || $tipo_ext == "png" || $tipo_ext == "gif" || $tipo_ext == "svg"){
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
        
        if($request->contado==0){
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
          }else{
            $cuenta=new Cuenta();
            $cuenta->user_id= Auth::user()->id;
            $cuenta->Monto=$salida->Total;
            $cuenta->Detalle="Venta al contado";
            $cuenta->Fecha=date("Y-m-d");
            $cuenta->save();
          }
        
        $lote=Ingreso::firstWhere('id',$request->lote);
       
        if($tipo=="Por Kilo"){
            $cantidad_saliente=$lote->salidas->sum('CantMoldes');
        
           
            if(($cantidad_saliente-$lote->CantMoldes)==0){
                
                $merma=new Merma();
                $merma->ingreso_id=$request->lote;
                $merma->CantMerma=$lote->Peso-$lote->salidas->sum("Peso");
                $merma->save();
            }
        }
        
      return redirect()->route('menu')->with('registrar','ok');
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
        $tipo=Producto::all()->where('id',$request->producto)->last()->Tipo;
        if($tipo=="Por Kilo"){
            $total=$request->peso*$request->precio;
        }else{
            $total=$request->precio*$request->cantidad_moldes;
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
        $venta->Estado=1;
        $venta->save();

        $fi=$request->file('comprobante');
        
        foreach ($fi as $fil) {
            $tipo_ext=$fil->getClientOriginalExtension();
            if($tipo_ext == "jpeg" || $tipo_ext == "jpg" || $tipo_ext == "png" || $tipo_ext == "gif" || $tipo_ext == "svg"){
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

        $cuenta=new Cuenta();
        $cuenta->user_id= Auth::user()->id;
        $cuenta->Monto=$salida->Total;
        $cuenta->Detalle="Venta rápida";
        $cuenta->Fecha=date("Y-m-d");
        $cuenta->save();

        $lote=Ingreso::firstWhere('id',$request->lote);
        if($tipo=="Por Kilo"){
            $cantidad_saliente=$lote->salidas->sum('CantMoldes');
        
           
            if(($cantidad_saliente-$lote->CantMoldes)==0){
                
                $merma=new Merma();
                $merma->ingreso_id=$request->lote;
                $merma->CantMerma=$lote->Peso-$lote->salidas->sum("Peso");
                $merma->save();
            }
        }
        return redirect()->route('menu')->with('registrar','ok');
    }
    
    public function vistaReporte(){
       // $ventas=Venta::orderByDesc("id")->get();
        $lotes=Ingreso::orderBy('id','desc')->where("Activo",1)->get();
        return view("reporte_ventas",["lotes"=>$lotes]);
     //return  $pdf->download('archivo.pdf');
    }

    public function vistaReporteVentas($id){
        $ventas=Venta::where('ingreso_id',$id)->orderByDesc('id')->get();
        return view('reporte_lote_ventas',["ventas"=>$ventas,'id'=>$id]);
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
        $salida->Total=$salida->Total - $request->monto;
        $salida->CantMoldes= $salida->CantMoldes - $request->unidades;
        if($venta->cliente_id != NULL){
            $cuenta=$venta->cliente->saldos->last();
            $saldo= new Saldo();
            $saldo->Monto= $request->monto;
            $saldo->Saldo= $cuenta->Saldo - $request->monto;
            $saldo->Detalle="Devolucion";
            $saldo->cliente_id=$venta->cliente_id;
            $saldo->save();
        }else{
            $cuenta1 =new Cuenta();
            $cuenta1->user_id= Auth::user()->id;
            $cuenta1->Monto=$request->monto*(-1);
            $cuenta1->Detalle="Ajuste de venta rapida";
            $cuenta1->Fecha=date("Y-m-d");
            $cuenta1->save();
        }
        $salida->save();
        $lote=Asignacion::all()->where("ingreso_id",$venta->ingreso_id)->where("asignado_id",$venta->user_id)->last();
        $lote->CantMoldes=$lote->CantMoldes + $request->unidades;
        $lote->save();
        return redirect()->route('reporte_ventas')->with('registrar','ok');
    }
    public function descarga($id)
    {
        $ventas=Venta::where('ingreso_id',$id)->orderByDesc('id')->get();
       // $ventas=Venta::orderByDesc("id")->get();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_ventas_pdf",compact('ventas'));
        return  $pdf->download('reporteVentas.pdf');
    }
    public function venta_completa(ventaRequest $request, $id){
      
        $lista=Lista::find($id);
        $lista->delete();
        $salida=new Salida();
        $salida->CantMoldes=$request->cantidad_moldes;
        $salida->Peso=$request->peso;
        $salida->Precio=$request->precio;
        $total=0;
        $tipo=Producto::all()->where('id',$request->producto)->last()->Tipo;
        if($tipo=="Por Kilo"){
            $total=$request->peso*$request->precio;
        }else{
            $total=$request->precio*$request->cantidad_moldes;
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
            $tipo_ext=$fil->getClientOriginalExtension();
            if($tipo_ext == "jpeg" || $tipo_ext == "jpg" || $tipo_ext == "png" || $tipo_ext == "gif" || $tipo_ext == "svg"){
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
        
        if($request->contado==0){
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
          }else{
            $cuenta=new Cuenta();
            $cuenta->user_id= Auth::user()->id;
            $cuenta->Monto=$salida->Total;
            $cuenta->Detalle="Venta al contado";
            $cuenta->Fecha=date("Y-m-d");
            $cuenta->save();
          }

        $lote=Ingreso::firstWhere('id',$request->lote);
       
        if($tipo=="Por Kilo"){
            $cantidad_saliente=$lote->salidas->sum('CantMoldes');
        
           
            if(($cantidad_saliente-$lote->CantMoldes)==0){
                
                $merma=new Merma();
                $merma->ingreso_id=$request->lote;
                $merma->CantMerma=$lote->Peso-$lote->salidas->sum("Peso");
                $merma->save();
            }
        }
        
      return redirect()->route('menu')->with('registrar','ok');
    }
    public function vistaEditar($id){
        $venta=Venta::find($id);
        $comprobantes=Comprobante::where("venta_id",$id)->get();
        return view("editar_venta",["venta"=>$venta,"comprobantes"=>$comprobantes]);
    }
    public function Editar(requestEditVenta $request, $id){
        $salida=Salida::find($id);
        $precio_ant=$salida->Precio;
        $total_ant=0;
        $salida->Precio=$request->precio;
        $total=0;
        $tipo=Producto::all()->where('id',$request->producto)->last()->Tipo;
        if($tipo=="Por Kilo"){
            $total=$request->peso*$request->precio;
            $total_ant=$request->peso* $precio_ant;
        }else{
            $total=$request->precio*$request->cantidad_moldes;
            $total_ant= $request->cantidad_moldes* $precio_ant;
        }
        if($request->tipo==0){
            $total=round($total,2);
        }else{
            $total=round($total,0);
        }

        //$salida->save();
        $venta=Venta::find($id);
        if($venta->cliente_id != NULL){
            $saldo_ant=Saldo::all()->where("cliente_id",$venta->cliente_id)->last();
            $nuevo_saldo= new Saldo();
            $nuevo_saldo->Saldo= $saldo_ant->Saldo + $total - $salida->Total;
            $nuevo_saldo->Monto=  $total - $salida->Total;
            $nuevo_saldo->Detalle = "Ajuste de venta";
            $nuevo_saldo->cliente_id= $venta->cliente_id;
            $nuevo_saldo->save();
        }else{
            $cuenta =new Cuenta();
            $cuenta->user_id= Auth::user()->id;
            $cuenta->Monto=$total- $total_ant;
            $cuenta->Detalle="Ajuste de venta rapida";
            $cuenta->Fecha=date("Y-m-d");
            $cuenta->save();
        }
        $salida->Total =$total;
        $salida->save();
        return redirect()->route('reporte_ventas')->with('registrar','ok');
    }
}
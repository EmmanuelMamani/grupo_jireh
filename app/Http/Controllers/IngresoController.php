<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\loteRule;
use App\Models\Asignacion;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use PDF;

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
        $lotes=Ingreso::all()->last();
        $asignacion= new Asignacion();
        $asignacion->cantMoldes=$request->moldes;
        $asignacion->ingreso_id=$lotes->id;
        $asignacion->asignado_id=Auth::user()->id;
        $asignacion->asignador_id=Auth::user()->id;
        $asignacion->save();
        return redirect()->route('registro_lote')->with('registrar', 'ok');
    }

    public function vistaReporte(){
        $lotes=Ingreso::orderBy('id','desc')->where("Activo",1)->get();
       return view("reporte_lote",['lotes'=>$lotes]);
    }

    public function Eliminar($id){
        $lote=Ingreso::find($id);
        $lote->Activo=0;
        $lote->save();
        return redirect()->route('reporte_lotes')->with('eliminar', 'ok');
    }
    
    public function Pagar($id){
        $lote=Ingreso::find($id);
        $lote->Pagado=1;
        $lote->save();
        return redirect()->route('reporte_lotes')->with('registrar', 'ok');

    }
    public function descarga(){
        $lotes=Ingreso::orderBy('id','desc')->get();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_lote_pdf",compact('lotes'));
        return  $pdf->download('reporteLotes.pdf');

    }
}

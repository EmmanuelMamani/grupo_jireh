<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\loteRule;
use App\Models\Asignacion;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use App\Models\Salida;
use App\Models\Merma;
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
        $lotes = Ingreso::orderBy('id', 'desc')
                ->where("Activo", 1)
                ->limit(50)
                ->get();
       return view("reporte_lote",['lotes'=>$lotes]);
    }
    public function vistaReporteTotal(){
        $lotes = Ingreso::orderBy('id', 'desc')
                ->where("Activo", 1)
                ->get();
       return view("reporte_lote",['lotes'=>$lotes]);
    }
    public function Eliminar($id){
        $lote=Ingreso::find($id);
        $lote->Activo=0;
        $lote->save();
        $asignaciones = $lote->asignaciones;
        foreach($asignaciones as $asignacion){
            $asignacion->delete();
        }
        return redirect()->route('reporte_lotes')->with('eliminar', 'ok');
    }
    
    public function Pagar($id){
        $lote=Ingreso::find($id);
        $lote->Pagado=1;
        $lote->save();
        return redirect()->route('reporte_lotes')->with('registrar', 'ok');

    }
    public function descarga(){
        $lotes=Ingreso::orderBy('id','desc')->where("Activo",1)->get();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_lote_pdf",compact('lotes'));
        return  $pdf->download('reporteLotes.pdf');

    }
    public function vistaEditar($id){
        $lote=Ingreso::find($id);
        return view("editar_lote",["lote"=>$lote]);
    }
    public function Editar(loteRule $request , $id){
        $lote=Ingreso::find($id);
        $diferencia= $request->moldes - $lote->CantMoldes;
        $lote->Precio=$request->costo;
        $lote->CantMoldes=$request->moldes;
        $lote->Peso=$request->peso;
        $lote->save();
        $asignacion = Asignacion::all()->where('ingreso_id',$id)->where('asignado_id',Auth::user()->id)->last();
        $asignacion->CantMoldes += $diferencia;
        $asignacion->save();
        $unidades_vendidas=0;
        foreach($lote->ventas as $venta){
            $unidades_vendidas+= $venta->salida->CantMoldes;
        }

        if($unidades_vendidas == $lote->CantMoldes){
            $merma= Merma::find($lote->merma->id);
            $merma->CantMerma=$lote->Peso-$lote->salidas->sum("Peso");
            $merma->save();
        }
        return redirect()->route('reporte_lotes')->with('registrar', 'ok');
    }
}

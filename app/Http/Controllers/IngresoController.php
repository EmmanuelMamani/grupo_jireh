<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\loteRule;
use App\Models\Asignacion;
use App\Models\Cuenta;
use App\Models\PagoProveedor;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use App\Models\Salida;
use App\Models\Merma;
use Illuminate\Support\Facades\DB;
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
                ->with('producto', 'salidas')
                ->limit(50)
                ->get();
        $this->enriquecerLotes($lotes);
        $kpis = $this->kpisLotes($lotes);
        $productos = Producto::all();
       return view("reporte_lote",['lotes'=>$lotes,'kpis'=>$kpis,'productos'=>$productos,'alcance'=>'50 últimos']);
    }
    public function vistaReporteTotal(){
        $lotes = Ingreso::orderBy('id', 'desc')
                ->where("Activo", 1)
                ->with('producto', 'salidas')
                ->get();
        $this->enriquecerLotes($lotes);
        $kpis = $this->kpisLotes($lotes);
        $productos = Producto::all();
        return view("reporte_lote",['lotes'=>$lotes,'kpis'=>$kpis,'productos'=>$productos,'alcance'=>'todos']);
    }

    protected function enriquecerLotes($lotes){
        foreach ($lotes as $lote) {
            $esKilo = $lote->producto && $lote->producto->Tipo == 'Por Kilo';
            $costo = $esKilo ? $lote->Precio * $lote->Peso : $lote->Precio * $lote->CantMoldes;
            $vendido = (float) $lote->salidas->sum('Total');
            $vendidas = (int) $lote->salidas->sum('CantMoldes');
            $stock = $lote->CantMoldes - $vendidas;
            $lote->setAttribute('costo_total', round($costo, 2));
            $lote->setAttribute('vendido_total', round($vendido, 2));
            $lote->setAttribute('ganancia', round($vendido - $costo, 2));
            $lote->setAttribute('vendidas', $vendidas);
            $lote->setAttribute('stock_restante', $stock);
            $lote->setAttribute('pct_vendido', $lote->CantMoldes > 0 ? round($vendidas / $lote->CantMoldes * 100, 1) : 0);
            $merma = (!$esKilo || $stock > 0) ? 0 : round($lote->Peso - (float) $lote->salidas->sum('Peso'), 2);
            $lote->setAttribute('merma_kg', $merma);
        }
    }

    protected function kpisLotes($lotes){
        return [
            'inversion' => round($lotes->sum('costo_total'), 2),
            'vendido' => round($lotes->sum('vendido_total'), 2),
            'ganancia' => round($lotes->sum('ganancia'), 2),
            'stock' => (int) $lotes->sum('stock_restante'),
            'activos' => $lotes->count(),
            'pagados' => $lotes->where('Pagado', 1)->count(),
        ];
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
    
    public function Pagar(Request $request, $id){
        $lote=Ingreso::findOrFail($id);
        $tipo = ($lote->producto && $lote->producto->Tipo == 'Por Kilo') ? 'Por Kilo' : 'Por Unidad';
        $costoTotal = $tipo == 'Por Kilo'
            ? $lote->Precio * $lote->Peso
            : $lote->Precio * $lote->CantMoldes;
        $pagadoPrevio = PagoProveedor::where('ingreso_id', $id)->sum('monto');
        $pendiente = round($costoTotal - $pagadoPrevio, 2);
        if ($pendiente <= 0) {
            $lote->Pagado = 1;
            $lote->save();
            return redirect()->route('reporte_lotes')->withErrors(['pago' => 'El lote ya está pagado en su totalidad.']);
        }
        $request->validate([
            'monto' => 'nullable|numeric|gt:0|lte:' . $pendiente,
            'fecha' => 'nullable|date',
        ], [
            'monto.lte' => 'El monto no puede ser mayor al pendiente de Bs ' . number_format($pendiente, 2),
        ]);
        $monto = $request->monto ?? $pendiente;
        $fecha = $request->fecha ?? date('Y-m-d');
        DB::beginTransaction();
        try {
            $cuenta = new Cuenta();
            $cuenta->user_id = Auth::user()->id;
            $cuenta->Monto = $monto * -1;
            $cuenta->Detalle = "Pago a proveedor " . $lote->Proveedor . " lote #" . $lote->id;
            $cuenta->Fecha = $fecha;
            $cuenta->save();
            $pagoProv = new PagoProveedor();
            $pagoProv->ingreso_id = $lote->id;
            $pagoProv->monto = $monto;
            $pagoProv->fecha = $fecha;
            $pagoProv->cuenta_id = $cuenta->id;
            $pagoProv->user_id = Auth::user()->id;
            $pagoProv->save();
            if (round($pendiente - $monto, 2) <= 0) {
                $lote->Pagado = 1;
                $lote->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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

<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Saldo;
use App\Models\Venta;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Echo_;

class PendienteController extends Controller
{
    public function reporte(){
        $ventas=Venta::all()->where("Estado",0);
        return view("ventas_pendientes",["ventas"=>$ventas]);
    }
    public function modificar($id, $tipo){
        $venta=Venta::find($id);
        if($tipo==1){
            $venta->Estado=1;
            $venta->save();

        }
        if($tipo==2){
           $consulta=Asignacion::all()->where("id",$venta->ingreso_id)->where("asignado_id",$venta->user_id)->last();
           $consulta->CantMoldes=$consulta->CantMoldes+$venta->salida->CantMoldes;
           $consulta->save();
           $saldo=new Saldo();
           $saldo->cliente_id=$venta->cliente_id;
           $saldo->Monto=$venta->salida->Total;
           $saldo->Detalle="Venta cancelada";
           $antiguo=0;
           if(Saldo::all()->where("cliente_id",$venta->cliente_id)->isNotEmpty()){
            $antiguo=Saldo::all()->where("cliente_id",$venta->cliente_id)->last()->Saldo;
           }
           $saldo->Saldo=$antiguo - $saldo->Monto;
           $saldo->save();
           $venta->salida->delete();
           $venta->delete();
        }
        return redirect()->route('ventas_pendientes')->with('registrar', 'ok');
    }
}

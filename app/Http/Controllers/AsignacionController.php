<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsignacionController extends Controller
{
    //
    public function vistaRegistro(){
        $user=Auth::user();
        $usuarios=User::all()->where("id","!=",$user->id);
        $asignaciones=Asignacion::all()->where("asignado_id","=",$user->id)->where("CantMoldes",">",0);
        return view("transferir_lote",["usuarios"=>$usuarios, "asignaciones" => $asignaciones, "user"=>$user]);
    }
    
    public function registro(Request $request){
            $antiguo=Asignacion::find($request->lote);
            $antiguo->CantMoldes=$antiguo->CantMoldes - $request->cantidad_moldes;
           $antiguo->save();
            $asignacion=Asignacion::all()->where("asignado_id","=",$request->receptor)->where("ingreso_id","=",$antiguo->ingreso_id)->last();
            if($asignacion==""){
                $asignacion=new Asignacion();
            }
            $asignacion->asignado_id=$request->receptor;
            $asignacion->asignador_id=Auth::user()->id;
            $asignacion->CantMoldes=$request->cantidad_moldes + $asignacion->CantMoldes;
            $asignacion->ingreso_id=$antiguo->ingreso_id;
            $asignacion->save();
            return redirect()->route('transferir_lote')->with('registrar', 'ok');
    }

    public function vistaReporte(){
        
    }

}

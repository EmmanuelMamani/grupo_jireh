<?php

namespace App\Http\Controllers;
use App\Models\Zona;
use App\Http\Requests\zonaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZonaController extends Controller
{
    public function vistaRegistro(){
        return view('registro_zona');
    }
    
    public function registro(zonaRule $request){

        $nombre=str_replace(" ","",$request->Nombre);
        $resultado=DB::table('zonas')->whereRaw("REPLACE(Nombre,' ','') LIKE '".$nombre."'")->where('Activo',0)->get();
        if($resultado->isEmpty()){
            $zona=new Zona();
            $zona->Nombre=$request->Nombre;
            $zona->save();
        }else{
            $zona=DB::table('zonas')->whereRaw("REPLACE(Nombre,' ','') LIKE '".$nombre."'")->update(['Nombre'=>$request->Nombre,'Activo'=>1]);
           
        }
        
        return redirect()->route('registro_zona')->with('registrar', 'ok');
    }

    public function vistaEliminar(){
       
    }
    
    public function eliminar($id){
        $zona=Zona::find($id);
        $zona->Activo=0;
        $zona->save();
        return redirect()->route("reporte_zona")->with('eliminar','ok');
    }

    public function vistaReporte(){
        $zonas=Zona::all()->where('Activo',1);
        return view('reporte_zona',['zonas'=>$zonas]);
    } 
}

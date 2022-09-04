<?php

namespace App\Http\Controllers;

use App\Http\Requests\cuentaRequest;
use App\Http\Requests\periodoRequest;
use App\Models\Cuenta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
class CuentaController extends Controller
{
    //
    public function vistaRegistro(){
        return view("registro_gasto");
    }
    
    public function registro(cuentaRequest $request){
        $cuenta=new Cuenta();
        $usuario= Auth::user();
        $constante=-1;
        if($request->cuenta==1){
            $constante=1;
        }
        $cuenta->Monto=$request->monto * $constante;
        $cuenta->Detalle=$request->detalle;
        $cuenta->user_id=$usuario->id;
        $fecha=date('Y-m-d');
        $cuenta->Fecha=$fecha;
        $cuenta->save();
        return redirect()->route('registro_gasto')->with('registrar', 'ok');
        
    }

    public function vistaReporte(){
        $fecha=date('Y-m-d');
        $titulo="Diario total";
        $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
        $cuentas=[];
        foreach($consultas as $c){
            if($c->Fecha==$fecha){
                array_push($cuentas,$c);
           }
        }
        $usuarios=User::all();
        return view("reporte_cuenta",["cuentas"=>$cuentas,"usuarios"=>$usuarios,"titulo"=>$titulo]);
    }

    public function reporteDiario(){
        $fecha=date('Y-m-d');
        $titulo="Diario";
        $cuentas=Cuenta::where('user_id',Auth::user()->id)->where("Fecha",$fecha)->get();
        return view("detalle_cuenta",["cuentas"=>$cuentas,"titulo"=>$titulo]);
    }

    public function DetalleCuenta($id,$fecha){
        $cuentas=Cuenta::all()->where("user_id",$id)->where("Fecha",$fecha);
     return view("detalle_cuenta",["cuentas"=>$cuentas]);
    }
    public function VistaPeriodo(){
        return view("cuentas_periodo");
    }
    public function ReportePeriodo(periodoRequest $request){
       $inicio=$request->inicio;
        $fin=$request->fin;
        $titulo="Periodo";
        $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
        $cuentas=[];
        $monto=0;
        foreach($consultas as $c){
            if($c->Fecha>=$inicio && $c->Fecha<=$fin){
                array_push($cuentas,$c);
                $monto+=$c->monto;
           }
        }
        $usuarios=User::all();
        return view("reporte_periodo",["cuentas"=>$cuentas,"usuarios"=>$usuarios,"monto"=>$monto,'inicio'=>$inicio,'fin'=>$fin,"titulo"=>$titulo]);
    }
    public function reporteHistorico(){
        $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
        $cuentas=[];
        $titulo="Historico";
        foreach($consultas as $c){
                array_push($cuentas,$c);
        }
        $usuarios=User::all();
        return view("reporte_cuenta",["cuentas"=>$cuentas,"usuarios"=>$usuarios,"titulo"=>$titulo]);
    }
    public function descarga(){
        $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
        $cuentas=[];
        foreach($consultas as $c){
                array_push($cuentas,$c);
        }
        $usuarios=User::all();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_cuentas_pdf",['cuentas'=>$cuentas,'usuarios'=>$usuarios]);
        return  $pdf->download('reporteCuentas.pdf'); 
    }
    public function descarga_periodo($inicio,$fin){
        $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
        $cuentas=[];
        $monto=0;
        foreach($consultas as $c){
            if($c->Fecha>=$inicio && $c->Fecha<=$fin){
                array_push($cuentas,$c);
                $monto+=$c->monto;
           }
        }
        $usuarios=User::all();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_periodo_pdf",["cuentas"=>$cuentas,"usuarios"=>$usuarios,"monto"=>$monto,'inicio'=>$inicio,'fin'=>$fin]);
        return  $pdf->download('reportePeriodo.pdf'); 
    }
}

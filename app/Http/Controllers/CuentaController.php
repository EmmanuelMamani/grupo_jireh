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
    public function estadisticas(){
        $results = DB::select("SELECT 
            meses.año,
            meses.mes,
            COALESCE(almuerzo.gasto_mensual, 0) AS almuerzo,
            COALESCE(desayuno.gasto_mensual, 0) AS desayuno,
            COALESCE(gasolina.gasto_mensual, 0) AS gasolina,
            COALESCE(diesel.gasto_mensual, 0) AS diesel,
            COALESCE(transporte.gasto_mensual, 0) AS transporte,
            COALESCE(cambio_aceite.gasto_mensual, 0) AS aceite
        FROM 
            (SELECT DISTINCT YEAR(Fecha) AS año, MONTH(Fecha) AS mes FROM cuentas) AS meses
        LEFT JOIN
            (SELECT 
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE '%almuerzo%'
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS almuerzo
        ON meses.año = almuerzo.año AND meses.mes = almuerzo.mes
        LEFT JOIN
            (SELECT 
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE '%desayuno%'
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS desayuno
        ON meses.año = desayuno.año AND meses.mes = desayuno.mes
        LEFT JOIN
            (SELECT 
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE '%gasolina%'
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS gasolina
        ON meses.año = gasolina.año AND meses.mes = gasolina.mes
        LEFT JOIN
            (SELECT 
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE '%diesel%'
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS diesel
        ON meses.año = diesel.año AND meses.mes = diesel.mes
        LEFT JOIN
            (SELECT 
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE '%transporte%'
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS transporte
        ON meses.año = transporte.año AND meses.mes = transporte.mes
        LEFT JOIN
            (SELECT 
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE '%cambio aceite%'
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS cambio_aceite
        ON meses.año = cambio_aceite.año AND meses.mes = cambio_aceite.mes");
        return view("estadisticas_cuentas",['results'=>$results]);
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
        $user=User::find(Auth::user()->id);
        return view("detalle_cuenta",["cuentas"=>$cuentas,"titulo"=>$titulo,"user"=>$user]);
    }

    public function DetalleCuenta($id,$fecha){
        $cuentas=Cuenta::all()->where("user_id",$id)->where("Fecha",$fecha);
        $user=User::find($id);
     return view("detalle_cuenta",["cuentas"=>$cuentas,"user"=>$user]);
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
    public function descarga_diario($user_id){
        $fecha=date('Y-m-d');
        $cuentas=Cuenta::where('user_id',$user_id)->where("Fecha",$fecha)->get();
        $usuarios=User::where('id',$user_id)->get();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_cuentas_diario_pdf",['cuentas'=>$cuentas,'usuarios'=>$usuarios]);
        return  $pdf->download('reporte_diario.pdf');
    }
    public function descarga_cuentas_diarias(){
        $fecha=date('Y-m-d');
        $cuentas=Cuenta::where("Fecha",$fecha)->get();
        $usuarios=User::all();
        $pdf = PDF::setOptions(['dpi' => 96])->loadView("reporte_cuentas_diario_pdf",['cuentas'=>$cuentas,'usuarios'=>$usuarios]);
        return  $pdf->download('reporte_diario.pdf');
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

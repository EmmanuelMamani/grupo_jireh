<?php

namespace App\Http\Controllers;

use App\Http\Requests\cuentaRequest;
use App\Http\Requests\periodoRequest;
use App\Models\Cuenta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CuentaController extends Controller
{
    //
    public function vistaRegistro(){
        return view("registro_gasto");
    }
    
    public function registro(cuentaRequest $request){
        $cuenta=new Cuenta();
        $usuario= Auth::user();
        $cuenta->Monto=$request->monto*-1;
        $cuenta->Detalle=$request->detalle;
        $cuenta->user_id=$usuario->id;
        $cuenta->save();
        return redirect()->route('registro_gasto')->with('registrar', 'ok');

    }

    public function vistaReporte(){
      $fecha=date("Y-m-d");
      
      $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
      $cuentas=[];
        foreach($consultas as $c){
            if($c->Fecha==$fecha){
                array_push($cuentas,$c);
           }
        }
        $usuarios=User::all();
        return view("reporte_cuenta",["cuentas"=>$cuentas,"usuarios"=>$usuarios]);
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
       // echo($inicio ."<br>". $fin);
       /*$fecha=date("Y-m-d H:i:s");
       $fecha=strtotime("-4 hour",strtotime($fecha));
       $fecha=date ( 'Y-m-d H:i:s' , $fecha);
       echo($fecha);*/
    }
}

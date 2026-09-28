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
    public function estadisticas(Request $request){
        $categoriasDisponibles = [
            'almuerzo' => 'almuerzo',
            'desayuno' => 'desayuno',
            'gasolina' => 'gasolina',
            'diesel' => 'diesel',
            'transporte' => 'transporte',
            'aceite' => 'cambio aceite',
        ];

        $validated = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
            'categorias' => 'nullable|array',
            'categorias.*' => 'in:almuerzo,desayuno,gasolina,diesel,transporte,aceite',
        ], [
            'hasta.date' => 'La fecha hasta no es válida',
            'desde.date' => 'La fecha desde no es válida',
        ]);

        $desde = $validated['desde'] ?? null;
        $hasta = $validated['hasta'] ?? null;
        $seleccionadas = $validated['categorias'] ?? array_keys($categoriasDisponibles);
        if (empty($seleccionadas)) {
            $seleccionadas = array_keys($categoriasDisponibles);
        }

        if ($desde && $hasta && $desde > $hasta) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hasta' => 'La fecha hasta debe ser posterior o igual a la fecha desde.',
            ]);
        }

        $whereFecha = '';
        $dateBindings = [];
        if ($desde && $hasta) {
            $whereFecha = 'Fecha BETWEEN ? AND ?';
            $dateBindings = [$desde, $hasta];
        } elseif ($desde) {
            $whereFecha = 'Fecha >= ?';
            $dateBindings = [$desde];
        } elseif ($hasta) {
            $whereFecha = 'Fecha <= ?';
            $dateBindings = [$hasta];
        }

        $bindings = $dateBindings;
        $baseFiltro = $whereFecha ? " WHERE {$whereFecha}" : '';
        $selects = ['meses.año', 'meses.mes'];
        $joins = '';
        foreach ($seleccionadas as $key) {
            $keyword = $categoriasDisponibles[$key];
            $filtroFecha = $whereFecha ? " AND {$whereFecha}" : '';
            $joins .= " LEFT JOIN
            (SELECT
                YEAR(Fecha) AS año,
                MONTH(Fecha) AS mes,
                SUM(ABS(Monto)) AS gasto_mensual
            FROM cuentas
            WHERE LOWER(detalle) LIKE ?{$filtroFecha}
            GROUP BY YEAR(Fecha), MONTH(Fecha)) AS {$key}
        ON meses.año = {$key}.año AND meses.mes = {$key}.mes";
            $bindings[] = '%' . strtolower($keyword) . '%';
            foreach ($dateBindings as $b) {
                $bindings[] = $b;
            }
            $selects[] = "COALESCE({$key}.gasto_mensual, 0) AS {$key}";
        }

        $results = DB::select("SELECT " . implode(', ', $selects) . "
        FROM
            (SELECT DISTINCT YEAR(Fecha) AS año, MONTH(Fecha) AS mes FROM cuentas{$baseFiltro}) AS meses
        {$joins}
        ORDER BY meses.año, meses.mes", $bindings);

        $totales = [];
        foreach ($seleccionadas as $key) {
            $totales[$key] = 0;
        }
        foreach ($results as $row) {
            foreach ($seleccionadas as $key) {
                $totales[$key] += (float) $row->$key;
            }
        }
        $totalGeneral = array_sum($totales);

        $mesesConDatos = count($results);
        $categoriaTop = null;
        $montoTop = 0;
        foreach ($totales as $key => $monto) {
            if ($monto > $montoTop) {
                $montoTop = $monto;
                $categoriaTop = $key;
            }
        }
        $kpis = [
            'total' => $totalGeneral,
            'categoriaTop' => $categoriaTop ? $categoriasDisponibles[$categoriaTop] : null,
            'montoTop' => $montoTop,
            'meses' => $mesesConDatos,
            'promedio' => $mesesConDatos > 0 ? $totalGeneral / $mesesConDatos : 0,
        ];

        return view("estadisticas_cuentas", [
            'results' => $results,
            'categoriasDisponibles' => $categoriasDisponibles,
            'seleccionadas' => $seleccionadas,
            'desde' => $desde,
            'hasta' => $hasta,
            'totales' => $totales,
            'totalGeneral' => $totalGeneral,
            'kpis' => $kpis,
        ]);
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
        $usuarios=User::all()->keyBy('id');
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
        $cuentas=Cuenta::where("user_id",$id)->where("Fecha",$fecha)->get();
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
        $usuarios=User::all()->keyBy('id');
        return view("reporte_periodo",["cuentas"=>$cuentas,"usuarios"=>$usuarios,"monto"=>$monto,'inicio'=>$inicio,'fin'=>$fin,"titulo"=>$titulo]);
    }
    public function reporteHistorico(){
        $consultas=DB::select("SELECT user_id ,Fecha , SUM(Monto) as monto FROM cuentas GROUP BY user_id,Fecha ORDER BY Fecha DESC");
        $cuentas=[];
        $titulo="Historico";
        foreach($consultas as $c){
                array_push($cuentas,$c);
        }
        $usuarios=User::all()->keyBy('id');
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

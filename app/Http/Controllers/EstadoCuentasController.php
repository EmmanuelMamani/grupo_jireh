<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadoCuentasController extends Controller
{
    public function index(){
        return view('estados_cuentas');
    }
    public function reporte(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fechaFin    = Carbon::parse($request->fecha_fin)->endOfDay();

        // REPORTE POR PRODUCTO SOLO DEL RANGO
        $reporte = DB::select("
        SELECT
            c.producto,
            SUM(c.cant_ing) AS cant_ing,
            SUM(c.peso_ing) AS peso_ing,
            SUM(c.total_ing) AS total_ing,
            SUM(c.cantidad_salida) AS cantidad_salida,
            SUM(c.peso_salida) AS peso_salida,
            SUM(c.total_salida) AS total_salida
        FROM (
            SELECT
                i.CantMoldes AS cant_ing,
                i.Peso AS peso_ing,
                CASE
                    WHEN p.Tipo = 'Por Kilo' THEN i.Precio * i.Peso
                    WHEN p.Tipo = 'Por Unidad' THEN i.Precio * i.CantMoldes
                    ELSE 0
                END AS total_ing,
                COALESCE(v.total_cant, 0) AS cantidad_salida,
                COALESCE(v.total_peso, 0) AS peso_salida,
                COALESCE(v.total_venta, 0) AS total_salida,
                p.Nombre AS producto
            FROM ingresos i
            INNER JOIN productos p ON p.id = i.producto_id
            LEFT JOIN (
                SELECT
                    v.ingreso_id,
                    SUM(s.CantMoldes) AS total_cant,
                    SUM(s.Peso) AS total_peso,
                    SUM(s.Total) AS total_venta
                FROM salidas s
                INNER JOIN ventas v ON v.salida_id = s.id
                WHERE s.created_at BETWEEN ? AND ?
                GROUP BY v.ingreso_id
            ) AS v ON v.ingreso_id = i.id
            WHERE i.created_at BETWEEN ? AND ?
        ) AS c
        GROUP BY c.producto
        ORDER BY c.producto ASC
    ", [$fechaInicio, $fechaFin, $fechaInicio, $fechaFin]);

        $totales = [
            'cant_ing'        => collect($reporte)->sum('cant_ing'),
            'peso_ing'        => collect($reporte)->sum('peso_ing'),
            'total_ing'       => collect($reporte)->sum('total_ing'),
            'cantidad_salida' => collect($reporte)->sum('cantidad_salida'),
            'peso_salida'     => collect($reporte)->sum('peso_salida'),
            'total_salida'    => collect($reporte)->sum('total_salida'),
        ];

        // ESTADISTICAS DE GASTOS SOLO DEL RANGO
        $estadisticas = DB::selectOne("
        SELECT
            COALESCE(SUM(CASE WHEN LOWER(detalle) LIKE '%almuerzo%' THEN ABS(Monto) ELSE 0 END), 0) AS almuerzo,
            COALESCE(SUM(CASE WHEN LOWER(detalle) LIKE '%desayuno%' THEN ABS(Monto) ELSE 0 END), 0) AS desayuno,
            COALESCE(SUM(CASE WHEN LOWER(detalle) LIKE '%gasolina%' THEN ABS(Monto) ELSE 0 END), 0) AS gasolina,
            COALESCE(SUM(CASE WHEN LOWER(detalle) LIKE '%diesel%' THEN ABS(Monto) ELSE 0 END), 0) AS diesel,
            COALESCE(SUM(CASE WHEN LOWER(detalle) LIKE '%transporte%' THEN ABS(Monto) ELSE 0 END), 0) AS transporte,
            COALESCE(SUM(CASE WHEN LOWER(detalle) LIKE '%cambio aceite%' THEN ABS(Monto) ELSE 0 END), 0) AS aceite
        FROM cuentas
        WHERE Fecha BETWEEN ? AND ?
    ", [$fechaInicio, $fechaFin]);

        $estadisticas_totales = [
            'total_gastos' =>
                ($estadisticas->almuerzo ?? 0) +
                ($estadisticas->desayuno ?? 0) +
                ($estadisticas->gasolina ?? 0) +
                ($estadisticas->diesel ?? 0) +
                ($estadisticas->transporte ?? 0) +
                ($estadisticas->aceite ?? 0)
        ];

        return response()->json([
            'ok' => true,
            'filtros' => [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin'    => $request->fecha_fin,
            ],
            'data' => $reporte,
            'totales' => $totales,
            'estadisticas' => $estadisticas,
            'estadisticas_totales' => $estadisticas_totales,
        ]);
    }
}

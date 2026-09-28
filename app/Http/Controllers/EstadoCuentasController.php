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

        // Obtener ingresos por pagos
        $ingresosPorPago = DB::selectOne("
        SELECT SUM(Monto) AS total_pago
        FROM cuentas c
        WHERE c.created_at BETWEEN ? AND ?
        AND c.Detalle LIKE '%pago%'
    ", [$fechaInicio, $fechaFin]);

        $totales = [
            'cant_ing'        => collect($reporte)->sum('cant_ing'),
            'peso_ing'        => collect($reporte)->sum('peso_ing'),
            'total_ing'       => collect($reporte)->sum('total_ing'),
            'cantidad_salida' => collect($reporte)->sum('cantidad_salida'),
            'peso_salida'     => collect($reporte)->sum('peso_salida'),
            'total_salida'    => collect($reporte)->sum('total_salida'),
            'total_pago'      => $ingresosPorPago->total_pago ?? 0, // Ingresos por pagos
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

        // VENTAS VS COSTOS POR MES (devengado: fecha de venta)
        $ventasCostos = DB::select("
    SELECT
        DATE_FORMAT(v.created_at, '%Y-%m') AS mes,
        SUM(s.Total) AS ventas,
        SUM(COALESCE(s.costo_unitario, i.Precio) *
            CASE WHEN p.Tipo = 'Por Kilo' THEN COALESCE(s.Peso, 0) ELSE s.CantMoldes END
        ) AS costo
    FROM ventas v
    INNER JOIN salidas s ON s.id = v.salida_id
    INNER JOIN ingresos i ON i.id = v.ingreso_id
    INNER JOIN productos p ON p.id = i.producto_id
    WHERE v.created_at BETWEEN ? AND ?
    GROUP BY mes
    ORDER BY mes ASC
    ", [$fechaInicio, $fechaFin]);

        $ventasCostosTotales = [
            'ventas' => 0,
            'costo' => 0,
            'utilidad' => 0,
            'margen' => 0,
        ];
        foreach ($ventasCostos as $fila) {
            $fila->ventas = (float) $fila->ventas;
            $fila->costo = (float) $fila->costo;
            $fila->utilidad = $fila->ventas - $fila->costo;
            $fila->margen = $fila->ventas != 0 ? round($fila->utilidad / $fila->ventas * 100, 2) : 0;
            $ventasCostosTotales['ventas'] += $fila->ventas;
            $ventasCostosTotales['costo'] += $fila->costo;
        }
        $ventasCostosTotales['ventas'] = round($ventasCostosTotales['ventas'], 2);
        $ventasCostosTotales['costo'] = round($ventasCostosTotales['costo'], 2);
        $ventasCostosTotales['utilidad'] = round($ventasCostosTotales['ventas'] - $ventasCostosTotales['costo'], 2);
        $ventasCostosTotales['margen'] = $ventasCostosTotales['ventas'] != 0
            ? round($ventasCostosTotales['utilidad'] / $ventasCostosTotales['ventas'] * 100, 2)
            : 0;

        // COBRANZA (percibido: fecha de pago)
        $fechaIni = $fechaInicio->toDateString();
        $fechaFinD = $fechaFin->toDateString();
        $cobradoTotal = DB::selectOne("
            SELECT COALESCE(SUM(monto), 0) AS total FROM pagos WHERE fecha BETWEEN ? AND ?
        ", [$fechaIni, $fechaFinD]);
        $cobradoDelPeriodo = DB::selectOne("
            SELECT COALESCE(SUM(p.monto), 0) AS total
            FROM pagos p
            INNER JOIN ventas v ON v.id = p.venta_id
            WHERE p.fecha BETWEEN ? AND ? AND v.created_at BETWEEN ? AND ?
        ", [$fechaIni, $fechaFinD, $fechaInicio, $fechaFin]);
        $pendientes = DB::selectOne("
            SELECT COUNT(*) AS cantidad,
                COALESCE(SUM(GREATEST(s.Total - COALESCE(pp.pagado, 0), 0)), 0) AS total
            FROM ventas v
            INNER JOIN salidas s ON s.id = v.salida_id
            LEFT JOIN (SELECT venta_id, SUM(monto) AS pagado FROM pagos GROUP BY venta_id) pp ON pp.venta_id = v.id
            WHERE v.cliente_id IS NOT NULL AND (s.Total - COALESCE(pp.pagado, 0)) > 0
        ");
        $pendientesPeriodo = DB::selectOne("
            SELECT COUNT(*) AS cantidad,
                COALESCE(SUM(GREATEST(s.Total - COALESCE(pp.pagado, 0), 0)), 0) AS total
            FROM ventas v
            INNER JOIN salidas s ON s.id = v.salida_id
            LEFT JOIN (SELECT venta_id, SUM(monto) AS pagado FROM pagos GROUP BY venta_id) pp ON pp.venta_id = v.id
            WHERE v.cliente_id IS NOT NULL AND (s.Total - COALESCE(pp.pagado, 0)) > 0
            AND v.created_at BETWEEN ? AND ?
        ", [$fechaInicio, $fechaFin]);
        $pendientesAntiguedad = DB::select("
            SELECT
                CASE
                    WHEN DATEDIFF(CURDATE(), DATE(v.created_at)) <= 30 THEN '0-30'
                    WHEN DATEDIFF(CURDATE(), DATE(v.created_at)) <= 90 THEN '31-90'
                    WHEN DATEDIFF(CURDATE(), DATE(v.created_at)) <= 180 THEN '91-180'
                    ELSE '>180'
                END AS bucket,
                COUNT(*) AS cantidad,
                COALESCE(SUM(GREATEST(s.Total - COALESCE(pp.pagado, 0), 0)), 0) AS total
            FROM ventas v
            INNER JOIN salidas s ON s.id = v.salida_id
            LEFT JOIN (SELECT venta_id, SUM(monto) AS pagado FROM pagos GROUP BY venta_id) pp ON pp.venta_id = v.id
            WHERE v.cliente_id IS NOT NULL AND (s.Total - COALESCE(pp.pagado, 0)) > 0
            GROUP BY bucket
        ");
        $cobranza = [
            'cobrado_total' => (float) ($cobradoTotal->total ?? 0),
            'cobrado_del_periodo' => (float) ($cobradoDelPeriodo->total ?? 0),
            'pendiente_total' => (float) ($pendientes->total ?? 0),
            'ventas_con_pendiente' => (int) ($pendientes->cantidad ?? 0),
            'pendiente_periodo' => (float) ($pendientesPeriodo->total ?? 0),
            'ventas_con_pendiente_periodo' => (int) ($pendientesPeriodo->cantidad ?? 0),
            'pendiente_antiguedad' => $pendientesAntiguedad,
        ];

        // PROVEEDORES: compras del período + pagos + deuda total
        $compras = DB::selectOne("
            SELECT COUNT(*) AS lotes,
                COALESCE(SUM(CASE
                    WHEN p.Tipo = 'Por Kilo' THEN i.Precio * i.Peso
                    WHEN p.Tipo = 'Por Unidad' THEN i.Precio * i.CantMoldes
                    ELSE 0
                END), 0) AS costo_total
            FROM ingresos i
            INNER JOIN productos p ON p.id = i.producto_id
            WHERE i.created_at BETWEEN ? AND ?
        ", [$fechaInicio, $fechaFin]);
        $pagosProvRango = DB::selectOne("
            SELECT COALESCE(SUM(monto), 0) AS total FROM pago_proveedors WHERE fecha BETWEEN ? AND ?
        ", [$fechaIni, $fechaFinD]);
        $deudaProv = DB::selectOne("
            SELECT COALESCE(SUM(CASE
                    WHEN p.Tipo = 'Por Kilo' THEN i.Precio * i.Peso
                    WHEN p.Tipo = 'Por Unidad' THEN i.Precio * i.CantMoldes
                    ELSE 0
                END), 0) - COALESCE((SELECT SUM(monto) FROM pago_proveedors), 0) AS total
            FROM ingresos i
            INNER JOIN productos p ON p.id = i.producto_id
            WHERE i.Activo = 1
        ");
        $pagosProvHist = DB::selectOne("
            SELECT COUNT(*) AS n, COALESCE(SUM(monto), 0) AS total FROM pago_proveedors
        ");
        $proveedores = [
            'lotes' => (int) ($compras->lotes ?? 0),
            'compras_total' => (float) ($compras->costo_total ?? 0),
            'pagado_rango' => (float) ($pagosProvRango->total ?? 0),
            'deuda_total' => (float) ($deudaProv->total ?? 0),
            'flujo_neto_rango' => round((float) ($compras->costo_total ?? 0) - (float) ($pagosProvRango->total ?? 0), 2),
            'pagos_historico_n' => (int) ($pagosProvHist->n ?? 0),
            'pagos_historico' => (float) ($pagosProvHist->total ?? 0),
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
            'ventas_costos' => $ventasCostos,
            'ventas_costos_totales' => $ventasCostosTotales,
            'cobranza' => $cobranza,
            'proveedores' => $proveedores,
        ]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditoriaCuadre extends Command
{
    protected $signature = 'auditoria:cuadre {--json : Salida en JSON}';
    protected $description = 'Auditoría de integridad: huérfanos, costos, fechas, enlaces y relojes (solo lectura)';

    public function handle()
    {
        $r = [];
        $r['huerfanos_pagos'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM pagos WHERE venta_id IS NULL AND saldo_id IS NULL AND cuenta_id IS NULL")->n;
        $r['ventas_sin_salida'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM ventas v LEFT JOIN salidas s ON s.id = v.salida_id WHERE s.id IS NULL")->n;
        $r['ventas_sin_lote'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM ventas v LEFT JOIN ingresos i ON i.id = v.ingreso_id WHERE i.id IS NULL")->n;
        $r['salidas_sin_costo'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM salidas WHERE costo_unitario IS NULL")->n;
        $r['cuentas_fuera_de_fecha'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM cuentas WHERE DATEDIFF(Fecha, DATE(created_at)) != 0")->n;
        $r['cobros_sueltos_saldos'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM saldos s WHERE s.Detalle = 'Pago de deuda' AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = s.id)")->n;
        $r['cobros_sueltos_cuentas'] = (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM cuentas c WHERE c.Detalle LIKE 'Pago de de saldo de %' AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.cuenta_id = c.id)")->n;
        $php = date('Y-m-d H:i:s');
        $my = DB::selectOne('SELECT NOW() AS ahora');
        $r['reloj_php'] = $php;
        $r['reloj_mysql'] = $my->ahora;
        $r['reloj_desfase_seg'] = abs(strtotime($php) - strtotime($my->ahora));
        $r['reloj_tz'] = config('app.timezone') . '/' . date_default_timezone_get();

        $criticos = $r['huerfanos_pagos'] + $r['ventas_sin_salida'] + $r['ventas_sin_lote']
            + $r['salidas_sin_costo'] + $r['cuentas_fuera_de_fecha'];
        $r['estado'] = ($criticos === 0 && $r['reloj_desfase_seg'] <= 5) ? 'OK' : 'REVISAR';

        if ($this->option('json')) {
            $this->line(json_encode($r, JSON_PRETTY_PRINT));
        } else {
            foreach ($r as $k => $v) {
                $this->line(str_pad($k, 24) . ' ' . $v);
            }
        }
        return $r['estado'] === 'OK' ? 0 : 1;
    }
}

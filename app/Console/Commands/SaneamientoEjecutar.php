<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Réplica del saneamiento completo en una sola pasada ordenada e idempotente.
 * Uso: php artisan saneamiento:ejecutar   (con la app detenida, ver docs/replica-produccion.md)
 */
class SaneamientoEjecutar extends Command
{
    protected $signature = 'saneamiento:ejecutar';
    protected $description = 'Ejecuta el saneamiento completo (costos, pagos, fechas, conciliación) en orden y con verificaciones';

    protected $ppv = [];

    protected function fail($msg)
    {
        $this->error($msg);
        exit(1);
    }

    protected function cargarPendientes()
    {
        $this->ppv = [];
        $pagados = DB::select("SELECT venta_id, SUM(monto) AS pagado FROM pagos WHERE venta_id IS NOT NULL GROUP BY venta_id");
        foreach ($pagados as $p) {
            $this->ppv[$p->venta_id] = (float) $p->pagado;
        }
    }

    protected function ventasPorCliente()
    {
        $ventas = DB::select("SELECT v.id, v.cliente_id, DATE(v.created_at) AS fecha, s.Total AS total
            FROM ventas v JOIN salidas s ON s.id = v.salida_id WHERE v.cliente_id IS NOT NULL ORDER BY v.id");
        $vpc = [];
        foreach ($ventas as $v) {
            $vpc[$v->cliente_id][] = $v;
        }
        return $vpc;
    }

    /** Imputa $monto FIFO a ventas del cliente hasta $fecha. Devuelve [filas]. */
    protected function imputarFifo($clienteId, $monto, $fecha, $saldoId, $cuentaId, $ahora, $vpc)
    {
        $filas = [];
        $restante = round($monto, 2);
        $primero = true;
        foreach (($vpc[$clienteId] ?? []) as $v) {
            if ($restante <= 0) {
                break;
            }
            if ($v->fecha > $fecha) {
                break;
            }
            $pend = round($v->total - ($this->ppv[$v->id] ?? 0), 2);
            if ($pend <= 0) {
                continue;
            }
            $asig = min($restante, $pend);
            $filas[] = ['venta_id' => $v->id,
                'saldo_id' => $primero ? $saldoId : null, 'cuenta_id' => $primero ? $cuentaId : null,
                'cliente_id' => $clienteId, 'monto' => $asig,
                'fecha' => $fecha, 'created_at' => $ahora, 'updated_at' => $ahora];
            $this->ppv[$v->id] = ($this->ppv[$v->id] ?? 0) + $asig;
            $primero = false;
            $restante = round($restante - $asig, 2);
        }
        if ($restante > 0) {
            $filas[] = ['venta_id' => null,
                'saldo_id' => $primero ? $saldoId : null, 'cuenta_id' => $primero ? $cuentaId : null,
                'cliente_id' => $clienteId, 'monto' => $restante,
                'fecha' => $fecha, 'created_at' => $ahora, 'updated_at' => $ahora];
        }
        return $filas;
    }

    protected function topeKardex($clienteId)
    {
        $v = (float) DB::selectOne("SELECT COALESCE(SUM(s.Total),0) AS t FROM ventas v JOIN salidas s ON s.id = v.salida_id WHERE v.cliente_id = ?", [$clienteId])->t;
        $p = (float) DB::table('pagos')->where('cliente_id', $clienteId)->sum('monto');
        $k = DB::selectOne("SELECT Saldo FROM saldos WHERE cliente_id = ? ORDER BY id DESC LIMIT 1", [$clienteId]);
        return round($v - $p - ($k ? (float) $k->Saldo : 0), 2);
    }

    public function handle()
    {
        if (!Schema::hasTable('pagos') || !Schema::hasTable('pago_proveedors') || !Schema::hasColumn('salidas', 'costo_unitario')) {
            $this->fail('Faltan tablas/columnas: ejecutar `php artisan migrate` primero.');
        }
        $ahora = date('Y-m-d H:i:s');
        $this->cargarPendientes();
        $vpc = $this->ventasPorCliente();

        $this->pasoCostos();
        $this->pasoContado($ahora);
        $this->pasoPareoExacto($ahora, $vpc);
        $this->pasoFusionGlobales($ahora, $vpc);
        $this->pasoFechas();
        $this->pasoPareoExacto($ahora, $vpc); // re-pairing post-corrección
        $this->pasoMergeDuplicados($ahora, $vpc);
        $this->pasoConciliarCuentas($ahora, $vpc);
        $this->pasoRenombres($ahora, $vpc);
        $this->pasoReplay($ahora, $vpc);
        $this->pasoRelink();
        $this->info('Saneamiento completado. Corra `php artisan auditoria:cuadre` para certificar.');
        return 0;
    }

    protected function pasoCostos()
    {
        $n = DB::update("UPDATE salidas s JOIN ventas v ON v.salida_id = s.id JOIN ingresos i ON i.id = v.ingreso_id
            SET s.costo_unitario = i.Precio WHERE s.costo_unitario IS NULL");
        $restan = DB::selectOne("SELECT COUNT(*) AS n FROM salidas WHERE costo_unitario IS NULL")->n;
        $this->line("costos: backfill=$n restantes=$restan");
        if ($restan > 0) {
            $this->fail('Quedaron salidas sin costo.');
        }
    }

    protected function pasoContado($ahora)
    {
        $candidatas = DB::select("SELECT v.id, v.cliente_id, s.Total AS total, DATE(v.created_at) AS fecha
            FROM ventas v JOIN salidas s ON s.id = v.salida_id
            WHERE (s.al_contado = 1 OR (v.Estado = 1 AND v.cliente_id IS NULL))
              AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.venta_id = v.id)");
        $ins = 0;
        DB::transaction(function () use ($candidatas, $ahora, &$ins) {
            foreach ($candidatas as $v) {
                DB::table('pagos')->insert(['venta_id' => $v->id, 'saldo_id' => null, 'cuenta_id' => null,
                    'cliente_id' => $v->cliente_id, 'monto' => $v->total,
                    'fecha' => $v->fecha, 'created_at' => $ahora, 'updated_at' => $ahora]);
                $this->ppv[$v->id] = ($this->ppv[$v->id] ?? 0) + (float) $v->total;
                $ins++;
            }
        });
        $this->line("contado: pagos creados=$ins");
    }

    protected function parejasExactas()
    {
        return DB::select("SELECT s.id AS saldo_id, c.id AS cuenta_id, cl.id AS cliente_id,
                s.Monto AS monto, DATE(s.created_at) AS fecha
            FROM saldos s
            JOIN cuentas c ON c.Detalle LIKE 'Pago de de saldo de %'
                          AND c.Fecha = DATE(s.created_at)
                          AND ROUND(c.Monto, 2) = ROUND(s.Monto, 2)
            JOIN clientes cl ON cl.id = s.cliente_id AND cl.Nombre = TRIM(SUBSTRING(c.Detalle, 21))
            WHERE s.Detalle = 'Pago de deuda'
              AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = s.id)
              AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.cuenta_id = c.id)
            ORDER BY DATE(s.created_at), s.id");
    }

    protected function pasoPareoExacto($ahora, $vpc)
    {
        $rows = $this->parejasExactas();
        $grupos = [];
        foreach ($rows as $r) {
            $grupos[$r->cliente_id . '|' . $r->monto . '|' . $r->fecha][] = $r;
        }
        $uno = 0;
        $filas = [];
        DB::transaction(function () use ($grupos, $ahora, $vpc, &$uno, &$filas) {
            foreach ($grupos as $g) {
                if (count($g) !== 1) {
                    continue;
                }
                $r = $g[0];
                foreach ($this->imputarFifo($r->cliente_id, (float) $r->monto, $r->fecha, $r->saldo_id, $r->cuenta_id, $ahora, $vpc) as $f) {
                    $filas[] = $f;
                }
                $uno++;
                if (count($filas) >= 500) {
                    DB::table('pagos')->insert($filas);
                    $filas = [];
                }
            }
            if (!empty($filas)) {
                DB::table('pagos')->insert($filas);
            }
        });
        $this->line('pareo-exacto: grupos_1a1=' . $uno);
    }

    protected function pasoFusionGlobales($ahora, $vpc)
    {
        $grupos = DB::select("SELECT g.cliente_id, g.fecha, g.total,
                (SELECT GROUP_CONCAT(p.id) FROM pagos p WHERE p.venta_id IS NULL AND p.cliente_id = g.cliente_id AND p.fecha = g.fecha) AS ids
            FROM (SELECT p.cliente_id, ROUND(SUM(p.monto),2) AS total, p.fecha FROM pagos p
                  WHERE p.venta_id IS NULL GROUP BY p.cliente_id, p.fecha) g
            WHERE g.total = (SELECT ROUND(COALESCE(SUM(s.Monto),0),2) FROM saldos s WHERE s.Detalle = 'Pago de deuda'
                AND s.cliente_id = g.cliente_id AND DATE(s.created_at) = g.fecha
                AND NOT EXISTS (SELECT 1 FROM pagos q WHERE q.saldo_id = s.id)) AND g.total > 0");
        $n = 0;
        DB::transaction(function () use ($grupos, $ahora, $vpc, &$n) {
            foreach ($grupos as $g) {
                $rep = DB::table('pagos')->where('venta_id', null)->where('cliente_id', $g->cliente_id)
                    ->where('fecha', $g->fecha)->orderBy('id')->first();
                DB::table('pagos')->where('venta_id', null)->where('cliente_id', $g->cliente_id)->where('fecha', $g->fecha)->delete();
                foreach ($this->imputarFifo($g->cliente_id, (float) $g->total, $g->fecha, $rep ? $rep->saldo_id : null, $rep ? $rep->cuenta_id : null, $ahora, $vpc) as $f) {
                    DB::table('pagos')->insert($f);
                }
                $n++;
            }
        });
        $this->line('fusion-globales: grupos=' . $n);
    }

    protected function pasoFechas()
    {
        $n = DB::update("UPDATE cuentas SET Fecha = DATE(created_at) WHERE DATEDIFF(Fecha, DATE(created_at)) = 1 AND HOUR(created_at) BETWEEN 18 AND 23");
        $restan = DB::selectOne("SELECT COUNT(*) AS n FROM cuentas WHERE DATEDIFF(Fecha, DATE(created_at)) != 0")->n;
        $this->line("fechas: corregidas=$n con_desvio=$restan");
    }

    protected function dineroContado($clienteId, $fecha, $monto)
    {
        $d = DB::selectOne("SELECT id FROM pagos WHERE cliente_id = ? AND fecha = ? AND ROUND(monto,2) = ROUND(?,2) LIMIT 1", [$clienteId, $fecha, $monto]);
        return (bool) $d;
    }

    protected function pasoMergeDuplicados($ahora, $vpc)
    {
        $saldos = DB::select("SELECT s.id, s.cliente_id, s.Monto AS monto, DATE(s.created_at) AS fecha
            FROM saldos s WHERE s.Detalle = 'Pago de deuda'
            AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = s.id) ORDER BY s.created_at");
        $n = 0;
        $usadoTope = [];
        DB::transaction(function () use ($saldos, $ahora, $vpc, &$n, &$usadoTope) {
            foreach ($saldos as $s) {
                if ($this->dineroContado($s->cliente_id, $s->fecha, $s->monto)) {
                    continue;
                }
                $cs = DB::select("SELECT c.id FROM cuentas c WHERE c.Detalle LIKE 'Pago de de saldo de %'
                    AND ABS(DATEDIFF(c.Fecha, ?)) <= 1 AND ROUND(c.Monto,2) = ROUND(?,2)
                    AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.cuenta_id = c.id)", [$s->fecha, $s->monto]);
                if (count($cs) === 0) {
                    continue;
                }
                // fusionar grupo misma clave si totales coinciden
                $gS = DB::select("SELECT id, Monto FROM saldos WHERE cliente_id = ? AND Detalle = 'Pago de deuda'
                    AND DATE(created_at) = ? AND ROUND(Monto,2) = ROUND(?,2)
                    AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = saldos.id)", [$s->cliente_id, $s->fecha, $s->monto]);
                $sumS = array_sum(array_map(fn($r) => (float) $r->Monto, $gS));
                $sumC = array_sum(array_map(fn($r) => (float) DB::table('cuentas')->where('id', $r->id)->value('Monto'), $cs));
                if (abs($sumS - $sumC) > 0.01 || $sumS <= 0) {
                    continue;
                }
                if (!isset($usadoTope[$s->cliente_id])) {
                    $usadoTope[$s->cliente_id] = ['tope' => $this->topeKardex($s->cliente_id), 'usado' => 0];
                }
                $disp = round($usadoTope[$s->cliente_id]['tope'] - $usadoTope[$s->cliente_id]['usado'], 2);
                if ($disp <= 0) {
                    continue;
                }
                $total = min($sumS, $disp);
                $this->cargarPendientes();
                $vpc2 = $this->ventasPorCliente();
                foreach ($this->imputarFifo($s->cliente_id, $total, $s->fecha, $gS[0]->id, $cs[0]->id, $ahora, $vpc2) as $f) {
                    DB::table('pagos')->insert($f);
                }
                $usadoTope[$s->cliente_id]['usado'] = round($usadoTope[$s->cliente_id]['usado'] + $total, 2);
                $n++;
            }
        });
        $this->line('merge-duplicados: grupos=' . $n);
    }

    protected function pasoConciliarCuentas($ahora, $vpc)
    {
        $cuentas = DB::select("SELECT c.id, c.Monto AS monto, c.Fecha AS fecha, TRIM(SUBSTRING(c.Detalle, 21)) AS nombre
            FROM cuentas c WHERE c.Detalle LIKE 'Pago de de saldo de %'
            AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.cuenta_id = c.id) ORDER BY c.Fecha, c.id");
        $clientes = DB::table('clientes')->pluck('id', 'Nombre')->all();
        $n = 0;
        $usadoTope = [];
        DB::transaction(function () use ($cuentas, $clientes, $ahora, $vpc, &$n, &$usadoTope) {
            foreach ($cuentas as $c) {
                if (!isset($clientes[$c->nombre])) {
                    continue;
                }
                $cid = $clientes[$c->nombre];
                if ($this->dineroContado($cid, (string) $c->fecha, (float) $c->monto)) {
                    continue;
                }
                if (!isset($usadoTope[$cid])) {
                    $usadoTope[$cid] = ['tope' => $this->topeKardex($cid), 'usado' => 0];
                }
                $disp = round($usadoTope[$cid]['tope'] - $usadoTope[$cid]['usado'], 2);
                if ($disp <= 0) {
                    continue;
                }
                $monto = min(round((float) $c->monto, 2), $disp);
                foreach ($this->imputarFifo($cid, $monto, (string) $c->fecha, null, $c->id, $ahora, $vpc) as $f) {
                    DB::table('pagos')->insert($f);
                }
                $usadoTope[$cid]['usado'] = round($usadoTope[$cid]['usado'] + $monto, 2);
                $n++;
            }
        });
        $this->line('conciliar-cuentas: ' . $n);
    }

    protected function pasoRenombres($ahora, $vpc)
    {
        // cuentas cuyo nombre no existe exacto + un único saldo suelto misma clave
        $cuentas = DB::select("SELECT c.id, c.Monto AS monto, c.Fecha AS fecha, TRIM(SUBSTRING(c.Detalle, 21)) AS nombre
            FROM cuentas c WHERE c.Detalle LIKE 'Pago de de saldo de %'
            AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.cuenta_id = c.id)");
        $clientes = DB::table('clientes')->pluck('id', 'Nombre')->all();
        $n = 0;
        $usadoTope = [];
        DB::transaction(function () use ($cuentas, $clientes, $ahora, $vpc, &$n, &$usadoTope) {
            foreach ($cuentas as $c) {
                if (isset($clientes[$c->nombre])) {
                    continue;
                }
                $ss = DB::select("SELECT s.id, s.cliente_id FROM saldos s WHERE s.Detalle = 'Pago de deuda'
                    AND DATE(s.created_at) = ? AND ROUND(s.Monto,2) = ROUND(?,2)
                    AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = s.id)", [$c->fecha, $c->monto]);
                if (count($ss) !== 1) {
                    continue;
                }
                $cid = $ss[0]->cliente_id;
                if ($this->dineroContado($cid, (string) $c->fecha, (float) $c->monto)) {
                    continue;
                }
                if (!isset($usadoTope[$cid])) {
                    $usadoTope[$cid] = ['tope' => $this->topeKardex($cid), 'usado' => 0];
                }
                $disp = round($usadoTope[$cid]['tope'] - $usadoTope[$cid]['usado'], 2);
                if ($disp <= 0) {
                    continue;
                }
                $monto = min(round((float) $c->monto, 2), $disp);
                foreach ($this->imputarFifo($cid, $monto, (string) $c->fecha, $ss[0]->id, $c->id, $ahora, $vpc) as $f) {
                    DB::table('pagos')->insert($f);
                }
                $usadoTope[$cid]['usado'] = round($usadoTope[$cid]['usado'] + $monto, 2);
                $n++;
            }
        });
        $this->line('renombres: parejas=' . $n);
    }

    protected function pasoReplay($ahora, $vpc)
    {
        $saldos = DB::select("SELECT s.id, s.cliente_id, s.Monto AS monto, DATE(s.created_at) AS fecha
            FROM saldos s WHERE s.Detalle = 'Pago de deuda'
            AND NOT EXISTS (SELECT 1 FROM pagos p WHERE p.saldo_id = s.id) ORDER BY s.created_at");
        $usadoTope = [];
        $n = 0;
        $creado = 0;
        DB::transaction(function () use ($saldos, $ahora, $vpc, &$n, &$creado, &$usadoTope) {
            foreach ($saldos as $s) {
                $cid = $s->cliente_id;
                $ya = DB::selectOne("SELECT id FROM pagos WHERE cliente_id = ? AND fecha = ? AND ROUND(monto,2) = ROUND(?,2) LIMIT 1", [$cid, $s->fecha, $s->monto]);
                if ($ya) {
                    continue;
                }
                if (!isset($usadoTope[$cid])) {
                    $usadoTope[$cid] = ['tope' => $this->topeKardex($cid), 'usado' => 0];
                }
                $disp = round($usadoTope[$cid]['tope'] - $usadoTope[$cid]['usado'], 2);
                if ($disp <= 0) {
                    continue;
                }
                $monto = min(round((float) $s->monto, 2), $disp);
                foreach ($this->imputarFifo($cid, $monto, $s->fecha, $s->id, null, $ahora, $vpc) as $f) {
                    DB::table('pagos')->insert($f);
                }
                $usadoTope[$cid]['usado'] = round($usadoTope[$cid]['usado'] + $monto, 2);
                $creado = round($creado + $monto, 2);
                $n++;
            }
        });
        $this->line("replay: aplicados=$n creado=$creado");
    }

    protected function pasoRelink()
    {
        $filas = DB::select("SELECT id, cliente_id, fecha, venta_id, saldo_id, cuenta_id FROM pagos WHERE cliente_id IS NOT NULL");
        $grupos = [];
        foreach ($filas as $f) {
            $grupos[$f->cliente_id . '|' . $f->fecha][] = $f;
        }
        $upd = 0;
        $amb = 0;
        DB::transaction(function () use ($grupos, &$upd, &$amb) {
            foreach ($grupos as $g) {
                $huerf = array_values(array_filter($g, fn($r) => $r->saldo_id === null && $r->cuenta_id === null));
                if (empty($huerf)) {
                    continue;
                }
                $fuentes = array_values(array_filter($g, fn($r) => $r->saldo_id !== null || $r->cuenta_id !== null));
                if (count($fuentes) !== 1) {
                    $amb++;
                    continue;
                }
                foreach ($huerf as $h) {
                    $set = [];
                    if ($fuentes[0]->saldo_id !== null) {
                        $set['saldo_id'] = $fuentes[0]->saldo_id;
                    }
                    if ($fuentes[0]->cuenta_id !== null) {
                        $set['cuenta_id'] = $fuentes[0]->cuenta_id;
                    }
                    if (empty($set)) {
                        continue;
                    }
                    DB::table('pagos')->where('id', $h->id)->update($set);
                    $upd++;
                }
            }
        });
        $this->line("relink: filas=$upd ambiguos=$amb");
    }
}

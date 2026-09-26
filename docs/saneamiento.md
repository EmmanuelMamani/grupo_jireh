# Bitácora de saneamiento — Grupo Jireh (2026-09-26)

Base de laboratorio (copia de prueba). La base real del cliente sigue en producción;
ver `replica-produccion.md` para el corte y `resumen-ejecutivo.md` para la versión no técnica.

## 0. Entorno y punto de partida
- App Laravel 8 (`laravel/framework ^8.75`), PHP 8.2.12 (XAMPP), MySQL local, BD `jireh`.
- `composer install` bloqueado: faltaba `ext-gd` (`composer.json` la exige).
- `composer.json` pedía `barryvdh/laravel-dompdf ^2.0` → `dompdf/dompdf 2.0.x`,
  bloqueado por Composer 2.10 por avisos de seguridad (PKSA-*, < 3.1.6).

## 1. PHP + dependencias
- Backup `C:\xampp\php\php.ini` → `php.ini.bak`.
- `C:\xampp\php\php.ini:931`: `;extension=gd` → `extension=gd`.
  Verificado con `php --ri gd` (GD 2.1, PNG/JPEG/FreeType/WebP).
- Upgrade Laravel 8 → 9 (mínimo necesario para `barryvdh/laravel-dompdf ^3`
  + `dompdf/dompdf 3.1.6`, la versión parcheada):
  `composer.json`: `php ^8.0.2`, `laravel/framework ^9.0`,
  `barryvdh/laravel-dompdf ^3.0`, `laravel/tinker ^2.7`,
  `facade/ignition` → `spatie/laravel-ignition ^1.0`,
  `nunomaduro/collision ^6.0`.
  Resultado: Laravel 9.52.22, dompdf 3.1.6, sin avisos. Backup `composer.json.bak-l8`.
- `config/app.php`: alias `PDF` → `Barryvdh\DomPDF\Facade\Pdf` (el `Facade` viejo
  no existe en v3). `setOptions()->loadView()` sigue igual. Prueba real de PDF OK.

## 2. Estadísticas de cuentas (filtros + dashboard)
- `CuentaController@estadisticas`: acepta `desde`, `hasta`, `categorias[]` por GET,
  validado, SQL dinámico con bindings (antes interpolaba), `ORDER BY año, mes`,
  totales por categoría + total general. Sin filtros = comportamiento anterior.
- Vista `estadisticas_cuentas.blade.php`: formulario (fechas + 6 checkboxes),
  4 tarjetas KPI (total, top, meses, promedio), gráfico ApexCharts (línea/barras/área
  + descarga), tabla de totales, estado vacío. Se eliminó el CDN de Tailwind.
- Verificado con 8 escenarios contra 23 879 cuentas reales.

## 3. Reporte ventas vs costos (criterio devengado + columnas)
- Hallazgo: 3 fechas mezcladas (venta `ventas.created_at`, cobro `cuentas.Fecha`,
  compra `ingresos.created_at`); pagos globales por cliente (sin `venta_id`);
  costo leído en vivo de `ingresos.Precio` (mutable); `Pagado` sin monto/fecha.
- Migraciones (ver `database/migrations/2026_09_26_*.php`):
  - `salidas.costo_unitario` (snapshot de `ingresos.Precio` al vender).
  - `pagos` (`venta_id/saldo_id/cuenta_id/cliente_id/monto/fecha`, FKs con
    `nullOnDelete` en las tres primeras).
  - `pago_proveedors` (`ingreso_id/monto/fecha/cuenta_id/user_id`).
- Código: `costo_unitario` en `registro/registroRapido/venta_completa`;
  filas `pagos` en contado/acuenta/rápidas; `SaldoController::Pago` con
  imputación FIFO + `venta_id` opcional (+ endpoint `ventasPendientes`,
  selector en `saldos.blade.php`); `IngresoController::Pagar` con monto/fecha
  (total por defecto, parcial soportado) + egreso espejo en `cuentas`.
- `EstadoCuentasController::reporte`: bloques nuevos `ventas_costos(+totales)`,
  `cobranza`, `proveedores` (claves viejas intactas). UI `estados_cuentas.blade.php`
  rediseñada (KPIs, gráfico mensual, tablas, marca #125149/#DA7922).
- Ej. 2024: ventas Bs 7 091 959.77, costo Bs 6 235 343.11,
  utilidad Bs 856 616.66 (12.08 %).

## 4. Backfills históricos (orden de ejecución, todos con backup previo)
1. `costo_unitario`: 39 956 filas desde `ingresos.Precio` vía `ventas.ingreso_id`.
2. `pagos` contado/rápidas: 8 757 filas (monto = Total, fecha = venta).
3. Pairing exacto saldo↔cuenta (cliente+monto+fecha) + FIFO: 33 865 filas
   (Bs 14 911 023.73) + 162 globales (Bs 54 839.04).
4. Fusión de 71 grupos ambiguos: Bs 41 264 (25 absorbidos ~Bs 11.4K, resto global).
5. Corrección de 536 `cuentas.Fecha` (+1 día por desfase PHP/MySQL nocturno).
6. Re-pairing post-corrección: 565 filas (Bs 266 092.96) + 187 globales.
7. Merge duplicados/±1 día 2026: 0 fusible restante (ya cubierto) — verificado.
8. Conciliación 44 cuentas 2026: 30 aplicadas (Bs 8 526.00), 14 saltadas
   ("Carnicería la junta" → cliente 354, renombrado 01/04/2026).
9. Caso yunta: 14 parejas (Bs 4 881.60).
10. Replay FIFO pre-2026 con tope kardex: 481 aplicados (Bs 439 255.68).
11. Clasificación final + categoría (b): 6 aplicados (Bs 1 907.12).
12. Relink de hermanos: 21 168 filas (UPDATEs, solo enlaces).
- Reglas inviolables aplicadas en todo: 1:1 inequívoco o grupo fusionable con
  totales iguales; FIFO cronológico con tope kardex; nunca duplicar dinero
  (firewall misma-clave y ±1 día); transacciones con rollback; sumas exactas.

## 5. Fechas (causa raíz encontrada y cerrada)
- 536 `cuentas.Fecha` = día siguiente al `created_at`, todas 18:00–23:59:
  el `date('Y-m-d')` de PHP iba un día adelante del MySQL en horario nocturno
  (prueba: cuenta #21890 creada 13/03 21:56:39 con Fecha 14/03).
- Hoy ambos relojes coinciden al segundo (sonda) y Laravel usa `America/La_Paz`
  (en config desde 2022, sin caché que lo anule) → causa histórica, cerrada.
- Corrección: `Fecha = DATE(created_at)` en esas 536. Re-barrido = 0 desvíos.

## 6. Verificaciones finales (laboratorio)
- `pagos`: ~Bs 19.89M totales; 0 huérfanos (regla auditada por test);
  0 salidas sin costo; 0 ventas sin salida/lote.
- Septiembre 2026: cobrado Bs 382 381.99 (era Bs 0 antes del backfill de cobros).
- Cobros sueltos: 898 (Bs 1.39M) → ~154 anotaciones residuales explicadas;
  2026 en caja 100 % atribuido.
- Suite: 13 tests passed (`ReportePagosTest`, `ConciliacionTest`, ejemplos).
- Kardex (`saldos`) y caja (`cuentas`) históricas: cero filas modificadas
  (solo se agregó `Fecha` corregida en 536 cuentas + filas nuevas en
  `pagos`/`pago_proveedors` + columna `costo_unitario`).

## 8. Comandos versionados (para réplica y auditoría)
- `php artisan saneamiento:ejecutar`: consolida todos los backfills en 13 pasos
  ordenados, idempotentes (guardas `NOT EXISTS`, `DATEDIFF = 1`, firewall de
  dinero ya contado y tope kardex). Verificado en laboratorio con 3 corridas:
  converge (2.ª y 3.ª corrida ~ceros, sumas idénticas; `fusion-globales`
  revalida grupos residuales sin mover montos).
- `php artisan auditoria:cuadre [--json]`: 6 chequeos + sonda de relojes,
  exit 0/1. Estado actual del laboratorio: OK.
- Tests: 13 passed (incluye `todo_pago_tiene_origen` como guardián permanente).
## 7. Limitaciones declaradas
- Crédito histórico sin match (≈8 %): pendiente por venta sobreestima vs kardex;
  el kardex manda; el FIFO lo absorbe en ~6 meses de cobranza.
- Devoluciones Por Kilo no ajustan `Peso` (lógica previa intacta).
- Ventas canceladas borran la venta (sus pagos quedan globales trazables).
- `Image::fromFile` (preexistente, roto) e `imagick` como driver configurado
  sin estar instalado: no se tocaron.
- Avisos de seguridad residuales en `laravel/framework` 9.x (parche solo en 12/13).

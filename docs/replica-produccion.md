# Réplica a producción — runbook del corte

## Pre-requisitos
- [ ] Servidor externo con PHP ≥ 8.0.2 + extensión `gd` habilitada
  (verificar con `php --ri gd`) y MySQL disponible.
- [ ] Ventana de downtime acordada (nadie opera el sistema durante la corrida).
- [ ] Acceso SSH/consola al servidor + credenciales MySQL con permiso de backup.
- [ ] Este repo commiteado y desplegable (ver paso 1).

## Paso 0 — Línea base de producción (antes de tocar nada)
1. `php artisan auditoria:cuadre --json > auditoria-pre.json` (no escribe nada).
2. Guardar los 6 números. Si alguno es crítico (huérfanos, sin costo),
   avisar antes de seguir.

## Paso 1 — Despliegue de código
1. `git pull` (o copia) del código con todos los cambios + `composer install`.
2. Verificar `.env` (no se versiona): `APP_KEY`, `DB_*`, `MAIL_*`.
3. `php artisan migrate --force` (crea `costo_unitario`, `pagos`,
   `pago_proveedors`; las 3 tienen `down()`).

## Paso 2 — Backup (obligatorio)
```bash
mysqldump --host=... --user=... jireh_produccion > jireh-pre-corte.sql
```
Anotar tamaño y hora. Sin backup verificado, NO seguir.

## Paso 3 — Saneamiento (un solo comando, ~13 sub-pasos en orden)
```bash
php artisan saneamiento:ejecutar
```
Orden interno: costos → contado/rápidas → pairing exacto → fusión globales →
corrección de fechas → re-pairing → merge duplicados/±1 → conciliación de
cuentas → replay FIFO con tope kardex → categoría (b) → relink final →
auditoría. Cada paso imprime conteos; ante anomalía (suma inexacta, FK,
tope excedido) hace rollback de ese paso y se detiene con mensaje claro.
Idempotente: re-ejecutar solo completa lo pendiente (todo usa `NOT EXISTS`,
`DATEDIFF = 1` y enlaces faltantes como guardas).

## Paso 4 — Verificación post-corte
1. `php artisan auditoria:cuadre` → 0 huérfanos, 0 sin costo, 0 desvíos de fecha,
   regla venta/saldo/cuenta en verde.
2. `php artisan test` en el servidor (o al menos `ReportePagosTest`).
3. Humo funcional: login, registrar + anular una venta de prueba (y revertirla),
   cobrar en Cobranza con imputación, reporte septiembre, conciliación.
4. Comparar `auditoria-pre.json` vs post: las deudas y caja deben ser idénticas;
   solo cambian las métricas por venta/cobrado (que antes estaban en 0 o parciales).

## Paso 5 — Cierre
1. Reiniciar Apache/PHP-FPM si aplica (opcache).
2. Levantar el downtime y avisar al cliente.
3. Guardar logs de la corrida + `auditoria-pre/post.json` junto al backup.

## Rollback
- **Dentro del comando:** rollback automático por paso (transacciones).
- **Post-corte (si algo grave):** restaurar `jireh-pre-corte.sql` + volver al
  commit anterior del código. Tiempo estimado: 10–20 min (dump de ~4 GB).
- **Regla de oro:** si durante la corrida se detecta operatoria concurrente
  (filas nuevas ajenas al comando), detener y re-evaluar antes de seguir.

## Diferencias esperadas vs laboratorio
- Conteos y montos diferirán (otros datos). Las validaciones son por reglas
  (sumas exactas paso a paso, topes kardex, 1:1), no por cifras fijas.
- Casos ambiguos/nuevos quedan como globales o sueltos para la pantalla
  Conciliación — el comando nunca inventa atribuciones.

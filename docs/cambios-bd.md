# Cambios de base de datos — guía para conectar la BD real

> Complemento de `replica-produccion.md` (runbook del corte).
> Este documento lista **qué cambió en la BD**, qué es solo código
> y qué configurar al apuntar a la base de datos real.

## 1. Migraciones que la BD real debe tener

Todas están en `database/migrations/` y tienen `down()`.
Se aplican con `php artisan migrate --force` (paso 1 del runbook).

| Migración | Cambio de esquema |
|---|---|
| `2026_09_26_120000_add_costo_unitario_to_salidas_table` | `salidas.costo_unitario` DOUBLE(8,2) NULL después de `Precio` |
| `2026_09_26_121000_create_pagos_table` | Crea `pagos`: `venta_id/saldo_id/cuenta_id` NULL con FK `nullOnDelete`, `cliente_id` INT NULL (sin FK), `monto` DOUBLE(8,2), `fecha` DATE |
| `2026_09_26_122000_create_pago_proveedors_table` | Crea `pago_proveedors`: `ingreso_id` FK `cascadeOnDelete`, `monto`, `fecha`, `cuenta_id` NULL FK `nullOnDelete`, `user_id` INT NULL (sin FK) |
| `2026_09_27_195830_drop_comprobantes_table` | Elimina tabla `comprobantes` (módulo retirado; el `down()` no la recrea a propósito) |
| `2026_09_27_201539_tiendas_blob_to_path` | `clientes.tienda` MEDIUMBLOB → VARCHAR(255) NULL (path en disco `public`, ej. `tiendas/cliente_12_....jpg`). Los blobs históricos se ponen a NULL: estaban corruptos (bug `Nette\Utils\Image`) e impedían el cambio de tipo |

Tablas base (2022, sin cambios): `users, ingresos, salidas, clientes,
saldos, ventas, mermas, asignacions, zonas, productos, cuentas, listas`.

## 2. Alineación solo-código (NO requiere `migrate`)

Commit `refactor(modelos)`: los modelos y el factory se alinearon al
esquema real. Si tu BD ya tiene el esquema del punto 1, no hay nada
que ejecutar por este commit.

| Archivo | Cambio | Por qué |
|---|---|---|
| `Asignacion` | `asignador()` → FK `asignador_id`, `asignado()` → FK `asignado_id` | Antes ambas resolvían a `user_id` (columna inexistente) |
| `Cuenta` | Nuevas `pagos()` (hasMany `Pago`), `pagoProveedors()` (hasMany `PagoProveedor`) | Navegación a cobros y pagos a proveedor |
| `Ingreso::salidas()` | Pivote explícito `Venta` con keys `ingreso_id/salida_id` | Antes resolvía a tabla `ingreso_salida` (inexistente) |
| `Salida::lotes()` | Pivote explícito `Venta` con keys `salida_id/ingreso_id` | Idem anterior |
| `PagoProveedor` | Nueva `user()` (belongsTo `User`) | Quién registró el pago (`user_id`) |
| `Saldo` | Nueva `pagos()` (hasMany `Pago`) | Cobros imputados a la deuda |
| `User` | `fillable`: `CI, Nombre, Email, Telefono, Rol, Usuario, Contrasenia, Activo`; `casts`: `Activo => boolean` | Antes traía el scaffold (`name, password, email_verified_at`) que no existe en `users` |
| `UserFactory` | Genera columnas reales (`CI, Nombre, Email, Telefono, Rol=Empleado, Usuario, Contrasenia=password, Activo`) | El factory anterior fallaba contra la tabla real |

## 3. Conexión a la BD real — checklist `.env`

El `.env` no se versiona. Variables a definir (ver `.env.example`):

```ini
DB_CONNECTION=mysql
DB_HOST=<host real>
DB_PORT=3306
DB_DATABASE=<bd real>
DB_USERNAME=<usuario>
DB_PASSWORD=<clave>
```

Además: `APP_KEY` (generar con `php artisan key:generate` si es
instalación nueva), `APP_URL`, y `MAIL_*` si se usan notificaciones.

## 4. Orden de aplicación en la BD real

1. Backup: `mysqldump --host=... --user=... <bd> > jireh-pre-corte.sql`
2. `composer install`, configurar `.env`
3. `php artisan migrate --force` (aplica el punto 1)
4. `php artisan auditoria:cuadre --json > auditoria-pre.json`
5. `php artisan saneamiento:ejecutar` (detalle en `replica-produccion.md`)
6. `php artisan auditoria:cuadre` → 0 huérfanos, 0 sin costo, 0 desvíos
7. `php artisan test` en el servidor

## 5. Notas de compatibilidad

- `pagos.cliente_id` y `pago_proveedors.user_id` son INT **sin FK**:
  funcionan aunque los ids no existan; no rompen el `migrate`.
- `clientes.tienda` ahora es path relativo al disco `public`:
  tras migrar, ejecutar `php artisan storage:link` en el servidor
  para que las fotos sean accesibles vía web.
- La tabla `comprobantes` desaparece: verificar que ningún reporte
  externo la consulte antes del corte.

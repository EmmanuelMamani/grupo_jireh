<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TiendasBlobToPath extends Migration
{
    /**
     * La columna `clientes.tienda` pasa de MEDIUMBLOB a VARCHAR(255):
     * en adelante solo guarda el path relativo en el disco `public`
     * (ej. `tiendas/cliente_12_1729991234.jpg`).
     *
     * Los blobs históricos se descartan (NULL): quedaron corruptos por el
     * bug de `Nette\Utils\Image` (ninguno es legible como imagen, ni con
     * Intervention ni por MIME) e impedían el cambio de tipo.
     *
     * @return void
     */
    public function up()
    {
        DB::table('clientes')->whereNotNull('tienda')->update(['tienda' => null]);
        DB::statement('ALTER TABLE clientes MODIFY tienda VARCHAR(255) NULL');
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE clientes MODIFY tienda MEDIUMBLOB NULL');
    }
}

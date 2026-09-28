<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropComprobantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('comprobantes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // La creación original vive en 2022_07_31_004002_create_comprobantes_table.
        // No se recrea aquí para evitar reintroducir el longblob eliminado.
    }
}

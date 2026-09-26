<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostoUnitarioToSalidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->double('costo_unitario', 8, 2)->nullable()->after('Precio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->dropColumn('costo_unitario');
        });
    }
}

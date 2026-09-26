<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoProveedorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_proveedors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingreso_id');
            $table->double('monto', 8, 2);
            $table->date('fecha');
            $table->unsignedBigInteger('cuenta_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();

            $table->foreign('ingreso_id')->references('id')->on('ingresos')->cascadeOnDelete();
            $table->foreign('cuenta_id')->references('id')->on('cuentas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_proveedors');
    }
}

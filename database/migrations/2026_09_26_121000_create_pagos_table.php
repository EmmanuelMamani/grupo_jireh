<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id')->nullable();
            $table->unsignedBigInteger('saldo_id')->nullable();
            $table->unsignedBigInteger('cuenta_id')->nullable();
            $table->integer('cliente_id')->nullable();
            $table->double('monto', 8, 2);
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('ventas')->nullOnDelete();
            $table->foreign('saldo_id')->references('id')->on('saldos')->nullOnDelete();
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
        Schema::dropIfExists('pagos');
    }
}

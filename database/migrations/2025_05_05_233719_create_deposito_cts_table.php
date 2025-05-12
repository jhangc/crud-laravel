<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositoCtsTable extends Migration
{
    public function up()
    {
        Schema::create('deposito_cts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cts_usuario_id');       // referencia a cts_usuarios
            $table->decimal('monto', 15, 2);                    // monto depositado (permanece en caja)
            $table->timestamp('fecha_deposito')->nullable();                      // fecha del depósito
            $table->unsignedBigInteger('realizado_por')->nullable();      // quién registró
            $table->unsignedBigInteger('caja_transaccion_id')->nullable(); // enlace a transacción de caja
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposito_cts');
    }
}

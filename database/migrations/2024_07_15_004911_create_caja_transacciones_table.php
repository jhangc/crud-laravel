<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTransaccionesTable extends Migration
{
    public function up()
    {
        Schema::create('caja_transacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caja_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // ID del usuario que abrió la caja
            $table->decimal('monto_apertura', 15, 3)->nullable();
            $table->json('json_apertura')->nullable();
            $table->json('json_cierre')->nullable();
            $table->time('hora_apertura')->nullable();
            $table->time('hora_cierre')->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->decimal('monto_cierre', 15, 2)->nullable();
            $table->decimal('cantidad_ingresos', 15, 2)->nullable();
            $table->decimal('cantidad_egresos', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('caja_transacciones');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeudasFinancierasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deudas_financieras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_entidad');
            $table->decimal('saldo_capital', 15, 2);
            $table->decimal('cuota', 15, 2);
            $table->integer('tiempo_restante');
            $table->unsignedBigInteger('id_cliente');
            $table->string('estado');
            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deudas_financieras');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInicioDesembolsosTable extends Migration
{
    public function up()
    {
        Schema::create('inicio_desembolsos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');             // quién solicita
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->boolean('permiso_abierto')->default(false);// permiso para desembolso
            $table->enum('estado', ['pendiente','aprobado','rechazado'])
                  ->default('pendiente');                      // flujo de aprobación
            $table->string('documento_autorizacion')->nullable();// doc. soporte
            $table->timestamps();
            $table->softDeletes();                             // opcional: borrado lógico
        });
    }

    public function down()
    {
        Schema::dropIfExists('inicio_desembolsos');
    }
}

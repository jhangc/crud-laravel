<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReprogramacionesTable extends Migration
{
    public function up()
    {
        Schema::create('reprogramaciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Quién solicita
            $table->unsignedBigInteger('usuario_id');
            // Quién aprueba/rechaza (puede quedar null hasta la aprobación/rechazo)
            $table->unsignedBigInteger('admin_id')->nullable();
            // Referencia al crédito
            $table->unsignedBigInteger('credito_id');
            // Datos de la solicitud
            $table->integer('cuotas_pendientes'); // Cantidad de cuotas pendientes que se reprograman
            $table->decimal('tasa_interes', 10, 2);
            $table->date('fecha_reprogramar');

            // Saldos pendientes en el momento de la solicitud
            $table->decimal('capital_restante', 14, 2);
            $table->decimal('interes_restante', 14, 2);
            $table->integer('nuevo_numero_cuotas'); // Nueva cantidad de cuotas que se reprograman

            // Flujo de aprobación
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])
                  ->default('pendiente');
            $table->text('observaciones')->nullable();        // del solicitante
            $table->text('comentario_admin')->nullable();     // feedback del admin

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reprogramaciones');
    }
}

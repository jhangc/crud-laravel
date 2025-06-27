<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisadoToEstadoInReprogramacionesTable extends Migration
{
    public function up()
    {
        // Necesitas doctrine/dbal para modificar un enum
        Schema::table('reprogramaciones', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'generado'])
                  ->default('pendiente')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('reprogramaciones', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])
                  ->default('pendiente')
                  ->change();
        });
    }
}
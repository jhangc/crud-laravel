<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gasto_producir', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_actividad');
            $table->integer('cantidad_terreno');
            $table->integer('produccion_total');
            $table->decimal('precio_kg', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gasto_producir');
    }
};

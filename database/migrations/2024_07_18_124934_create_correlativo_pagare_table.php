<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrelativoPagareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correlativo_pagare', function (Blueprint $table) {
            $table->id(); // ID por defecto
            $table->string('serie'); // Campo serie
            $table->integer('numero'); // Campo numero
            $table->unsignedBigInteger('id_prestamo'); // Campo id_prestamo
            $table->string('correlativo'); // Campo correlativo
            $table->timestamps(); // Campos created_at y updated_at por defecto
            $table->softDeletes(); // Campo deleted_at por defecto

            // Si es necesario, puedes añadir una clave foránea para id_prestamo
            // $table->foreign('id_prestamo')->references('id')->on('prestamos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correlativo_pagare');
    }
}

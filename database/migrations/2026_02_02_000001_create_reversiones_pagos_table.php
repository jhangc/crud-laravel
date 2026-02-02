<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reversiones_pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingreso_id');
            $table->unsignedBigInteger('prestamo_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('monto', 15, 3);
            $table->string('motivo')->nullable();
            $table->text('detalles')->nullable();
            $table->timestamps();

            $table->foreign('ingreso_id')->references('id')->on('ingresos')->onDelete('cascade');
            $table->foreign('prestamo_id')->references('id')->on('prestamos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reversiones_pagos');
    }
};

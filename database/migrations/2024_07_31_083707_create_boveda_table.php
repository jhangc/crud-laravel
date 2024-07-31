<?php
// database/migrations/xxxx_xx_xx_create_boveda_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBovedaTable extends Migration
{
    public function up()
    {
        Schema::create('boveda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id');
            $table->decimal('monto_inicio', 15, 2);
            $table->date('fecha_inicio');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('boveda');
    }
}


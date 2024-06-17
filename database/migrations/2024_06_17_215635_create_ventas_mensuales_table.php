<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ventas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->string('mes')->nullable();
            $table->decimal('porcentaje', 10, 2)->nullable();
            $table->unsignedBigInteger('id_prestamo')->nullable();
            $table->timestamps();
            $table->foreign('id_prestamo')->references('id')->on('prestamos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas_mensuales');
    }
};

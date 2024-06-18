<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos_agricolas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prestamo');
            $table->string('nombre_actividad')->nullable();
            $table->string('unidad_medida_siembra')->nullable();
            $table->string('hectareas')->nullable();
            $table->string('cantidad_cultivar')->nullable();
            $table->string('unidad_medida_venta')->nullable();
            $table->string('rendimiento_unidad_siembra')->nullable();
            $table->string('ciclo_productivo_meses')->nullable();
            $table->string('mes_inicio')->nullable();
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
        Schema::dropIfExists('productos_agricolas');
    }
};

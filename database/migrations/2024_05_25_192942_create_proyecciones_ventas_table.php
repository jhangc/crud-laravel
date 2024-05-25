<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyeccionesVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyecciones_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion_producto');
            $table->string('unidad_medida');
            $table->string('frecuencia_compra');
            $table->integer('unidades_compradas');
            $table->integer('unidades_vendidas');
            $table->integer('stock_verificado');
            $table->decimal('precio_compra', 15, 2);
            $table->decimal('precio_venta', 15, 2);
            $table->unsignedBigInteger('id_prestamo');
            $table->string('estado');
            $table->foreign('id_prestamo')->references('id')->on('prestamos')->onDelete('cascade');
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
        Schema::dropIfExists('proyecciones_ventas');
    }
}


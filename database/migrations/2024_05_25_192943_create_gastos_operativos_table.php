<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosOperativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gastos_operativos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->decimal('precio_unitario', 15, 2);
            $table->integer('cantidad');
            $table->unsignedBigInteger('id_prestamo');
            $table->string('acciones');
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
        Schema::dropIfExists('gastos_operativos');
    }
}
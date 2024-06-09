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
        Schema::table('gasto_producir', function (Blueprint $table) {
            $table->unsignedBigInteger('id_prestamo')->nullable();

            // Si hay una relación con otra tabla, agregar la restricción de clave foránea
            $table->foreign('id_prestamo')->references('id')->on('prestamos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('gasto_producir', function (Blueprint $table) {
            $table->dropForeign(['id_prestamo']);
            $table->dropColumn('id_prestamo');
        });
    }};

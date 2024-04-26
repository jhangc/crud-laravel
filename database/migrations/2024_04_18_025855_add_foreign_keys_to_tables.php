<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadir clave foránea a 'clientes'
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });

        // Añadir clave foránea a 'creditos'
        Schema::table('creditos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });

        // Añadir clave foránea a 'pagos'
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreign('credito_id')->references('id')->on('creditos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Eliminar clave foránea de 'clientes'
         Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
        });

        // Eliminar clave foránea de 'creditos'
        Schema::table('creditos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
        });

        // Eliminar clave foránea de 'pagos'
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['credito_id']);
        });

    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCreditoClienteTable extends Migration
{
    public function up()
    {
        Schema::table('credito_cliente', function (Blueprint $table) {
            // Agregar clave foránea a la tabla 'prestamos'
            $table->foreign('prestamo_id')
                ->references('id')->on('prestamos')
                ->onDelete('cascade');

            // Agregar clave foránea a la tabla 'clientes'
            $table->foreign('cliente_id')
                ->references('id')->on('clientes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('credito_cliente', function (Blueprint $table) {
            // Eliminar clave foránea de la tabla 'prestamos'
            $table->dropForeign(['prestamo_id']);

            // Eliminar clave foránea de la tabla 'clientes'
            $table->dropForeign(['cliente_id']);
        });
    }
}

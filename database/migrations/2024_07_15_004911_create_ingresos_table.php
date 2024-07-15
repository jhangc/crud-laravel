<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresosTable extends Migration
{
    public function up()
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaccion_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('prestamo_id')->nullable();
            $table->unsignedBigInteger('cronograma_id')->nullable();
            $table->integer('numero_cuota')->nullable();
            $table->decimal('monto_cuota', 15, 3)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->time('hora_pago')->nullable();
            $table->decimal('monto', 15, 3)->nullable();
            $table->timestamps();

            $table->foreign('transaccion_id')->references('id')->on('caja_transacciones')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('prestamo_id')->references('id')->on('prestamos')->onDelete('cascade');
            $table->foreign('cronograma_id')->references('id')->on('cronograma')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingresos');
    }
}

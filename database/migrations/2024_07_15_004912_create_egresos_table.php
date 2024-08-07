<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEgresosTable extends Migration
{
    public function up()
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaccion_id')->nullable();
            $table->unsignedBigInteger('prestamo_id')->nullable();
            $table->date('fecha_egreso')->nullable();
            $table->time('hora_egreso')->nullable();
            $table->decimal('monto', 15, 2)->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaccion_id')->references('id')->on('caja_transacciones')->onDelete('cascade');
            $table->foreign('prestamo_id')->references('id')->on('prestamos')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('egresos');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestamosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);
            $table->string('producto', 100);
            $table->string('subproducto', 100)->nullable();
            $table->string('destino', 100)->nullable();
            $table->unsignedBigInteger('id_cliente');
            $table->string('recurrencia', 50)->nullable();
            $table->decimal('tasa', 5, 2)->nullable();
            $table->integer('tiempo')->nullable();
            $table->decimal('monto_total', 15, 2)->nullable();
            $table->date('fecha_desembolso')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('fecha_fin')->useCurrent();
            $table->string('estado', 20)->nullable();
            $table->string('categoria', 20)->nullable();
            $table->string('activo', 1)->nullable();
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
        Schema::dropIfExists('prestamos');
    }
}

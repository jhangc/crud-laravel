<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->onDelete('cascade'); // Relación con la tabla 'prestamos'
            $table->decimal('cuentas_por_cobrar', 15, 2);
            $table->decimal('saldo_en_caja_bancos', 15, 2);
            $table->decimal('adelanto_a_proveedores', 15, 2);
            $table->decimal('otros', 15, 2);
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
        Schema::dropIfExists('activos');
    }
};

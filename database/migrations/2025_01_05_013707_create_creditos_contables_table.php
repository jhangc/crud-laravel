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
        Schema::create('creditos_contables', function (Blueprint $table) {
            $table->id();
            $table->Integer('prestamo_id')->nullable();
            $table->date('fecha_otorgamiento');
            $table->decimal('monto_credito', 15, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('estado')->nullable();// ['Activo', 'Refinanciado', 'Cancelado']
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos_contables');
    }
};

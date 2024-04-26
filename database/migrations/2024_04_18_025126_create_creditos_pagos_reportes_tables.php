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
        // Crear tabla 'creditos'
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->string('tipo_credito', 100);  // Cambiado de enum a string
            $table->decimal('monto', 10, 2);
            $table->decimal('tasa_interes', 5, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado', 100);  // Cambiado de enum a string
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Crear tabla 'pagos'
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credito_id');
            $table->date('fecha_pago');
            $table->decimal('monto_pago', 10, 2);
            $table->decimal('mora', 10, 2)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Crear tabla 'reportes'
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_reporte', 100);  // Cambiado de enum a string
            $table->date('fecha_generacion');
            $table->text('detalles');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('creditos');
    }
};

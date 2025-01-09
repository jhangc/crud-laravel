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
        Schema::create('pagos_contables', function (Blueprint $table) {
            $table->id();
            $table->Integer('ingreso_id')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->decimal('monto_pago', 15, 2)->nullable();
            $table->decimal('monto_interes', 15, 2)->nullable();
            $table->decimal('monto_capital', 15, 2)->nullable();
            $table->string('estado')->nullable();//, 1['Pagado',  2 'Pendiente',3 'Mora']
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_contables');
    }
};

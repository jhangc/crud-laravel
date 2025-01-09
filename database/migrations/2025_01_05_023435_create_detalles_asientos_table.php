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
        Schema::create('detalles_asientos', function (Blueprint $table) {
            $table->id();
            $table->Integer('asiento_contable_id')->nullable();
            $table->Integer('cuenta_id')->nullable();
            $table->decimal('monto', 15, 2);
            $table->Integer('tipo');// 1['Débito',  0'Crédito']
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_asientos');
    }
};

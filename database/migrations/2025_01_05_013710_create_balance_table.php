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
        Schema::create('balance', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->nullable();
            $table->decimal('activo_total', 15, 2)->nullable();
            $table->decimal('pasivo_total', 15, 2)->nullable();
            $table->decimal('patrimonio_total', 15, 2)->nullable();
            $table->Integer('estado')->nullable();// 0['Cerrado', 1'Abierto']
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance');
    }
};

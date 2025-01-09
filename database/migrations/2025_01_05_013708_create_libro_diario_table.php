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
        Schema::create('libro_diario', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('total_debe', 15, 2)->nullable();
            $table->decimal('total_haber', 15, 2)->nullable();
            $table->integer('estado')->nullable();// 1['Cerrado', 0 'Abierto']
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_diario');
    }
};

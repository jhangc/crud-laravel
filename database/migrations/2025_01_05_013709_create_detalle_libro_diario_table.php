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
        Schema::create('detalle_libro_diario', function (Blueprint $table) {
            $table->id();
            $table->Integer('libro_diario_id')->nullable();
            $table->Integer('cuenta_id')->nullable();
            $table->decimal('monto', 15, 2)->nullable();
            $table->Integer('tipo')->nullable();//, 1['Debe',  0'Haber']
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_libro_diario');
    }
};

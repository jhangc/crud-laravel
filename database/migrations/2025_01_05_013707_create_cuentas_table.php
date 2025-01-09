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
        // Migration: cuentas
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->nullable();
            $table->string('nombre');
            $table->string('tipo')->nullable();// ['Activo', 'Pasivo', 'Patrimonio', 'Ingresos', 'Egresos']
            $table->integer('nivel')->nullable();
            $table->integer('estado')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};

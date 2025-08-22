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
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('kilate');     // 14, 16, 18, 21...
            $table->decimal('precio', 10, 2);          // S/ por gramo
            $table->date('fecha');                     // fecha de vigencia del precio
            $table->timestamps();

            // Para evitar duplicados exactos por día y kilate:
            $table->unique(['kilate', 'fecha']);
            $table->index(['kilate', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};

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
        Schema::create('libro_mayor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->nullable();
            $table->string('periodo', 7)->nullable(); // Format: YYYY-MM
            $table->decimal('saldo_inicial', 15, 2)->nullable();
            $table->decimal('total_debe', 15, 2)->nullable();
            $table->decimal('total_haber', 15, 2)->nullable();
            $table->decimal('saldo_final', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_mayor');
    }
};

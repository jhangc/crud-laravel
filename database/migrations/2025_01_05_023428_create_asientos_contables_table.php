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
        
        
            Schema::create('asientos_contables', function (Blueprint $table) {
                $table->id();
                $table->string('codigo_asiento', 50)->unique();
                $table->date('fecha');
                $table->string('descripcion', 255);
                $table->decimal('monto_total', 15, 2);
                $table->string('model', 255)->nullable();
                $table->string('model_id', 255)->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
};

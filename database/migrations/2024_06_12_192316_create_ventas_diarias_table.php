<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
        public function up()
        {
            Schema::create('ventas_diarias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prestamo_id')->constrained('prestamos')->onDelete('cascade'); // Relación con la tabla 'prestamos'
                $table->date('dia');
                $table->integer('cantidad_maxima');
                $table->integer('cantidad_minima');
                $table->decimal('promedio', 8, 2);
                $table->timestamps();
            });
        }
    
        public function down()
        {
            Schema::dropIfExists('ventas_diarias');
        }
    
    
};

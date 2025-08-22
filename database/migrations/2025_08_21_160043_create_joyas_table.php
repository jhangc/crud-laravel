<?php

// database/migrations/2025_08_21_000002_create_credijoya_joyas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('credijoya_joyas', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('prestamo_id')->nullable();
            $t->unsignedTinyInteger('kilate');           // 14, 16, 18, 21
            $t->decimal('precio_gramo', 10, 2);
            $t->decimal('peso_bruto', 10, 3)->nullable();
            $t->decimal('peso_neto', 10, 3);
            $t->unsignedInteger('piezas')->default(1);
            $t->text('descripcion')->nullable();
            $t->decimal('valor_tasacion', 12, 2);
            $t->integer('devuelta')->nullable()->default(0);
            $t->string('codigo')->nullable();
            $t->timestamps();
             $t->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('credijoya_joyas');
    }
};

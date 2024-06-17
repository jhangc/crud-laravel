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
        Schema::table('gastos_operativos', function (Blueprint $table) {
            $table->string('unidad')->nullable();
            $table->string('mes1')->nullable();
            $table->string('mes2')->nullable();
            $table->string('mes3')->nullable();
            $table->string('mes4')->nullable();
            $table->string('mes5')->nullable();
            $table->string('mes6')->nullable();
            $table->string('mes7')->nullable();
            $table->string('mes8')->nullable();
            $table->string('mes9')->nullable();
            $table->string('mes10')->nullable();
            $table->string('mes11')->nullable();
            $table->string('mes12')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos_operativos', function (Blueprint $table) {
            $table->dropColumn('unidad');
            $table->dropColumn('mes1');
            $table->dropColumn('mes2');
            $table->dropColumn('mes3');
            $table->dropColumn('mes4');
            $table->dropColumn('mes5');
            $table->dropColumn('mes6');
            $table->dropColumn('mes7');
            $table->dropColumn('mes8');
            $table->dropColumn('mes9');
            $table->dropColumn('mes10');
            $table->dropColumn('mes11');
            $table->dropColumn('mes12');
        });
    }
};

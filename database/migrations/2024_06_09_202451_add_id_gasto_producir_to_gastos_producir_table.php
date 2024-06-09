<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gastos_producir', function (Blueprint $table) {
            $table->unsignedBigInteger('id_gasto_producir')->nullable();

            $table->foreign('id_gasto_producir')->references('id')->on('gasto_producir')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('gastos_producir', function (Blueprint $table) {
            $table->dropForeign(['id_gasto_producir']);
            $table->dropColumn('id_gasto_producir');
        });
    }
};
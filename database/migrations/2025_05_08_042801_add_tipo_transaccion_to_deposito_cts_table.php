<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposito_cts', function (Blueprint $table) {
            // 1 = ingreso, 2 = egreso
            $table->tinyInteger('tipo_transaccion')
                  ->after('realizado_por')
                  ->default(1)
                  ->comment('1 ingreso, 2 egreso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposito_cts', function (Blueprint $table) {
            $table->dropColumn('tipo_transaccion');
        });
    }
};

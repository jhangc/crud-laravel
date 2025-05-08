<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToDepositoCtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposito_cts', function (Blueprint $table) {
            // 1 = pagado, 2 = pendiente
            $table->tinyInteger('estado')
                  ->after('tipo_transaccion')
                  ->default(1)
                  ->comment('1 pagado, 2 pendiente');
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
            $table->dropColumn('estado');
        });
    }
}

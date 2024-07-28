<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoraAndTotalPagoFieldsToIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->decimal('monto_mora', 10, 2)->nullable()->after('monto');
            $table->integer('dias_mora')->nullable()->after('monto_mora');
            $table->decimal('porcentaje_mora', 5, 2)->nullable()->after('dias_mora');
            $table->decimal('monto_total_pago_final', 10, 2)->nullable()->after('porcentaje_mora');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->dropColumn('monto_mora');
            $table->dropColumn('dias_mora');
            $table->dropColumn('porcentaje_mora');
            $table->dropColumn('monto_total_pago_final');
        });
    }
}

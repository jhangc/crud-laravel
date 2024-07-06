<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCronogramaTable extends Migration
{
    public function up()
    {
        Schema::table('cronograma', function (Blueprint $table) {
            $table->decimal('capital', 15, 2)->after('monto');
            $table->decimal('interes', 15, 2)->after('capital');
            $table->decimal('amortizacion', 15, 2)->after('interes');
            $table->decimal('saldo_deuda', 15, 2)->after('amortizacion');
        });
    }

    public function down()
    {
        Schema::table('cronograma', function (Blueprint $table) {
            $table->dropColumn(['capital', 'interes', 'amortizacion', 'saldo_deuda']);
        });
    }
}

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
        Schema::table('ingresos', function (Blueprint $table) {
            $table->decimal('diferencia', 10, 2)
                ->default(0.00)
                ->after('monto_mora');
        });
    }

    public function down()
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->dropColumn('diferencia');
        });
    }
};

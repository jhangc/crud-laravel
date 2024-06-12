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
        Schema::table('proyecciones_ventas', function (Blueprint $table) {
            $table->decimal('proporcion_ventas', 5, 2)->after('estado'); // Ajusta la posición según sea necesario
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proyecciones_ventas', function (Blueprint $table) {
            $table->dropColumn('proporcion_ventas');
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInventariosAndProyeccionesVentasTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('tipo_inventario')->nullable()->after('unidad');
        });

        Schema::table('proyecciones_ventas', function (Blueprint $table) {
            $table->json('ingredientes')->nullable()->after('proporcion_ventas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn('tipo_inventario');
        });

        Schema::table('proyecciones_ventas', function (Blueprint $table) {
            $table->dropColumn('ingredientes');
        });
    }
}


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('direccion_conyugue')->nullable()->after('dni_conyugue');
            $table->string('numero_dni_aval')->nullable()->after('aval');
            $table->string('direccion_aval')->nullable()->after('dni_aval');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('direccion_conyugue');
            $table->dropColumn('numero_dni_aval');
            $table->dropColumn('direccion_aval');
        });
    }
}


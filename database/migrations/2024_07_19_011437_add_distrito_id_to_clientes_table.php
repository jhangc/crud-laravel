<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistritoIdToClientesTable extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('distrito_id')->nullable()->after('direccion');
            $table->foreign('distrito_id')->references('dis_id')->on('distritos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['distrito_id']);
            $table->dropColumn('distrito_id');
        });
    }
}
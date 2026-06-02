<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reversiones_pagos', function (Blueprint $table) {
            $table->timestamp('restablecido_at')->nullable()->after('detalles');
            $table->unsignedBigInteger('restablecido_por')->nullable()->after('restablecido_at');
            $table->string('motivo_restablecimiento')->nullable()->after('restablecido_por');

            $table->foreign('restablecido_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reversiones_pagos', function (Blueprint $table) {
            $table->dropForeign(['restablecido_por']);
            $table->dropColumn(['restablecido_at', 'restablecido_por', 'motivo_restablecimiento']);
        });
    }
};

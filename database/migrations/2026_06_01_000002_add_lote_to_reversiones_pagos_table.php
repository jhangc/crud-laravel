<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reversiones_pagos', function (Blueprint $table) {
            // Agrupa todos los pagos reversados en una misma operacion (grupo completo).
            // Restablecer cualquiera del lote restablece todo el lote.
            $table->string('lote_reversion')->nullable()->after('detalles')->index();
        });
    }

    public function down()
    {
        Schema::table('reversiones_pagos', function (Blueprint $table) {
            $table->dropColumn('lote_reversion');
        });
    }
};

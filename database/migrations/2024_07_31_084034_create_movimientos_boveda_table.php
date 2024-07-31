<?php

// database/migrations/xxxx_xx_xx_create_movimientos_boveda_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosBovedaTable extends Migration
{
    public function up()
    {
        Schema::create('movimientos_boveda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boveda_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('monto', 15, 2)->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('serie_documento')->nullable();
            $table->string('tipo')->nullable(); // 'ingreso' o 'egreso'
            $table->string('motivo')->nullable();
            $table->text('observacion')->nullable();
            $table->string('archivo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_boveda');
    }
}

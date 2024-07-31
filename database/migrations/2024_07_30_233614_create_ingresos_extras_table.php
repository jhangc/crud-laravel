<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresosExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingresos_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->decimal('monto', 15, 3)->nullable();
            $table->string('motivo')->nullable();
            $table->string('numero_documento')->nullable();
            $table->foreignId('caja_transaccion_id')->constrained('caja_transacciones')->nullable();
            $table->string('serie_documento')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('archivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingresos_extras');
    }
}

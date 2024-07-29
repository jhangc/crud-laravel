<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caja_transaccion_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('monto_gasto', 10, 2)->nullable();
            $table->string('numero_doc')->nullable();
            $table->string('serie_doc')->nullable();
            $table->string('numero_documento_responsable')->nullable();
            $table->string('nombre_responsable')->nullable();
            $table->string('archivo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('caja_transaccion_id')->references('id')->on('caja_transacciones');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos');
    }
}

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInicioOperacionesTable extends Migration
{
    public function up()
    {
        Schema::create('inicio_operaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Usuario que tiene el permiso para abrir la caja
            $table->unsignedBigInteger('sucursal_id')->nullable(); // Sucursal relacionada
            $table->boolean('permiso_abierto')->default(false); // Permiso para abrir la caja
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inicio_operaciones');
    }
}

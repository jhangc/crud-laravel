<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->string('nombre')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas');
    }
}

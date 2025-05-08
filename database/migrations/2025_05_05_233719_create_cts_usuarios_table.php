<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtsUsuariosTable extends Migration
{
    public function up()
    {
        Schema::create('cts_usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');                   // id del trabajador
            $table->string('numero_cuenta', 16)->unique();           // [4seq][8dni][4rand]
            $table->decimal('saldo_disponible', 15, 2)->default(0);  // saldo que ve el usuario
            $table->timestamp('fecha_ultimo_deposito')->nullable();  // para control semestral
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cts_usuarios');
    }
}

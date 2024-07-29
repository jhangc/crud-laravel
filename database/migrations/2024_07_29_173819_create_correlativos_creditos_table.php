<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrelativosCreditosTable extends Migration
{
    public function up()
    {
        Schema::create('correlativos_creditos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prestamo');
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->string('serie', 4);
            $table->string('correlativo', 10);
            $table->timestamps();

           
        });
    }

    public function down()
    {
        Schema::dropIfExists('correlativos_creditos');
    }
}

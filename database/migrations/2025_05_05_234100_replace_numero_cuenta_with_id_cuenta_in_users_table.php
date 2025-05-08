<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceNumeroCuentaWithIdCuentaInUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Primero eliminamos la columna antigua
            $table->dropColumn('numero_cuenta');

            // Luego agregamos la nueva columna id_cuenta
            $table->unsignedBigInteger('id_cuenta')
                  ->nullable()
                  ->after('dni');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // En el rollback, eliminamos id_cuenta...
            $table->dropColumn('id_cuenta');

            // ...y restauramos numero_cuenta como string(16)
            $table->string('numero_cuenta', 16)
                  ->nullable()
                  ->after('dni');
        });
    }
}

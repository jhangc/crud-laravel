<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni', 8)->nullable()->after('telefono');
            $table->string('numero_cuenta', 16)->nullable()->after('dni');
            $table->date('fecha_nacimiento')->nullable()->after('numero_cuenta');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dni', 'numero_cuenta', 'fecha_nacimiento']);
        });
    }
}

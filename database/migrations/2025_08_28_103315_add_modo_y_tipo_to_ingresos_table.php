<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('ingresos', function (Blueprint $t) {
            if (!Schema::hasColumn('ingresos', 'interes_pagado')) $t->decimal('interes_pagado', 12, 2)->default(0);
            if (!Schema::hasColumn('ingresos', 'capital_pagado')) $t->decimal('capital_pagado', 12, 2)->default(0);
            if (!Schema::hasColumn('ingresos', 'modo')) $t->string('modo', 20)->nullable();   
            if (!Schema::hasColumn('ingresos', 'nuevo_id')) $t->unsignedBigInteger('nuevo_id')->nullable();
            if (!Schema::hasColumn('ingresos', 'tipo')) $t->string('tipo', 20)->nullable(); 
           
        });
    }

    public function down(): void {
        Schema::table('ingresos', function (Blueprint $t) {
            // Nota: normalmente no se borran columnas con datos históricos, pero si necesitas:
            $t->dropColumn(['interes_pagado','capital_pagado','modo','tipo','nuevo_id']);
        });
    }
};

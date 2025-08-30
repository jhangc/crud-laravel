<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('credijoya_joyas', function (Blueprint $t) {
            // Fecha en que se realizó el pago (para contar los 15 días gratis)
            $t->dateTime('fecha_pago')->nullable()->after('codigo');

            // Fecha en que se devolvió la(s) joya(s)
            $t->dateTime('fecha_devolucion')->nullable()->after('fecha_pago');

            // Monto cobrado por resguardo (si aplica)
            $t->decimal('monto_resguardo', 12, 2)->nullable()->default(0)->after('fecha_devolucion');
        });
    }

    public function down(): void {
        Schema::table('credijoya_joyas', function (Blueprint $t) {
            $t->dropColumn(['fecha_pago', 'fecha_devolucion', 'monto_resguardo']);
        });
    }
};

<?php

// database/migrations/2025_08_21_000001_add_credijoya_fields_to_prestamos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('prestamos', function (Blueprint $t) {
            // Identificadores del producto (pueden existir ya; si no, se agregan)

            // Parámetros CrediJoya (detalles dentro del crédito)
           
            if (!Schema::hasColumn('prestamos', 'tasacion_total'))   $t->decimal('tasacion_total', 12, 2)->default(0);
            if (!Schema::hasColumn('prestamos', 'monto_max_80'))     $t->decimal('monto_max_80', 12, 2)->default(0);        
            if (!Schema::hasColumn('prestamos', 'itf_desembolso'))   $t->decimal('itf_desembolso', 12, 2)->default(0);
            if (!Schema::hasColumn('prestamos', 'neto_recibir'))     $t->decimal('neto_recibir', 12, 2)->default(0);
            if (!Schema::hasColumn('prestamos', 'proximo_vencimiento')) $t->date('proximo_vencimiento')->nullable();

            // Deuda previa (resultado de cálculo; opcional guardar para auditoría)
            if (!Schema::hasColumn('prestamos', 'deuda_prev_modo'))  $t->string('deuda_prev_modo')->nullable();
            if (!Schema::hasColumn('prestamos', 'deuda_prev_monto')) $t->decimal('deuda_prev_monto', 12, 2)->default(0);
        });
    }

    public function down(): void {
        Schema::table('prestamos', function (Blueprint $t) {
            $cols = [
                'tasacion_total','monto_max_80',
                'itf_desembolso','neto_recibir','proximo_vencimiento',
                'deuda_prev_modo','deuda_prev_monto'
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('prestamos', $c)) $t->dropColumn($c);
            }
        });
    }
};

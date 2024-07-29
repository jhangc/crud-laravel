<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\credito;
use App\Models\CorrelativoCredito;

class FillCorrelativosForActiveGroupLoans extends Migration
{
    public function up()
    {
        // Retrieve all paid group loans sorted by the oldest first
        $creditos = credito::where('estado', 'pagado')
                    ->where('producto', 'grupal')
                    ->where('activo', 1)
                    ->orderBy('fecha_desembolso')
                    ->get();

        foreach ($creditos as $credito) {
            CorrelativoCredito::generateCorrelativosGrupales($credito->id);
        }
    }

    public function down()
    {
        // You can implement logic to reverse the migration if necessary
    }
}


<?php
namespace App\Http\Controllers;

use App\Exports\ClientesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PrestamosExport;
use Illuminate\Http\Request;
use App\Exports\CreditosIndividualesExport;
use App\Exports\CreditosGrupalesExport;

class ExcelController extends Controller
{
    public function export()
    {
        return Excel::download(new ClientesExport, 'clientes.xlsx');
    }

    public function exportPrestamosActivos()
    {
        return Excel::download(new PrestamosExport, 'prestamosactivos.xlsx');
    }

    public function exportarCreditoIndividual()
    {
        return Excel::download(new CreditosIndividualesExport, 'creditos_individuales.xlsx');
    }

    public function exportarCreditoGrupal()
    {
        return Excel::download(new CreditosGrupalesExport, 'creditos_grupales.xlsx');
    }
}

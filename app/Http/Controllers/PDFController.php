<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {
        // Renderiza la vista actual con los datos actuales
        $html = view('ruta.a.tu.vista', $request->all())->render();

        // Genera el PDF
        $pdf = PDF::loadHTML($html);

        return $pdf->stream('documento.pdf');
    }
}
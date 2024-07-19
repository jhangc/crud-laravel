<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }
    public function viewreportecliente()
    {
        // Obtener solo los clientes activos (activo = 1)
        $clientes = Cliente::where('activo', 1)->get();
        return view('admin.reportes.clientes', ['clientes' => $clientes]);

    }

    public function viewprestamosactivos()
    {
        return view('admin.reportes.prestamosactivos');
    }

    public function viewprestamosvencidos()
    {
        return view('admin.reportes.prestamosvencidos');
    }

    
}

//dsadasdas

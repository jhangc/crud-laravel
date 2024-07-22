<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\credito;
use App\Models\CreditoCliente;

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
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Administrador')) {
            // Si es administrador o cajera, obtener todos los créditos activos
            $creditos = credito::with('clientes')
                ->where('activo', 1)
                ->where('estado', 'pagado')
                ->get();
        } else {
            // Si no es administrador, obtener solo los créditos registrados por el usuario
            $creditos = credito::with('clientes')->where('activo', 1)->where('user_id', $user->id)->get();
        }
        return view('admin.reportes.prestamosactivos', ['creditos' => $creditos]);
    }


    public function viewprestamosvencidos()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Administrador')) {
            // Si es administrador, obtener todos los créditos activos y vencidos
            $creditos = credito::with('clientes')
                ->where('activo', 1)
                ->whereDate('fecha_fin', '<', now()->toDateString())
                ->get();
        } else {
            // Si no es administrador, obtener solo los créditos registrados por el usuario y vencidos
            $creditos = credito::with('clientes')
                ->where('activo', 1)
                ->where('user_id', $user->id)
                ->whereDate('fecha_fin', '<', now()->toDateString())
                ->get();
        }

        return view('admin.reportes.prestamosvencidos', ['creditos' => $creditos]);
    }
}

//dsadasdas

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
    public function viewreportecreditoindividual()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Administrador')) {
            // Si es administrador o cajera, obtener todos los créditos activos y que no sean grupales
            $creditos = Credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma',
                'correlativoPagare',
                'garantia',
                'ingresos'
            ])
                ->withCount('creditoClientes as cliente_creditos_count')
                ->where('activo', 1)
                ->where('estado', 'pagado')
                ->where('producto', '!=', 'grupal')
                ->get();
        } else {
            // Si no es administrador, obtener solo los créditos registrados por el usuario y que no sean grupales
            $creditos = Credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma',
                'correlativoPagare',
                'garantia',
                'ingresos'
            ])
                ->withCount('creditoClientes as cliente_creditos_count')
                ->where('activo', 1)
                ->where('estado', 'pagado')
                ->where('producto', '!=', 'grupal')
                ->where('user_id', $user->id) // Filtrar por el usuario autenticado
                ->get();
        }

        //dd($creditos);

        return view('admin.reportes.creditoindividual', ['creditos' => $creditos]);
    }

    public function viewreportecreditogrupal()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Administrador')) {
            // Si es administrador o cajera, obtener todos los créditos activos y que no sean grupales
            $creditos = Credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma' => function($query) {
                        $query->whereNull('cliente_id'); // Filtro para cuotas generales
                    },
                'garantia',
                'correlativos' => function ($query) {
                    $query->whereNull('id_cliente');
                },
                'ingresos'
            ])
                ->where('activo', 1)
                ->where('estado', 'pagado')
                ->where('producto', 'grupal')
                ->get();
        } else {
            // Si no es administrador, obtener solo los créditos registrados por el usuario y que no sean grupales
            $creditos = Credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma' => function($query) {
                        $query->whereNull('cliente_id'); // Filtro para cuotas generales
                    },
                'correlativoPagare',
                'garantia',
                'correlativos' => function ($query) {
                    $query->whereNull('id_cliente');
                },
                'ingresos'
            ])
                ->where('activo', 1)
                ->where('estado', 'pagado')
                ->where('producto', 'grupal')
                ->where('user_id', $user->id) // Filtrar por el usuario autenticado
                ->get();
        }

        //dd($creditos);

        return view('admin.reportes.creditogrupal', ['creditos' => $creditos]);
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

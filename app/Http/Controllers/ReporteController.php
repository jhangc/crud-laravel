<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\credito;
use App\Models\CreditoCliente;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // app/Http/Controllers/ReporteController.php

    public function viewreporteinteresesmensual(Request $request)
    {
        // Si no se envía 'fecha', usamos la fecha actual.
        $fecha = $request->input('fecha', date('Y-m-d'));
        $año = \Carbon\Carbon::parse($fecha)->year;

        // Subconsulta: Agrupar registros de 'cronograma' por id_prestamo
        $cronogramaSubquery = credito::select(
            'id_prestamo',
            DB::raw('MIN(cliente_id) as cliente_id'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 1 THEN interes ELSE 0 END) AS enero'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 2 THEN interes ELSE 0 END) AS febrero'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 3 THEN interes ELSE 0 END) AS marzo'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 4 THEN interes ELSE 0 END) AS abril'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 5 THEN interes ELSE 0 END) AS mayo'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 6 THEN interes ELSE 0 END) AS junio'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 7 THEN interes ELSE 0 END) AS julio'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 8 THEN interes ELSE 0 END) AS agosto'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 9 THEN interes ELSE 0 END) AS septiembre'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 10 THEN interes ELSE 0 END) AS octubre'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 11 THEN interes ELSE 0 END) AS noviembre'),
            DB::raw('SUM(CASE WHEN MONTH(fecha) = 12 THEN interes ELSE 0 END) AS diciembre'),
            DB::raw('SUM(interes) AS total_interes')
        )
            ->whereYear('fecha', $año)
            ->groupBy('id_prestamo');

        // Consulta principal: Unir la subconsulta con 'prestamos' y 'clientes'
        $reporte = credito::joinSub($cronogramaSubquery, 't', function ($join) {
            $join->on('prestamos.id', '=', 't.id_prestamo');
        })
            ->leftJoin('clientes as cl', 't.cliente_id', '=', 'cl.id')
            ->select(
                't.id_prestamo',
                DB::raw("CASE WHEN prestamos.producto = 'grupal' THEN prestamos.nombre_prestamo ELSE cl.nombre END as nombre_credito"),
                DB::raw("CASE WHEN prestamos.producto = 'grupal' THEN 'grupal' ELSE 'individual' END as tipo_credito"),
                't.enero',
                't.febrero',
                't.marzo',
                't.abril',
                't.mayo',
                't.junio',
                't.julio',
                't.agosto',
                't.septiembre',
                't.octubre',
                't.noviembre',
                't.diciembre',
                't.total_interes'
            )
            ->where('prestamos.estado', 'pagado')
            ->orderBy('t.id_prestamo', 'asc')
            ->get();

        // Calcular totales por mes
        $totalesMeses = [
            'enero'         => $reporte->sum('enero'),
            'febrero'       => $reporte->sum('febrero'),
            'marzo'         => $reporte->sum('marzo'),
            'abril'         => $reporte->sum('abril'),
            'mayo'          => $reporte->sum('mayo'),
            'junio'         => $reporte->sum('junio'),
            'julio'         => $reporte->sum('julio'),
            'agosto'        => $reporte->sum('agosto'),
            'septiembre'    => $reporte->sum('septiembre'),
            'octubre'       => $reporte->sum('octubre'),
            'noviembre'     => $reporte->sum('noviembre'),
            'diciembre'     => $reporte->sum('diciembre'),
            'total_interes' => $reporte->sum('total_interes')
        ];

        return view('admin.reportes.interesmensual', compact('reporte', 'totalesMeses', 'fecha'));
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
                'cronograma' => function ($query) {
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
                'cronograma' => function ($query) {
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

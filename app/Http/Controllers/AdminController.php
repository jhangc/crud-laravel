<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\credito;
use App\Models\Cronograma;
use App\Models\Caja;
use App\Models\cliente;
use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\CreditoCliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Gasto;
use App\Models\IngresoExtra;
use App\Models\Boveda;
use App\Models\MovimientoBoveda;
use App\Models\CajaTransaccion;
use App\Models\DepositoCts;

class AdminController extends Controller
{
    public function index()
    {
        $usuarios = User::all();


        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener todos los roles del usuario autenticado
        $roles = $user->roles->pluck('name');

        // Verificar si el usuario tiene alguno de los roles
        if ($roles->contains('Asesor de creditos')) {
            // Obtener los IDs de créditos con estado pagado y registrados por el asesor de crédito actual
            $idsCreditosPagados = credito::where('estado', 'pagado')
                ->where('user_id', $user->id)
                ->pluck('id')
                ->toArray();

            $creditosPagadosCount = count($idsCreditosPagados);
        } else {
            $idsCreditosPagados = credito::where('estado', 'pagado')->pluck('id')->toArray();
            $creditosPagadosCount = count($idsCreditosPagados);
        }


        // Obtener los id_cronograma de la tabla ingresos
        $idsCronogramaEnIngresos = Ingreso::pluck('cronograma_id')->toArray();

        // Contar las cuotas vencidas
        $cuotasVencidasCount = Cronograma::whereNotIn('id', $idsCronogramaEnIngresos)
            ->whereIn('id_prestamo', $idsCreditosPagados)
            ->where('fecha', '<', now())
            ->count();

        // Obtener el conteo de clientes activos
        $clientesActivosCount = cliente::where('activo', 1)->count();

        // Calcular la suma de todos los egresos menos los ingresos
        $totalIngresos = Ingreso::whereNotNull('cliente_id')->sum('monto'); // Ajusta 'monto' según el nombre de tu campo de cantidad en la tabla ingresos
        $totalEgresos = Egreso::sum('monto');  // Ajusta 'monto' según el nombre de tu campo de cantidad en la tabla egresos
        $balance = $totalEgresos - $totalIngresos;

        // Obtener la última transacción de caja de la sucursal del usuario
        $sucursalId = Auth::user()->sucursal_id;
        $ultimaTransaccion = CajaTransaccion::where('sucursal_id', $sucursalId)
            ->orderBy('created_at', 'desc')
            ->first();

        $ultimaCajaTransacciones = null;
        $ultimaCajaSaldo = null;
        $nombreCaja = null;

        if ($ultimaTransaccion) {
            $caja = $ultimaTransaccion->caja;
            $nombreCaja = $caja->nombre;

            $ingresosCaja = Ingreso::where('transaccion_id', $ultimaTransaccion->id)->sum('monto');
            $egresosCaja = Egreso::where('transaccion_id', $ultimaTransaccion->id)->sum('monto');
            $ingresosExtrasCaja = IngresoExtra::where('caja_transaccion_id', $ultimaTransaccion->id)->sum('monto');
            $gastosCaja = Gasto::where('caja_transaccion_id', $ultimaTransaccion->id)->sum('monto_gasto');
            $ultimaCajaSaldo = $ultimaTransaccion->monto_apertura + $ingresosCaja + $ingresosExtrasCaja - $egresosCaja - $gastosCaja;

            $ultimaCajaTransacciones = [
                'ingresos' => $ingresosCaja ?? 0,
                'ingresos_extras' => $ingresosExtrasCaja ?? 0,
                'egresos' => $egresosCaja ?? 0,
                'gastos' => $gastosCaja ?? 0,
                'saldo' => $ultimaCajaSaldo ?? 0,
            ];
        }

        // Obtener la última bóveda de la sucursal del usuario
        $ultimaBoveda = Boveda::where('sucursal_id', $sucursalId)
            ->orderBy('created_at', 'desc')
            ->first();

        $ultimaBovedaSaldo = null;

        if ($ultimaBoveda) {
            $movimientosBoveda = MovimientoBoveda::where('boveda_id', $ultimaBoveda->id)->get();
            $totalIngresosBoveda = $movimientosBoveda->where('tipo', 'ingreso')->sum('monto');
            $totalEgresosBoveda = $movimientosBoveda->where('tipo', 'egreso')->sum('monto');
            $ultimaBovedaSaldo = $ultimaBoveda->monto_inicio + $totalIngresosBoveda - $totalEgresosBoveda;
        }

        $totalIngresos = DepositoCts::where('tipo_transaccion', 1)
            ->sum('monto');

        // 2) Suma de todos los egresos CTS ya pagados (tipo_transaccion = 2, estado = 1)
        $totalEgresosPagados = DepositoCts::where('tipo_transaccion', 2)
            ->where('estado', 1)
            ->sum('monto');

        // 3) Balance neto
        $balanceGeneral = $totalIngresos - $totalEgresosPagados;
        return view('admin.index', [
            'usuarios' => $usuarios,
            'creditosPagadosCount' => $creditosPagadosCount,
            'cuotasVencidasCount' => $cuotasVencidasCount,
            'clientesActivosCount' => $clientesActivosCount,
            'balance' => $balance,
            'ultimaCajaTransacciones' => $ultimaCajaTransacciones,
            'ultimaBovedaSaldo' => $ultimaBovedaSaldo,
            'nombreCaja' => $nombreCaja,
            'balanceGeneralcts' => $balanceGeneral,
        ]);
    }



    public function aprobar(Request $request)
    {
        $credito = credito::find($request->id);
        $credito->estado = 'aprobado';
        $credito->comentario_administrador = $request->comentarioadministrador;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.aprobar'),
            'mensaje' => 'El crédito ha sido aprobado correctamente',
            'icono' => 'success'
        ]);
    }

    public function rechazar(Request $request)
    {
        $credito = credito::find($request->id);
        $credito->estado = 'rechazado';
        $credito->comentario_administrador = $request->comentarioadministrador;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.aprobar'),
            'mensaje' => 'El crédito ha sido rechazado correctamente',
            'icono' => 'success'
        ]);
    }

    public function observar(Request $request)
    {
        $credito = credito::find($request->id);
        $credito->estado = 'observado';
        $credito->comentario_administrador = $request->comentarioadministrador;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.aprobar'),
            'mensaje' => 'El crédito ha sido observado correctamente',
            'icono' => 'success'
        ]);
    }

    public function guardar(Request $request)
    {
        $credito = credito::find($request->id);
        if ($request->estado == 'rechazado por sistema') {
            $credito->estado = 'rechazado por sistema';
        } else {
            $credito->estado = 'revisado';
        }
        $credito->comentario_asesor = $request->comentario;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.index'),
            'mensaje' => 'El crédito ha sido ' . ($request->estado == 'rechazado por sistema' ? 'rechazado por el sistema' : 'revisado') . ' correctamente',
            'icono' => 'success'
        ]);
    }

    public function ingresosday(Request $request)
    {
        $sucursalId = Auth::user()->sucursal_id;  // Obtener la sucursal del usuario logueado
        $cajas = Caja::where('sucursal_id', $sucursalId)->get();
        return view('admin.creditos.ingresosday', compact('cajas'));
    }
    public function egresosday(Request $request) {}


    public function obtenerTransaccionesCaja($id)
    {
        $caja = Caja::findOrFail($id);
        $today = Carbon::today();

        // Verificar si la caja tiene una transacción abierta o cerrada hoy
        $ultimaTransaccion = $caja->transacciones()
            // ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')->first();
        if (!$ultimaTransaccion) {
            return response()->json([
                'success' => false,
                'message' => 'No hay transacciones abiertas para esta caja en el día de hoy.'
            ]);
        }

        $cajaCerrada = $ultimaTransaccion->hora_cierre ? true : false;
        $ingresos = Ingreso::where('transaccion_id', $ultimaTransaccion->id)
            ->with('cliente', 'transaccion.user') // Incluir relaciones
            ->whereNotNull('cliente_id')
            ->get();

        $egresos = Egreso::where('transaccion_id', $ultimaTransaccion->id)
            ->with(['prestamo.clientes', 'transaccion.user']) // Incluir relaciones
            ->get();

        $gastos = Gasto::where('caja_transaccion_id', $ultimaTransaccion->id)
            ->with('user') // Incluir relación con el usuario
            ->get();

        $ingresosExtras = IngresoExtra::where('caja_transaccion_id', $ultimaTransaccion->id)
            ->with('user') // Incluir relación con el usuario
            ->get();

        // Preparar datos de egresos con clientes
        $egresosConClientes = $egresos->map(function ($egreso) {
            return [
                'hora_egreso' => $egreso->hora_egreso,
                'monto' => $egreso->monto,
                'clientes' => $egreso->prestamo->clientes->pluck('nombre')->toArray(),
                'usuario' => $egreso->transaccion->user->name
            ];
        });

        // Preparar datos de gastos
        $gastosConDetalles = $gastos->map(function ($gasto) {
            return [
                'hora_gasto' => $gasto->created_at->format('H:i:s'),
                'monto' => $gasto->monto_gasto,
                'numero_documento' => $gasto->numero_doc,
                'usuario' => $gasto->user->name
            ];
        });

        // Preparar datos de ingresos extras
        $ingresosExtrasConDetalles = $ingresosExtras->map(function ($ingresoExtra) {
            return [
                'hora_ingreso' => $ingresoExtra->created_at->format('H:i:s'),
                'monto' => $ingresoExtra->monto,
                'motivo' => $ingresoExtra->motivo,
                'numero_documento' => $ingresoExtra->numero_documento . '-' . $ingresoExtra->serie_documento,
                'usuario' => $ingresoExtra->user->name
            ];
        });

        $datosCierre = null;
        $desajuste = null;
        if ($cajaCerrada) {
            $datosCierre = json_decode($ultimaTransaccion->json_cierre, true);

            // Calcular el saldo final real
            $saldoEfectivo = array_sum(array_map(function ($cantidad, $valor) {
                return $cantidad * $valor;
            }, $datosCierre['billetes'], array_keys($datosCierre['billetes'])));

            $saldoEfectivo += array_sum(array_map(function ($cantidad, $valor) {
                return $cantidad * $valor;
            }, $datosCierre['monedas'], array_keys($datosCierre['monedas'])));

            $saldoDepositos = $datosCierre['depositos'];
            $saldoFinalReal = $saldoDepositos + $saldoEfectivo;
            // Calcular el saldo final esperado
            $saldoFinalEsperado = $ultimaTransaccion->monto_apertura + $ingresos->sum('monto') - $egresos->sum('monto') - $gastos->sum('monto_gasto') + $ingresosExtras->sum('monto');

            $desajuste =  $saldoFinalReal - $saldoFinalEsperado;

            // Formatear valores a dos decimales
            $saldoFinalReal = number_format($saldoFinalReal, 2);
            $saldoFinalEsperado = number_format($saldoFinalEsperado, 2);
            $desajuste = number_format($desajuste, 2);
        }

        //  dd($saldoFinalEsperado);

        return response()->json([
            'success' => true,
            'ingresos' => $ingresos,
            'egresos' => $egresosConClientes,
            'gastos' => $gastosConDetalles,
            'ingresosExtras' => $ingresosExtrasConDetalles,
            'cajaCerrada' => $cajaCerrada,
            'datosCierre' => $datosCierre ?? [],
            'saldoFinalReal' => $saldoFinalReal ?? 0,
            'saldoFinalEsperado' => $saldoFinalEsperado ?? 0,
            'desajuste' => $desajuste ?? 0,
            'saldoEfectivo' => $saldoEfectivo ?? 0,
            'saldoDepositos' => $saldoDepositos ?? 0,
        ]);
    }
    public function resetCaja($id)
    {
        $caja = Caja::findOrFail($id);
        $today = Carbon::today();

        // Verificar si la caja tiene una transacción abierta o cerrada hoy
        $ultimaTransaccion = $caja->transacciones()
            // ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')->first();
        if (!$ultimaTransaccion) {
            return response()->json([
                'success' => false,
                'message' => 'No hay transacciones abiertas para esta caja en el día de hoy.'
            ]);
        }
        $ultimaTransaccion->hora_cierre = null;
        $ultimaTransaccion->fecha_cierre = null;
        $ultimaTransaccion->monto_cierre = null;
        $ultimaTransaccion->json_cierre = null;
        $ultimaTransaccion->save();

        return response()->json([
            'success' => true,
            'message' => 'Caja ,para volver a Llenar Arqueo , Indique a Cajera que vuelva  a recargar la Pagina.'
        ]);
    }
}

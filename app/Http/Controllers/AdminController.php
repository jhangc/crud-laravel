<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\credito;
use App\Models\Caja;
use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\CreditoCliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Gasto;

class AdminController extends Controller
{
    public function index(){
        $usuarios = User::all();
        return view('admin.index',['usuarios'=>$usuarios]);
    }

    public function aprobar(Request $request){
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

    public function rechazar(Request $request){
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

    public function observar(Request $request){
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

    public function guardar(Request $request){
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

    public function ingresosday (Request $request){
        $sucursalId = Auth::user()->sucursal_id;  // Obtener la sucursal del usuario logueado
        $cajas = Caja::where('sucursal_id', $sucursalId)->get();
        return view('admin.creditos.ingresosday', compact('cajas'));
        
    }
    public function egresosday (Request $request){
        
    }
   

    public function obtenerTransaccionesCaja($id)
    {
        $caja = Caja::findOrFail($id);
        $today = Carbon::today();

        // Verificar si la caja tiene una transacción abierta o cerrada hoy
        $ultimaTransaccion = $caja->transacciones()->whereDate('created_at', $today)->orderBy('created_at', 'desc')->first();
        if (!$ultimaTransaccion) {
            return response()->json([
                'success' => false,
                'message' => 'No hay transacciones abiertas para esta caja en el día de hoy.'
            ]);
        }

        $cajaCerrada = $ultimaTransaccion->hora_cierre ? true : false;
        $ingresos = Ingreso::where('transaccion_id', $ultimaTransaccion->id)
                            ->with('cliente', 'transaccion.user') // Incluir relaciones
                            ->get();

        $egresos = Egreso::where('transaccion_id', $ultimaTransaccion->id)
                          ->with(['prestamo.clientes', 'transaccion.user']) // Incluir relaciones
                          ->get();

        $gastos = Gasto::where('caja_transaccion_id', $ultimaTransaccion->id)
                          ->with('user') // Incluir relación con el usuario
                          ->get();

        // Preparar datos de egresos con clientes
        $egresosConClientes = $egresos->map(function($egreso) {
            return [
                'hora_egreso' => $egreso->hora_egreso,
                'monto' => $egreso->monto,
                'clientes' => $egreso->prestamo->clientes->pluck('nombre')->toArray(),
                'usuario' => $egreso->transaccion->user->name
            ];
        });

        // Preparar datos de gastos
        $gastosConDetalles = $gastos->map(function($gasto) {
            return [
                'hora_gasto' => $gasto->created_at->format('H:i:s'),
                'monto' => $gasto->monto_gasto,
                'numero_documento' => $gasto->numero_doc,
                'usuario' => $gasto->user->name
            ];
        });

        $datosCierre = null;
        $desajuste = null;
        if ($cajaCerrada) {
            $datosCierre = json_decode($ultimaTransaccion->json_cierre, true);

            // Calcular el saldo final real
            $saldoFinalReal = array_sum(array_map(function($cantidad, $valor) {
                return $cantidad * $valor;
            }, $datosCierre['billetes'], array_keys($datosCierre['billetes'])));

            $saldoFinalReal += array_sum(array_map(function($cantidad, $valor) {
                return $cantidad * $valor;
            }, $datosCierre['monedas'], array_keys($datosCierre['monedas'])));

            $saldoFinalReal += $datosCierre['depositos'];

            // Calcular el saldo final esperado
            $saldoFinalEsperado = $ultimaTransaccion->monto_apertura + $ingresos->sum('monto') - $egresos->sum('monto') - $gastos->sum('monto_gasto');

            $desajuste =  $saldoFinalReal-$saldoFinalEsperado;

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
            'cajaCerrada' => $cajaCerrada,
            'datosCierre' => $datosCierre??[],
            'saldoFinalReal' => $saldoFinalReal??0,
            'saldoFinalEsperado' => $saldoFinalEsperado??0,
            'desajuste' => $desajuste??0
        ]);
    }
}

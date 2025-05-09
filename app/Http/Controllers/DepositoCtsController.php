<?php

namespace App\Http\Controllers;

use App\Models\CajaTransaccion;
use App\Models\CtsUsuario;
use App\Models\DepositoCts;
use App\Models\InicioDesembolso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DepositoCtsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1) Carga todos los depósitos con su cuenta CTS y usuario responsable
        $depositos = DepositoCts::with(['ctsUsuario.user', 'realizadoPor'])
            ->orderBy('fecha_deposito', 'desc')
            ->where('tipo_transaccion', 1)
            ->get();

        // 2) Carga todas las cuentas CTS para el select del formulario
        $cuentas = CtsUsuario::with('user')->get();

        // 3) Retorna la vista con ambos conjuntos de datos
        return view('admin.caja.desembolso_cts', compact('depositos', 'cuentas'));
    }

    public function desembolsar()
    {
        // 1) Carga todos los depósitos con su cuenta CTS y usuario responsable
        $depositos = DepositoCts::with(['ctsUsuario.user', 'realizadoPor'])
            ->where('tipo_transaccion', 2)                  // Sólo los movimientos con estado = 1 (pagado)
            ->orderBy('fecha_deposito', 'desc')
            ->get();

        $tienePermisoAbierto = InicioDesembolso::where('permiso_abierto', 1)
            ->exists();


        // 3) Retorna la vista con ambos conjuntos de datos
        return view('admin.caja.pagar_cts_efectivo', compact('depositos','tienePermisoAbierto'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación común
        $request->validate([
            'id'              => 'nullable|exists:deposito_cts,id',
            'cts_usuario_id'  => 'required|exists:cts_usuarios,id',
            'monto'           => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user();

        // Si viene id, buscamos el depósito existente; si no, creamos uno nuevo
        if ($request->filled('id')) {
            $deposito = DepositoCts::findOrFail($request->id);
            // Calcula diferencia para ajustar saldo
            $diferencia = $request->monto - $deposito->monto;
        } else {
            $deposito = new DepositoCts();
            $diferencia = $request->monto; // todo el monto entra en saldo
            // Campos que solo se asignan al crear
            $deposito->realizado_por       = $user->id;
            $deposito->caja_transaccion_id = null;
        }

        $cajaTransaccion = CajaTransaccion::where('user_id', $user->id)
            ->latest()
            ->first();

        // Asignaciones comunes a create y update
        $deposito->cts_usuario_id  = $request->cts_usuario_id;
        $deposito->monto           = $request->monto;
        $deposito->caja_transaccion_id  = $cajaTransaccion->id;
        $deposito->tipo_transaccion  = 1;
        $deposito->estado  = 1;
        $deposito->fecha_deposito  = now();
        $deposito->save();

        // Ajusta el saldo disponible en la CTS
        // Actualiza la cuenta CTS
        $cts = CtsUsuario::findOrFail($request->cts_usuario_id);
        // 1) Ajusta el saldo disponible
        $cts->increment('saldo_disponible', $diferencia);
        // 2) Actualiza la fecha del último depósito
        $cts->fecha_ultimo_deposito = now();
        $cts->save();

        return response()->json([
            'success' => true,
            'message' => $request->filled('id')
                ? 'Depósito actualizado correctamente'
                : 'Depósito registrado correctamente',
            'data'    => $deposito,
        ]);
    }


    public function storeSolicitud(Request $request)
    {
        // Validación
        $request->validate([
            'cts_usuario_id'   => 'required|exists:cts_usuarios,id',
            'monto'            => 'required|numeric|min:0.01',
            'tipo_transaccion' => 'required|in:1,2',
            'estado'           => 'required|in:1,2',
        ]);

        $user = Auth::user();

        // Creamos el registro del movimiento
        $deposito = new DepositoCts();
        $deposito->cts_usuario_id      = $request->cts_usuario_id;
        $deposito->realizado_por       = null;
        $deposito->caja_transaccion_id = null;
        $deposito->monto               = $request->monto;
        $deposito->tipo_transaccion    = $request->tipo_transaccion;
        $deposito->estado              = $request->estado;
        $deposito->fecha_deposito      = now();
        $deposito->save();

        // Ajustamos el saldo CTS
        $cts = CtsUsuario::findOrFail($request->cts_usuario_id);
        if ($cts->saldo_disponible < $request->monto) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente para realizar el retiro'
            ], 400);
        }
        $cts->decrement('saldo_disponible', $request->monto);
        $cts->save();

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de retiro registrada, pendiente de aprobación',
            'data'    => $deposito,
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Busca el depósito o falla con 404
        $deposito = DepositoCts::findOrFail($id);

        // Retorna solo los campos que usa el formulario
        return response()->json([
            'data' => [
                'id'               => $deposito->id,
                'cts_usuario_id'   => $deposito->cts_usuario_id,
                'monto'            => $deposito->monto,
                'fecha_deposito'   => $deposito->fecha_deposito,
            ]
        ]);
    }

    public function pagarDesembolso($id)
    {
        $desembolso = DepositoCts::find($id);

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener la sucursal del usuario autenticado
        $sucursal_id = $user->sucursal_id;

        // Obtener la última transacción de caja abierta por el asesor (usuario actual)
        $ultimaTransaccion = CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();

        // Crear el egreso
        $desembolso->estado = 1;
        $desembolso->realizado_por = $user->id;
        $desembolso->caja_transaccion_id = $ultimaTransaccion->id;

        $montoTotal = $desembolso->monto;

        // Actualizar la cantidad de egresos en la transacción de caja
        //$ultimaTransaccion->cantidad_egresos = $ultimaTransaccion->cantidad_egresos + $montoTotal;
        //$ultimaTransaccion->save();

        $desembolso->save();

        $pdf = Pdf::loadView('pdf.ticket_cts', compact('desembolso', 'montoTotal'))
            ->setPaper([0, 0, 205, 800]);

        return $pdf->stream('ticket.pdf');
    }


    public function ticket($id)
    {
        $deposito = DepositoCts::find($id);

        $montoTotal = $deposito->monto;


        $pdf = Pdf::loadView('pdf.ticketdeposito', compact('deposito', 'montoTotal'))
            ->setPaper([0, 0, 205, 800]);

        return $pdf->stream('ticket.pdf');
    }
}

<?php
// app/Http/Controllers/BovedaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boveda;
use App\Models\MovimientoBoveda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BovedaController extends Controller
{
    public function index()
    {
        $bovedas = Boveda::with('sucursal')->get();

        foreach ($bovedas as $boveda) {
            $movimientos = MovimientoBoveda::where('boveda_id', $boveda->id)->get();
            $boveda->setAttribute('total_ingresos', $movimientos->where('tipo', 'ingreso')->sum('monto'));
            $boveda->setAttribute('total_egresos', $movimientos->where('tipo', 'egreso')->sum('monto'));
            $boveda->setAttribute('saldo_actual', $boveda->monto_inicio + $boveda->total_ingresos - $boveda->total_egresos);
        }

        return view('admin.transacciones.boveda', compact('bovedas'));
    }



    public function edit($id)
    {
        $boveda = Boveda::findOrFail($id);
        return response()->json(['data' => $boveda]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'monto_inicio' => 'required|numeric',
                'fecha_inicio' => 'required|date',
            ]);

            $sucursalId = Auth::user()->sucursal_id;

            $boveda = Boveda::find($request->id) ?? new Boveda;
            $boveda->sucursal_id = $sucursalId;
            $boveda->monto_inicio = $request->monto_inicio;
            $boveda->fecha_inicio = $request->fecha_inicio;

            $boveda->save();

            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Bóveda guardada correctamente',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al guardar la bóveda: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $boveda = Boveda::findOrFail($id);
            $boveda->delete();

            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Bóveda eliminada correctamente',
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al eliminar la bóveda: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function movimientos($id)
    {
        $boveda = Boveda::findOrFail($id);
        $movimientos = MovimientoBoveda::where('boveda_id', $id)->orderBy('id', 'desc')->get();

        $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
        $saldoActual = $boveda->monto_inicio + $totalIngresos - $totalEgresos;

        return view('admin.transacciones.movimientos_boveda', compact('boveda', 'movimientos', 'totalIngresos', 'totalEgresos', 'saldoActual'));
    }
    public function agregarMovimiento(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'tipo' => 'required|in:ingreso,egreso',
                'monto' => 'required|numeric',
                'numero_documento' => 'nullable|string',
                'serie_documento' => 'nullable|string',
                'motivo' => 'required|string',
                'observacion' => 'nullable|string',
                'archivo' => 'nullable|file|mimes:jpg,png,pdf',
            ]);

            $boveda = Boveda::findOrFail($id);
            $userId = Auth::user()->id;

            $movimiento = new MovimientoBoveda($request->all());
            $movimiento->boveda_id = $boveda->id;
            $movimiento->user_id = $userId;

            if ($request->hasFile('archivo')) {
                $movimiento->archivo = $request->file('archivo')->store('movimientos_boveda');
            }

            $movimiento->save();

            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Movimiento registrado correctamente',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al registrar el movimiento: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function editarMovimiento($id, $movimientoId)
    {
        $movimiento = MovimientoBoveda::findOrFail($movimientoId);
        return response()->json(['data' => $movimiento]);
    }

    public function actualizarMovimiento(Request $request, $id, $movimientoId)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'tipo' => 'required|in:ingreso,egreso',
                'monto' => 'required|numeric',
                'numero_documento' => 'nullable|string',
                'serie_documento' => 'nullable|string',
                'motivo' => 'required|string',
                'observaciones' => 'nullable|string',
                'archivo' => 'nullable|file|mimes:jpg,png,pdf',
            ]);

            $movimiento = MovimientoBoveda::findOrFail($movimientoId);
            $movimiento->update($request->all());

            if ($request->hasFile('archivo')) {
                Storage::delete($movimiento->archivo);
                $movimiento->archivo = $request->file('archivo')->store('movimientos_boveda');
            }

            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Movimiento actualizado correctamente',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al actualizar el movimiento: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function eliminarMovimiento($id, $movimientoId)
    {
        DB::beginTransaction();
        try {
            $movimiento = MovimientoBoveda::findOrFail($movimientoId);
            $movimiento->delete();

            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Movimiento eliminado correctamente',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al eliminar el movimiento: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

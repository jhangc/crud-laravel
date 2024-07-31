<?php

namespace App\Http\Controllers;

use App\Models\IngresoExtra;
use App\Models\CajaTransaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IngresoExtraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cajaTransaccion = CajaTransaccion::where('user_id', $user->id)->latest()->first();

        if ($cajaTransaccion) {
            $ingresosExtras = IngresoExtra::where('caja_transaccion_id', $cajaTransaccion->id)->get();
        } else {
            $ingresosExtras = collect(); // Retorna una colección vacía si no hay transacción de caja
        }

        return view('admin.caja.ingresos', compact('ingresosExtras'));
    }

    public function edit($id)
    {
        $ingresoExtra = IngresoExtra::findOrFail($id);
        return response()->json(['data' => $ingresoExtra]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'monto' => 'required|numeric',
                'motivo' => 'required|string',
                'numero_documento' => 'required|string',
                'serie_documento' => 'required|string',
                'observaciones' => 'nullable|string',
                'archivo' => 'nullable|file|mimes:jpg,png,pdf'
            ]);

            $user = Auth::user();
            $cajaTransaccion = CajaTransaccion::where('user_id', $user->id)->latest()->first();

            if (!$cajaTransaccion) {
                return response()->json(['error' => 'No se encontró una transacción de caja abierta para este usuario.'], 400);
            }
            $ingresoExtra = IngresoExtra::find($request->id) ?? new IngresoExtra;
            $ingresoExtra->caja_transaccion_id = $cajaTransaccion->id;
            $ingresoExtra->user_id = $user->id;
            $ingresoExtra->monto = $request->monto;
            $ingresoExtra->motivo = $request->motivo;
            $ingresoExtra->numero_documento = $request->numero_documento;
            $ingresoExtra->serie_documento = $request->serie_documento;
            $ingresoExtra->observaciones = $request->observaciones;
            
            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo')->storeAs('public/ingresos_extras_archivos', $nombreArchivo);
                $ingresoExtra->archivo = $ruta;
            }
            $ingresoExtra->save();
            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Ingreso extra guardado correctamente',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al guardar el ingreso extra: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ingresoExtra = IngresoExtra::findOrFail($id);
            if ($ingresoExtra->archivo) {
                Storage::delete($ingresoExtra->archivo);
            }
            $ingresoExtra->delete();
            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Ingreso extra eliminado correctamente',
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al eliminar el ingreso extra: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

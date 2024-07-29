<?php
namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\CajaTransaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GastoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cajaTransaccion = CajaTransaccion::where('user_id', $user->id)->latest()->first();

        if ($cajaTransaccion) {
            $gastos = Gasto::where('caja_transaccion_id', $cajaTransaccion->id)->get();
        } else {
            $gastos = collect(); // Retorna una colección vacía si no hay transacción de caja
        }

        return view('admin.caja.gastos', compact('gastos'));
    }

    public function edit($id)
    {
        $gasto = Gasto::findOrFail($id);
        return response()->json(['data' => $gasto]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'monto_gasto' => 'required|numeric',
                'numero_doc' => 'required|string',
                'serie_doc' => 'required|string',
                'numero_documento_responsable' => 'required|string',
                'nombre_responsable' => 'required|string',
                'archivo' => 'nullable|file|mimes:jpg,png,pdf'
            ]);

            $user = Auth::user();
            $cajaTransaccion = CajaTransaccion::where('user_id', $user->id)->latest()->first();

            if (!$cajaTransaccion) {
                return response()->json(['error' => 'No se encontró una transacción de caja abierta para este usuario.'], 400);
            }
            $gasto = Gasto::find($request->id) ?? new Gasto;
            $gasto->caja_transaccion_id = $cajaTransaccion->id;
            $gasto->user_id = $user->id;
            $gasto->monto_gasto = $request->monto_gasto;
            $gasto->numero_doc = $request->numero_doc;
            $gasto->serie_doc = $request->serie_doc;
            $gasto->numero_documento_responsable = $request->numero_documento_responsable;
            $gasto->nombre_responsable = $request->nombre_responsable;
            
            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $nombreUnico = Str::uuid();
                $extension = $request->file('archivo')->getClientOriginalExtension();
                $nombreArchivo = $nombreUnico . '.' . $extension;
                $ruta = $request->file('archivo')->storeAs('public/gastos_archivos', $nombreArchivo);
                $gasto->archivo = $ruta;
            }
            $gasto->save();
            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Gasto guardado correctamente',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al guardar el gasto: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $gasto = Gasto::findOrFail($id);
            if ($gasto->archivo) {
                Storage::delete($gasto->archivo);
            }
            $gasto->delete();
            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Gasto eliminado correctamente',
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al eliminar el gasto: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

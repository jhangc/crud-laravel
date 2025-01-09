<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CuentasController extends Controller
{
    // Mostrar todas las cuentas
    public function index()
    {
        $cuentas = Cuenta::all(); 
        return view('admin.contabilidad.cuentas', compact('cuentas'));
    }

    // Crear o actualizar una cuenta
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'codigo' => 'required|string|max:20|unique:cuentas,codigo,' . $request->id,
                'nombre' => 'required|string|max:255',
                'tipo' => 'required|string|max:50',
                'nivel' => 'required|integer|min:1',
                'estado' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'state' => '1',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear o actualizar la cuenta
            $cuenta = $request->id ? Cuenta::findOrFail($request->id) : new Cuenta;
            $cuenta->codigo = $request->codigo;
            $cuenta->nombre = $request->nombre;
            $cuenta->tipo = $request->tipo;
            $cuenta->nivel = $request->nivel;
            $cuenta->estado = $request->estado;
            $cuenta->save();

            DB::commit();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Cuenta guardada correctamente',
                'cuenta' => $cuenta
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al guardar la cuenta: ' . $e->getMessage()
            ], 500);
        }
    }

    // Editar una cuenta
    public function edit($id)
    {
        $cuenta = Cuenta::find($id);

        if ($cuenta) {
            return response()->json([
                'state' => '0',
                'cuenta' => $cuenta
            ], 200);
        } else {
            return response()->json([
                'state' => '1',
                'mensaje' => 'Cuenta no encontrada'
            ], 404);
        }
    }

    // Eliminar una cuenta
    public function destroy($id)
    {
        try {
            $cuenta = Cuenta::findOrFail($id);
            $cuenta->delete();

            return response()->json([
                'state' => '0',
                'mensaje' => 'Cuenta eliminada correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'state' => '1',
                'mensaje' => 'Error al eliminar la cuenta: ' . $e->getMessage()
            ], 500);
        }
    }
}
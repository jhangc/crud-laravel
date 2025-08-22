<?php
// app/Http/Controllers/GoldPriceController.php

namespace App\Http\Controllers;

use App\Models\GoldPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GoldPriceController extends Controller
{
    // Vista principal
    public function index()
    {
        return view('admin.precios_oro.index');
    }

    // Listado (JSON) con filtros
    public function list(Request $request)
    {
        $request->validate([
            'kilate'     => ['nullable', 'integer', 'in:14,16,18,21'],
            'desde'      => ['nullable', 'date'],
            'hasta'      => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $q = GoldPrice::query();

        if ($request->filled('kilate')) {
            $q->where('kilate', (int)$request->kilate);
        }
        if ($request->filled('desde')) {
            $q->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $q->whereDate('fecha', '<=', $request->hasta);
        }

        $items = $q->orderBy('fecha', 'desc')->orderBy('kilate')->get();

        return response()->json(['data' => $items]);
    }

    // Crear (insert histórico)
    public function store(Request $request)
    {
        $data = $request->validate([
            'kilate' => ['required', 'integer', Rule::in([14,16,18,21])],
            'precio' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'fecha'  => ['required', 'date'],
        ]);

        // Evita duplicados por día/kilate
        $exists = GoldPrice::where('kilate', $data['kilate'])
            ->whereDate('fecha', $data['fecha'])
            ->exists();

        if ($exists) {
            return response()->json([
                'ok' => false,
                'message' => 'Ya existe un precio para ese kilate en esa fecha.'
            ], 422);
        }

        $gp = GoldPrice::create($data);

        return response()->json(['ok' => true, 'item' => $gp]);
    }

    // Actualizar (corrige registro histórico)
    public function update(Request $request, GoldPrice $goldPrice)
    {
        $data = $request->validate([
            'kilate' => ['required', 'integer', Rule::in([14,16,18,21])],
            'precio' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'fecha'  => ['required', 'date'],
        ]);

        $duplicate = GoldPrice::where('kilate', $data['kilate'])
            ->whereDate('fecha', $data['fecha'])
            ->where('id', '!=', $goldPrice->id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'ok' => false,
                'message' => 'Ya existe un precio para ese kilate en esa fecha.'
            ], 422);
        }

        $goldPrice->update($data);

        return response()->json(['ok' => true, 'item' => $goldPrice]);
    }

    // Eliminar
    public function destroy(GoldPrice $goldPrice)
    {
        $goldPrice->delete();
        return response()->json(['ok' => true]);
    }

    // Endpoint para CrediJoya: precio vigente por kilate (último <= hoy)
    public function vigente(Request $request)
    {
        $request->validate([
            'kilataje' => ['required', 'integer', Rule::in([14,16,18,21])]
        ]);

        $k = (int)$request->kilataje;

        $row = GoldPrice::where('kilate', $k)
            ->whereDate('fecha', '<=', now()->toDateString())
            ->orderBy('fecha', 'desc')
            ->first();

        if (!$row) {
            return response()->json(['message' => 'No hay precio vigente'], 200);
        }

        return response()->json([
            'kilate'            => $row->kilate,
            'precio_por_gramo'  => (float)$row->precio,
            'fecha'             => $row->fecha->toDateString(),
        ],200);
    }
}

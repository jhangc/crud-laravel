<?php
namespace App\Http\Controllers;

use App\Models\InicioOperaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IniciarOpeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sucursal_id = $user->sucursal_id;
        $autorizaciones = InicioOperaciones::with(['user', 'sucursal'])
                                           ->where('sucursal_id', $sucursal_id)
                                           ->orderBy('created_at', 'desc')
                                           ->get();
        $ultimaAutorizacion = $autorizaciones->first();
        $activado = $ultimaAutorizacion ? $ultimaAutorizacion->permiso_abierto : false;

        return view('admin.creditos.operaciones', compact('autorizaciones', 'activado', 'sucursal_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'permiso_abierto' => 'required|boolean',
        ]);

        InicioOperaciones::create([
            'user_id' => $request->user_id,
            'sucursal_id' => $request->sucursal_id,
            'permiso_abierto' => $request->permiso_abierto,
        ]);

        return response()->json(['success' => true]);
    }

    public function start($id)
    {
        $autorizacion = InicioOperaciones::findOrFail($id);

        if (auth()->user()->id !== $autorizacion->user_id) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para iniciar operaciones.']);
        }

        $autorizacion->update(['permiso_abierto' => true]);

        return response()->json(['success' => true]);
    }

    public function close(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'permiso_abierto' => 'required|boolean',
        ]);

        $autorizacion = InicioOperaciones::where('user_id', $request->user_id)
                                         ->where('sucursal_id', $request->sucursal_id)
                                         ->orderBy('created_at', 'desc')
                                         ->first();

        if (!$autorizacion) {
            return response()->json(['success' => false, 'message' => 'No hay operaciones para cerrar.']);
        }

        $autorizacion->update(['permiso_abierto' => false]);

        return response()->json(['success' => true]);
    }
}

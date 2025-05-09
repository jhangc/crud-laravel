<?php

namespace App\Http\Controllers;

use App\Models\InicioDesembolso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class InicioDesembolsoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        // 2) Sólo las cuentas CTS del usuario logueado para el select
        $permisos = InicioDesembolso::with('user')
            ->where('user_id', $userId)
            ->latest()  
            ->get();
        // 3) Retorna la vista con ambos conjuntos
        return view('admin.cts.permisos', compact('permisos'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 0) Verificar permiso abierto existente
        $user = Auth::user();
        $tieneAbierto = InicioDesembolso::where('user_id', $user->id)
            ->where('permiso_abierto', 1)
            ->exists();

        if ($tieneAbierto) {
            return response()->json([
                'success' => false,
                'message' => 'Tienes un permiso abierto'
            ], 409);
        }

        // 1) Validación personalizada
        $validator = Validator::make($request->all(), [
            'documento_autorizacion' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'documento_autorizacion.required' => 'Sube el documento',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('documento_autorizacion')
            ], 422);
        }

        // 2) Procesa el archivo
        $file       = $request->file('documento_autorizacion');
        $ext        = $file->getClientOriginalExtension();
        $randomName = strtoupper(Str::random(6));
        $filename   = "{$randomName}.{$ext}";

        $destination = public_path('permisos_cts');
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        $file->move($destination, $filename);

        // 3) Crea el permiso
        InicioDesembolso::create([
            'user_id'                => $user->id,
            'sucursal_id'            => $user->sucursal_id,
            'permiso_abierto'        => 1,
            'estado'                 => 'aprobado',
            'documento_autorizacion' => $filename,
        ]);

        // 4) Respuesta AJAX de éxito
        // Laravel por defecto usará 200 OK
        return response()->json(['success' => true]);
    }

    public function cerrar($id)
    {
        $permiso = InicioDesembolso::findOrFail($id);
        $permiso->permiso_abierto = 2;
        $permiso->save();
        return response()->json(['success' => true], 200);
    }
}

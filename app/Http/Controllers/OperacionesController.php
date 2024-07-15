<?php
use App\Models\User;
use App\Models\InicioOperaciones;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OperacionesController extends Controller
{
    public function index()
    {
        $autorizaciones = InicioOperaciones::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.creditos.operaciones', compact('autorizaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permiso_abierto' => 'required|boolean',
        ]);

        InicioOperaciones::create([
            'user_id' => $request->user_id,
            'permiso_abierto' => $request->permiso_abierto,
        ]);

        return redirect()->route('admin.creditos.operaciones')->with('success', 'Permiso actualizado correctamente.');
    }

    public function start($id)
    {
        $autorizacion = InicioOperaciones::findOrFail($id);
        $autorizacion->update(['permiso_abierto' => true]);

        return redirect()->route('admin.creditos.operaciones')->with('success', 'Operaciones iniciadas correctamente.');
    }
}

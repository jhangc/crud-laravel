<?php

namespace App\Http\Controllers;

use App\Models\CtsUsuario;
use App\Models\DepositoCts;
use App\Models\InicioDesembolso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CtsUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        // 2) Sólo las cuentas CTS del usuario logueado para el select
        $cuenta = CtsUsuario::with('user')
            ->where('user_id', $userId)
            ->first();

        // 1) Depósitos sólo de la cuenta CTS del usuario logueado.
        //    Algunos usuarios (p. ej. el usuario de sistema o un admin sin cuenta)
        //    no tienen cuenta CTS; en ese caso simplemente no hay movimientos.
        $depositos = $cuenta
            ? DepositoCts::with(['ctsUsuario.user', 'realizadoPor'])
                ->where('cts_usuario_id', $cuenta->id)
                ->orderBy('fecha_deposito', 'desc')
                ->get()
            : collect();
        $tienePermisoAbierto = InicioDesembolso::where('permiso_abierto', 1)
            ->exists();

        // 3) Retorna la vista con ambos conjuntos
        return view('admin.cts.saldo', compact('depositos', 'cuenta', 'tienePermisoAbierto'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CtsUsuario $ctsUsuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CtsUsuario $ctsUsuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CtsUsuario $ctsUsuario)
    {
        //
    }
}

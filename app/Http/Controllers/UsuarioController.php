<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\credito;
use App\Models\cliente;
use App\Models\CreditoCliente;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filtra los usuarios con estado 1 o null y carga los roles asociados
        $usuarios = User::with('roles')->where(function ($query) {
            $query->where('estado', 1)
                ->orWhereNull('estado');
        })->get();

        return view('admin.usuarios.index', ['usuarios' => $usuarios]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all(); // Obtiene todos los roles
        return view('admin.usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'direccion' => 'nullable|max:255',  // Asegúrate de validar como email y que sea único en la tabla de usuarios
            'role' => 'required|exists:roles,id',  // Asegúrate de que el ID del rol exista en la tabla de roles
            'telefono' => 'required|numeric',
        ]);

        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request['password']);
        $usuario->direccion = $request->direccion;  // Guarda la dirección
        $usuario->telefono = $request->telefono;
        $usuario->estado = 1;
        $usuario->save();

        // Asignar rol por ID
        $role = Role::findById($request->role);
        if ($role) {
            $usuario->assignRole($role->name);
        }


        return redirect()->route('usuarios.index')
            ->with('mensaje', 'Se registró al usuario de manera correcta')
            ->with('icono', 'success');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = User::findOrFail($id);
        return view('admin.usuarios.show', ['usuario' => $usuario]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles = Role::all(); // Obtiene todos los roles disponibles
        $usuarioRole = $usuario->roles->first(); // Obtiene el primer rol del usuario, ya que solo tiene un rol asignado
        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => $roles,
            'usuarioRole' => $usuarioRole
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|confirmed',
            'telefono' => 'required|numeric',
        ]);
    
        $usuario = User::find($id);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->telefono = $request->telefono;
        $usuario->direccion = $request->direccion;
        $usuario->estado = 1;
        $usuario->save();
    
        return redirect()->route('usuarios.index')
            ->with('mensaje', 'Se actualizó al usuario de la manera correcta')
            ->with('icono', 'success');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->estado = 0; // Cambiar el estado a 0
        $user->save();

        return redirect()->route('usuarios.index')
            ->with('mensaje', 'Se eliminó al usuario de la manera correcta')
            ->with('icono', 'success');
    }

    public function actualizarCreditosTerminados()
{
    // Obtener todos los créditos que actualmente están en estado "pagado"
    $creditos = credito::where('estado', 'pagado')->get();

    foreach ($creditos as $credito) {
        $allPaid = true;
        
        // Para créditos individuales: se consideran las cuotas que tengan cliente asignado
        if ($credito->categoria != 'grupal') {
            $cuotas = $credito->cronograma()->whereNotNull('cliente_id')->get();
            foreach ($cuotas as $cuota) {
                // Si para alguna cuota no existe un ingreso asociado, la cuota no está pagada
                if (!\App\Models\Ingreso::where('cronograma_id', $cuota->id)->exists()) {
                    $allPaid = false;
                    break;
                }
            }
        } else {
            // Para créditos grupales: se deben verificar las cuotas generales (cliente_id null)
            // y además las cuotas individuales de cada cliente
            $cuotasGenerales = $credito->cronograma()->whereNull('cliente_id')->get();
            foreach ($cuotasGenerales as $cuotaGeneral) {
                if (!\App\Models\Ingreso::where('cronograma_id', $cuotaGeneral->id)->exists()) {
                    $allPaid = false;
                    break;
                }
            }
            // Solo si las generales están pagadas, verificamos las individuales
            if ($allPaid) {
                foreach ($credito->creditoClientes as $cc) {
                    $cuotasInd = $credito->cronograma()->where('cliente_id', $cc->cliente_id)->get();
                    foreach ($cuotasInd as $cuotaInd) {
                        if (!\App\Models\Ingreso::where('cronograma_id', $cuotaInd->id)->exists()) {
                            $allPaid = false;
                            break 2; // Salir de ambos bucles
                        }
                    }
                }
            }
        }

        // Si todas las cuotas de este crédito tienen un ingreso asociado, se actualiza el estado a "terminado"
        if ($allPaid) {
            $credito->estado = 'terminado';
            $credito->save();
        }
    }

    return response()->json(['success' => 'Estados de créditos actualizados correctamente.']);
}

}

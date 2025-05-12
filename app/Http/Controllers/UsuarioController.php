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
use App\Models\CtsUsuario;
use Illuminate\Support\Facades\DB;

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
            'name'              => 'required|max:100',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:6|confirmed',
            'telefono'          => 'required|numeric',
            'dni'               => 'required|digits:8',
            'direccion'         => 'nullable|max:255',
            'role'              => 'required|exists:roles,id',
        ]);

        // 1) Creamos el usuario
        $usuario = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'telefono'          => $request->telefono,
            'dni'               => $request->dni,
            'direccion'         => $request->direccion,
            'fecha_nacimiento'  => $request->fecha_nacimiento,
            'estado'            => 1,
        ]);

        // 2) Generamos la cuenta CTS y la guardamos en su tabla
        $cts = CtsUsuario::create([
            'user_id'                => $usuario->id,
            'numero_cuenta'          => $this->generateUniqueAccountNumber($request->dni),
            'saldo_disponible'       => 0,
            'fecha_ultimo_deposito'  => null,
        ]);

        // 3) Asignamos el id de esa cuenta al usuario
        $usuario->id_cuenta = $cts->id;
        $usuario->save();

        // 4) Asignamos el rol
        if ($role = Role::findById($request->role)) {
            $usuario->assignRole($role->name);
        }

        return redirect()->route('usuarios.index')
            ->with('mensaje', 'Se registró al usuario de manera correcta')
            ->with('icono', 'success');
    }

    private function generateAccountNumber(string $dni): string
    {
        $last = DB::table('cts_usuarios')
            ->whereNotNull('numero_cuenta')
            ->max(DB::raw('CAST(SUBSTR(numero_cuenta,1,4) AS UNSIGNED)'));

        $next   = ($last ?: 1000) + 1;
        $prefix = str_pad($next, 4, '0', STR_PAD_LEFT);
        $suffix = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $dni . $suffix;
    }

    private function generateUniqueAccountNumber(string $dni): string
    {
        do {
            $acct = $this->generateAccountNumber($dni);
        } while (CtsUsuario::where('numero_cuenta',$acct)->exists());

        return $acct;
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
            'name'              => 'required|max:100',
            'email'             => 'required|email|unique:users,email,' . $id,
            'password'          => 'nullable|confirmed',
            'telefono'          => 'required|numeric',
            'dni'               => 'required|digits:8|unique:users,dni,' . $id,
            'direccion'         => 'nullable|max:255',
            'role'              => 'required|exists:roles,id',
        ]);

        $usuario = User::findOrFail($id);
        $oldDni  = $usuario->dni;

        // Campos básicos
        $usuario->fill([
            'name'             => $request->name,
            'email'            => $request->email,
            'telefono'         => $request->telefono,
            'direccion'        => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'estado'           => 1,
        ]);

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        // Si cambió el DNI, actualizamos tanto user->dni como el numero_cuenta en cts_usuarios
        if (is_null($oldDni) || $oldDni !== $request->dni) {
            // 1) Actualiza el DNI en el usuario
            $usuario->dni = $request->dni;
        
            // 2) Obtiene la cuenta CTS existente (puede ser null)
            $cts = $usuario->ctsUsuario;
        
            if ($cts) {
                // 3a) Si existe, sólo regeneramos el número
                $cts->numero_cuenta = $this->generateUniqueAccountNumber($request->dni);
                $cts->save();
            } else {
                // 3b) Si no existe, la creamos y asignamos al usuario
                $cts = CtsUsuario::create([
                    'user_id'               => $usuario->id,
                    'numero_cuenta'         => $this->generateUniqueAccountNumber($request->dni),
                    'saldo_disponible'      => 0,
                    'fecha_ultimo_deposito' => null,
                ]);
                $usuario->id_cuenta = $cts->id;
            }
        }

        $usuario->save();

        // Sincronizamos el rol
        if ($role = Role::findById($request->role)) {
            $usuario->syncRoles($role->name);
        }

        return redirect()->route('usuarios.index')
            ->with('mensaje', 'Se actualizó al usuario correctamente')
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

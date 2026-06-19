<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\Credito;
use App\Models\Cliente;
use App\Models\CreditoCliente;
use App\Models\CtsUsuario;
use App\Models\CajaTransaccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

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
        })->with('ctsUsuario')->get();

        $limiteInactividad = now()->subMinutes((int) config('session.lifetime', 120));

        $cajerasConCajaAbierta = CajaTransaccion::whereNull('hora_cierre')
            ->whereNull('fecha_cierre')
            ->pluck('user_id');

        foreach ($usuarios as $usuario) {
            $ultimaActividadRaw = Cache::get('user-last-activity-' . $usuario->id);
            $ultimaActividad = $ultimaActividadRaw ? Carbon::parse($ultimaActividadRaw) : null;

            $usuario->sesion_activa = $ultimaActividad && $ultimaActividad->greaterThanOrEqualTo($limiteInactividad);
            $usuario->ultima_actividad = $ultimaActividad;

            $usuario->es_cajera = $usuario->roles->contains(function ($role) {
                return strtolower($role->name) === 'cajera';
            });

            $usuario->caja_abierta = $usuario->es_cajera && $cajerasConCajaAbierta->contains($usuario->id);
        }

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
        // Tolerancia en céntimos para redondeos
        $epsilon = 0.01;

        // Una cuota está pagada solo si su SALDO real (monto - abonos a cuota) está cubierto,
        // no basta con que exista un ingreso: un pago parcial deja saldo pendiente.
        $cuotaPagada = function ($cuota) use ($epsilon) {
            return $cuota->saldoYMora()['saldo'] <= $epsilon;
        };

        // Obtener todos los créditos que actualmente están en estado "pagado"
        $creditos = Credito::where('estado', 'pagado')->get();

        foreach ($creditos as $credito) {
            $allPaid = true;

            // Para créditos individuales: se consideran las cuotas que tengan cliente asignado
            if ($credito->categoria != 'grupal') {
                $cuotas = $credito->cronograma()->whereNotNull('cliente_id')->get();
                foreach ($cuotas as $cuota) {
                    // Si alguna cuota aún tiene saldo, el crédito no está terminado
                    if (!$cuotaPagada($cuota)) {
                        $allPaid = false;
                        break;
                    }
                }
            } else {
                // Para créditos grupales: se deben verificar las cuotas generales (cliente_id null)
                // y además las cuotas individuales de cada cliente
                $cuotasGenerales = $credito->cronograma()->whereNull('cliente_id')->get();
                foreach ($cuotasGenerales as $cuotaGeneral) {
                    if (!$cuotaPagada($cuotaGeneral)) {
                        $allPaid = false;
                        break;
                    }
                }
                // Solo si las generales están pagadas, verificamos las individuales
                if ($allPaid) {
                    foreach ($credito->creditoClientes as $cc) {
                        $cuotasInd = $credito->cronograma()->where('cliente_id', $cc->cliente_id)->get();
                        foreach ($cuotasInd as $cuotaInd) {
                            if (!$cuotaPagada($cuotaInd)) {
                                $allPaid = false;
                                break 2; // Salir de ambos bucles
                            }
                        }
                    }
                }
            }

            // Si todas las cuotas están completamente pagadas, se actualiza el estado a "terminado"
            if ($allPaid) {
                $credito->estado = 'terminado';
                $credito->save();
            }
        }

        return response()->json(['success' => 'Estados de créditos actualizados correctamente.']);
    }

    /**
     * Script de CORRECCIÓN. Debe ejecutarse ANTES del de actualización.
     *
     * Reactiva (vuelve a "pagado" = Activo) los créditos que quedaron mal
     * marcados como "terminado" pese a tener saldo pendiente, para que la
     * cajera pueda seguir cobrando. Usa exactamente el mismo criterio de saldo
     * que actualizarCreditosTerminados(), de modo que ambos scripts conviven:
     * la corrección reabre lo que aún debe y la actualización cierra solo lo
     * que de verdad está pagado por completo.
     *
     * Se EXCLUYEN los CrediJoya: su cierre puede provenir de una renovación
     * (se cierra el crédito viejo con saldo y se traslada a uno nuevo), por lo
     * que un saldo pendiente no implica error. Esos se revisan por separado.
     */
    public function corregirCreditosTerminadosErroneos()
    {
        // Tolerancia en céntimos para redondeos
        $epsilon = 0.01;

        // Una cuota está pagada solo si su SALDO real está cubierto.
        $cuotaPagada = function ($cuota) use ($epsilon) {
            return $cuota->saldoYMora()['saldo'] <= $epsilon;
        };

        // Solo créditos marcados "terminado", excluyendo CrediJoya.
        $creditos = Credito::where('estado', 'terminado')
            ->where(function ($q) {
                $q->where('categoria', '!=', 'credijoya')->orWhereNull('categoria');
            })
            ->get();

        $corregidos = [];

        foreach ($creditos as $credito) {
            $tieneSaldo = false;

            // Para créditos individuales: cuotas con cliente asignado
            if ($credito->categoria != 'grupal') {
                $cuotas = $credito->cronograma()->whereNotNull('cliente_id')->get();
                foreach ($cuotas as $cuota) {
                    if (!$cuotaPagada($cuota)) {
                        $tieneSaldo = true;
                        break;
                    }
                }
            } else {
                // Para créditos grupales: cuotas generales (cliente_id null)
                // y además las cuotas individuales de cada cliente
                $cuotasGenerales = $credito->cronograma()->whereNull('cliente_id')->get();
                foreach ($cuotasGenerales as $cuotaGeneral) {
                    if (!$cuotaPagada($cuotaGeneral)) {
                        $tieneSaldo = true;
                        break;
                    }
                }
                if (!$tieneSaldo) {
                    foreach ($credito->creditoClientes as $cc) {
                        $cuotasInd = $credito->cronograma()->where('cliente_id', $cc->cliente_id)->get();
                        foreach ($cuotasInd as $cuotaInd) {
                            if (!$cuotaPagada($cuotaInd)) {
                                $tieneSaldo = true;
                                break 2;
                            }
                        }
                    }
                }
            }

            // Si todavía debe, no debió quedar "terminado": se reactiva para cobranza.
            if ($tieneSaldo) {
                $credito->estado = 'pagado';
                $credito->save();
                $corregidos[] = $credito->id;
            }
        }

        return response()->json([
            'success'    => 'Créditos corregidos correctamente.',
            'total'      => count($corregidos),
            'corregidos' => $corregidos,
        ]);
    }
}

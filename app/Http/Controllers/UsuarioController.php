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
        // Filtra los usuarios con estado 1 o null y carga los roles asociados.
        // Se oculta el usuario de desarrollador (rol super_system) del listado.
        $usuarios = User::with('roles')->where(function ($query) {
            $query->where('estado', 1)
                ->orWhereNull('estado');
        })->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super_system');
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
        // Se excluye el rol oculto de desarrollador (super_system).
        $roles = Role::where('name', '!=', 'super_system')->get();
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
        // Se excluye el rol oculto de desarrollador (super_system).
        $roles = Role::where('name', '!=', 'super_system')->get();
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
        // Créditos en estado "pagado" (Activo), excluyendo CrediJoya: estos se
        // cierran por su propio flujo (que además libera las joyas), no aquí.
        $creditos = Credito::where('estado', 'pagado')
            ->where(function ($q) {
                $q->where('categoria', '!=', 'credijoya')->orWhereNull('categoria');
            })
            ->get();

        $terminados = [];

        foreach ($creditos as $credito) {
            // Se cierra (terminado) solo si TODAS sus cuotas están liquidadas.
            if ($this->creditoTotalmentePagado($credito)) {
                $credito->estado = 'terminado';
                $credito->save();
                $terminados[] = $credito->id;
            }
        }

        return response()->json([
            'success'    => 'Estados de créditos actualizados correctamente.',
            'total'      => count($terminados),
            'terminados' => $terminados,
        ]);
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
        // Solo créditos marcados "terminado", excluyendo CrediJoya.
        $creditos = Credito::where('estado', 'terminado')
            ->where(function ($q) {
                $q->where('categoria', '!=', 'credijoya')->orWhereNull('categoria');
            })
            ->get();

        $corregidos = [];

        foreach ($creditos as $credito) {
            // Si NO está totalmente pagado, no debió quedar "terminado": se reactiva
            // para que la cajera pueda seguir cobrando. Mismo predicado que usa la
            // actualización, así ambos scripts conviven sin contradecirse.
            if (!$this->creditoTotalmentePagado($credito)) {
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

    /**
     * Predicado ÚNICO que comparten la actualización (cerrar) y la corrección
     * (reabrir): un crédito está totalmente pagado si tiene al menos una cuota y
     * TODAS sus cuotas están liquidadas.
     *
     * Se evalúan TODAS las cuotas del cronograma sin distinguir nivel
     * (cliente_id null = generales de un grupal, o con cliente). Así:
     *  - Individual: revisa sus cuotas (todas con cliente).
     *  - Grupal moderno: generales e individuales reciben ingresos a la par,
     *    así que exigir todas liquidadas es correcto.
     *  - Grupal antiguo sin cuotas generales: revisa las individuales.
     *  - No depende de la relación creditoClientes (que podría estar incompleta).
     *  - Sin cuotas => NO se considera pagado (evita cerrar créditos vacíos).
     * Corta en la primera cuota no liquidada.
     */
    private function creditoTotalmentePagado(Credito $credito): bool
    {
        $cuotas = $credito->cronograma()->get();

        if ($cuotas->isEmpty()) {
            return false;
        }

        foreach ($cuotas as $cuota) {
            if (!$this->cuotaLiquidada($cuota)) {
                return false;
            }
        }

        return true;
    }

    /**
     * ¿La cuota está liquidada? (mismo criterio que CreditoController::cuotaLiquidada,
     * el que usa la vista de cobranza para mostrar "Cuota cancelada").
     *
     * Sí cuando: tiene un ingreso de "cierre" (pago normal/total de cuota, no un abono
     * parcial) — lo que cancela la cuota aunque condone una pequeña diferencia —, o
     * cuando los abonos parciales ya cubren todo el saldo. Un abono parcial que no
     * cubre la cuota NO la liquida.
     */
    private function cuotaLiquidada($cuota): bool
    {
        // Fuente de verdad única: App\Models\Cronograma::liquidada().
        return $cuota->liquidada();
    }
}

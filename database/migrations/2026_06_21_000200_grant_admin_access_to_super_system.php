<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Le da al usuario super_system acceso total "como el admin": además de su rol
 * oculto super_system, se le asigna el rol "Administrador" para que el menú
 * lateral (que usa @role('Administrador')) le muestre TODAS las opciones.
 *
 * Los permisos en sí ya pasan por el Gate::before de AuthServiceProvider
 * (modo dios), así que esta asignación es sobre todo para la visibilidad del menú.
 *
 * Idempotente: funciona tanto si la migración que crea el usuario ya corrió
 * como si corre justo antes (orden por timestamp).
 */
return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';
        $email = env('SUPER_SYSTEM_EMAIL', 'super@system.dev');

        $user = User::where('email', $email)->first();
        if (! $user) {
            return; // El usuario se crea en la migración 000100; si no existe, no hay nada que hacer.
        }

        // Garantiza que el rol que consume el menú ('Administrador') exista y se asigne.
        Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => $guard]);

        if (! $user->hasRole('Administrador')) {
            $user->assignRole('Administrador');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $email = env('SUPER_SYSTEM_EMAIL', 'super@system.dev');

        if ($user = User::where('email', $email)->first()) {
            if ($user->hasRole('Administrador')) {
                $user->removeRole('Administrador');
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};

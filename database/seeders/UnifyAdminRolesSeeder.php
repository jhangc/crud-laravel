<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Asegúrate de ajustar al modelo de usuario correcto de tu aplicación

class UnifyAdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Asegurarse de que el nuevo rol existe
        $newRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);

        // Encontrar el viejo rol
        $oldRole = Role::where('name', 'admin')->first();

        if ($oldRole) {
            // Mover usuarios del viejo al nuevo rol
            $users = $oldRole->users; // Asegúrate de que tu relación de usuarios está correctamente definida en Role
            foreach ($users as $user) {
                $user->removeRole($oldRole);
                $user->assignRole($newRole);
            }

            // Eliminar el viejo rol
            $oldRole->delete();
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class MoveAdminUsersToAdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Encuentra los roles
        $oldRole = Role::where('name', 'admin')->first();
        $newRole = Role::where('name', 'administrador')->firstOrFail();

        if ($oldRole) {
            // Obtén todos los usuarios con el rol 'admin'
            $users = $oldRole->users;

            foreach ($users as $user) {
                // Remueve el rol 'admin' y asigna el rol 'administrador'
                $user->removeRole($oldRole);
                $user->assignRole($newRole);
            }

            // Elimina el rol 'admin' después de mover todos los usuarios
            $oldRole->delete();
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignAdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        $administrador = Role::where('name', 'administrador')->firstOrFail();

        $permissions = [
            'admin.index', 'usuarios.index', 'usuarios.create', 'usuarios.store',
            'usuarios.show', 'usuarios.edit', 'usuarios.update', 'usuarios.destroy'
        ];

        foreach ($permissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName]);
            $permission->syncRoles([$administrador]);
        }
    }
}

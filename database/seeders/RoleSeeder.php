<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //El sistema tendra 5 roles: Administrador, prestamista

        $admin = Role::create(['name' => 'admin']);
        $prestamista = Role::create(['name' => 'prestamista']);

        $permission = Permission::create(['name' => 'admin.index'])->syncRoles([$admin,$prestamista]);
        $permission = Permission::create(['name' => 'usuarios.index'])->syncRoles([$admin]);
        $permission = Permission::create(['name' => 'usuarios.create'])->syncRoles([$admin]);
        $permission = Permission::create(['name' => 'usuarios.store'])->syncRoles([$admin]);
        $permission = Permission::create(['name' => 'usuarios.show'])->syncRoles([$admin]);
        $permission = Permission::create(['name' => 'usuarios.edit'])->syncRoles([$admin]);
        $permission = Permission::create(['name' => 'usuarios.update'])->syncRoles([$admin]);
        $permission = Permission::create(['name' => 'usuarios.destroy'])->syncRoles([$admin]);
    }

}

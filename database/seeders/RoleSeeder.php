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

        // $admin = Role::create(['name' => 'admin']);
        // $prestamista = Role::create(['name' => 'prestamista']);
        $administrador = Role::create(['name' => 'Administrador']);
        // $asesor = Role::create(['name' => 'Asesor de creditos']);
        // $cajera = Role::create(['name' => 'Cajera']);
        // $gestor = Role::create(['name' => 'Gestor de Cobranza']);
        // $contabilidad = Role::create(['name' => 'Contabilidad']);

        $permission = Permission::create(['name' => 'admin.index'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.index'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.create'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.store'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.show'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.edit'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.update'])->syncRoles([$administrador]);
        $permission = Permission::create(['name' => 'usuarios.destroy'])->syncRoles([$administrador]);

         // Permisos para el administrador
        //  $permission = [
        //     'admin.index', 'usuarios.index', 'usuarios.create', 'usuarios.store',
        //     'usuarios.show', 'usuarios.edit', 'usuarios.update', 'usuarios.destroy'
        // ];


}}

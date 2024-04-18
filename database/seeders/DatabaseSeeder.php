<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call([RoleSeeder::class]);

        $this->call([
            RoleSeeder::class,        // Este es el seeder que ya tienes
            UnifyAdminRolesSeeder::class,  // Agrega aquí tu nuevo seeder
        ]);


        
        User::create([
            'name'=>'admin',
            'email'=>'admin@admin.com',
            'password'=>Hash::make('12345678'),
        ])->assignRole('administrador');

        User::create([
            'name'=>'asesor',
            'email'=>'asesor@asesor.com',
            'password'=>Hash::make('12345678'),
        ])->assignRole('Asesor de creditos');

        

    }
}

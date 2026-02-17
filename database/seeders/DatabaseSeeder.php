<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear el rol administrador
        $adminRole = Role::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'ADMINISTRADOR',
                'description' => 'ROL CON ACCESO TOTAL AL SISTEMA',
                'is_active' => true,
            ]
        );

        // Crear el usuario administrador con el rol
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@unprg.edu.pe',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);

        $this->call([
            ViewsTableSeeder::class,
            PermissionsTableSeeder::class,
            RolePermissionsTableSeeder::class,
        ]);

        $this->command->info('Admin role and user created successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionsTableSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::findOrFail(1);

        $permissions = Permission::where('is_active', true)->get();

        foreach ($permissions as $permission) {
            RolePermission::updateOrCreate(
                [
                    'role_id'       => $adminRole->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'granted' => true,
                ]
            );
        }

        $this->command->info("Role permissions seeded: {$permissions->count()} permisos asignados al rol [{$adminRole->name}].");
    }
}
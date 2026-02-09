<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Define fixed permissions
         $permissions = [
            'view users',
            'edit users',
            'delete users',
            'create users',
            'manage roles',
            'view booking',
            'edit booking',
            'delete booking',
            'create booking',
            'manage booking',
            'cancel booking',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'admin' => array_diff($permissions, ['']),
            'driver' => ['view booking', 'cancel booking'],
            'employee' => ['create booking', 'cancel booking'],
        ];

        foreach ($roles as $role => $rolePermissions) {
            $roleModel = Role::firstOrCreate(['name' => $role]);
            $roleModel->syncPermissions($rolePermissions);
        }
    }
}

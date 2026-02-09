<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cars;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        User::factory()->create([
            'name' => 'Administrator',
            'employee_no' => '3001',
            'email' => 'admin@novulutions.com',
            'password' => Hash::make('N0vulut10ns@'),
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Driver',
            'employee_no' => '3002',
            'email' => 'driver@novulutions.com',
            'password' => Hash::make('N0vulut10ns@'),
        ])->assignRole('driver');

        Cars::factory()->create([
            'name'  => 'Toyota Hiace',
            'image' => 'Toyota Hiace',
            'license_plate' => '1234HA',
            'seater' => 16,
            'brand' => 'Toyota',
            'type' => 'SUV',
            'is_available' => true,
            'remarks' => '',
        ]);
    }
}

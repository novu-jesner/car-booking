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


        User::factory()->create([
            'name' => 'Driver 2',
            'employee_no' => '3003',
            'email' => 'driver2@novulutions.com',
            'password' => Hash::make('N0vulut10ns@'),
        ])->assignRole('driver');


        User::factory()->create([
            'name' => 'Driver 3',
            'employee_no' => '3004',
            'email' => 'driver3@novulutions.com',
            'password' => Hash::make('N0vulut10ns@'),
        ])->assignRole('driver');


        Cars::factory()->create([
            'name'  => 'Toyota Hiace',
            'image' => 'Toyota Hiace',
            'license_plate' => '1234HA',
            'seater' => 14,
            'brand' => 'Toyota',
            'type' => 'SUV',
            'is_available' => true,
            'remarks' => '',
        ]);

        Cars::factory()->create([
    'name' => 'Toyota Lancer',
    'image' => 'Toyota Lancer',
    'license_plate' => 'Lancer-001',
    'seater' => 16,
    'brand' => 'Toyota',
    'type' => 'Van',
    'is_available' => true,
]);

Cars::factory()->create([
    'name' => 'Nissan Urvan',
    'image' => 'Nissan Urvan',
    'license_plate' => 'URVAN-002',
    'seater' => 15,
    'brand' => 'Nissan',
    'type' => 'Van',
    'is_available' => true,
]);

    }
}

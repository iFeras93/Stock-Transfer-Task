<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //we can use factory instead static data



        // Create admin user
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create warehouse managers
        $manager1 = User::query()->firstOrCreate(
            ['email' => 'manager1@example.com'],
            [
                'name' => 'Warehouse Manager 1',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        $manager2 = User::query()->firstOrCreate(
            ['email' => 'manager2@example.com'],
            [
                'name' => 'Warehouse Manager 2',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        // Create shipping integration user
        $shipping = User::query()->firstOrCreate(
            ['email' => 'shipping@example.com'],
            [
                'name' => 'Shipping Integration',
                'password' => Hash::make('password'),
                'role' => 'shipping',
            ]
        );

        $this->command->info('Users seeded successfully!');
    }
}

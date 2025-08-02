<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\User;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {

        //we can use factory instead static data

        $manager1 = User::query()->where('email', 'manager1@example.com')->first();
        $manager2 = User::query()->where('email', 'manager2@example.com')->first();

        $warehouse1 = Warehouse::query()->firstOrCreate(
            ['name' => 'Main Warehouse'],
            [
                'location' => '123 Main St, City, State 12345',
                'owner_id' => $manager1?->id,
            ]
        );

        $warehouse2 = Warehouse::query()->firstOrCreate(
            ['name' => 'Secondary Warehouse'],
            [
                'location' => '456 Second St, City, State 12345',
                'owner_id' => $manager2?->id,
            ]
        );

        $warehouse3 = Warehouse::query()->firstOrCreate(
            ['name' => 'Distribution Center'],
            [
                'location' => '789 Third St, City, State 12345',
            ]
        );

        // Assign warehouse access to managers
        if ($manager1) {
            $manager1->giveWarehouseAccess($warehouse1->id);
        }

        if ($manager2) {
            $manager2->giveWarehouseAccess($warehouse2->id);
            $manager2->giveWarehouseAccess($warehouse3->id);
        }

        $this->command->info('Warehouses seeded successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Enums\StockTransferStatus;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $warehouses = Warehouse::all();
        $products = Product::all();

        if ($users->isEmpty() || $warehouses->count() < 2 || $products->isEmpty()) {
            $this->command->warn('Please ensure you have users, at least 2 warehouses, and products seeded first.');
            return;
        }

        $statuses = StockTransferStatus::cases();

        for ($i = 1; $i <= 20; $i++) {
            $warehouseFrom = $warehouses->random();
            $warehouseTo = $warehouses->where('id', '!=', $warehouseFrom->id)->random();

            $transfer = StockTransfer::query()->create([
                'warehouse_from_id' => $warehouseFrom->id,
                'warehouse_to_id' => $warehouseTo->id,
                'status' => $statuses[array_rand($statuses)],
                'notes' => fake()->optional()->sentence(),
                'created_by' => $users->random()->id,
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);

            // Add 1-5 products to each transfer
            $transferProducts = $products->random(rand(1, 5));
            foreach ($transferProducts as $product) {
                $quantity = rand(1, 100);
                $transfer->products()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'received_quantity' => $transfer->status->value === StockTransferStatus::COMPLETED->value
                        ? rand(1, $quantity) : null,
                    'damaged_quantity' => $transfer->status->value === StockTransferStatus::COMPLETED->value
                        ? rand(0, 5) : null,
                ]);
            }
        }

        $this->command->info('Stock transfers seeded successfully!');
    }
}

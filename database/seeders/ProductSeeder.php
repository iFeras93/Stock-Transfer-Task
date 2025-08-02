<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        //we can use factory instead static data
        $products = [
            ['name' => 'Laptop Computer', 'sku' => 'LAPTOP-001', 'description' => 'High-performance laptop', 'price' => 999.99],
            ['name' => 'Wireless Mouse', 'sku' => 'MOUSE-001', 'description' => 'Ergonomic wireless mouse', 'price' => 29.99],
            ['name' => 'Keyboard', 'sku' => 'KEYBOARD-001', 'description' => 'Mechanical keyboard', 'price' => 79.99],
            ['name' => 'Monitor', 'sku' => 'MONITOR-001', 'description' => '24-inch LED monitor', 'price' => 199.99],
            ['name' => 'Headphones', 'sku' => 'HEADPHONES-001', 'description' => 'Noise-canceling headphones', 'price' => 149.99],
            ['name' => 'USB Drive', 'sku' => 'USB-001', 'description' => '32GB USB flash drive', 'price' => 19.99],
            ['name' => 'Webcam', 'sku' => 'WEBCAM-001', 'description' => 'HD webcam', 'price' => 59.99],
            ['name' => 'Printer', 'sku' => 'PRINTER-001', 'description' => 'All-in-one printer', 'price' => 129.99],
            ['name' => 'Tablet', 'sku' => 'TABLET-001', 'description' => '10-inch tablet', 'price' => 299.99],
            ['name' => 'Smartphone', 'sku' => 'PHONE-001', 'description' => 'Latest smartphone', 'price' => 699.99],
        ];

        foreach ($products as $product) {
            Product::query()->firstOrCreate(['sku' => $product['sku']], $product);
        }

        $this->command->info('Products seeded successfully!');
    }
}

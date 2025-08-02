<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key for the product
            $table->string('name')->unique(); // Name of the product, should be unique
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->text('description')->nullable(); // Optional description
            $table->decimal('price', 10, 2)->nullable(); // Price of the product
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

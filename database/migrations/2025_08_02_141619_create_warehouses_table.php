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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id(); // Primary key for the warehouse
            $table->string('name')->unique(); // Name of the warehouse, should be unique
            $table->string('location')->nullable(); // Physical location of the warehouse
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_active')->default(true); // To indicate if the warehouse is active
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

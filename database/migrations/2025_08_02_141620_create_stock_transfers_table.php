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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();

            // Foreign key to a potential delivery integration service/table
            $table->unsignedBigInteger('delivery_integration_id')->nullable();

            $table->foreignId('warehouse_from_id')->constrained('warehouses')->onDelete('restrict'); // Sending warehouse
            $table->foreignId('warehouse_to_id')->constrained('warehouses')->onDelete('restrict');   // Receiving warehouse
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict'); // User who created the transfer

            $table->enum('status', [
                'new', 'preparing', 'ready', 'shipping',
                'received', 'completed', 'cancelled', 'returning'
            ])->default('new');

            $table->text('notes')->nullable();
            $table->timestamps();

            // To enable indexes and foreign keys, uncomment lines.
//            $table->foreign('delivery_integration_id')->references('id')->on('delivery_integrations')->onDelete('set null');

            $table->index(['status', 'created_at']);
            $table->index(['warehouse_from_id', 'warehouse_to_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};

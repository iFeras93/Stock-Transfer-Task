<?php

namespace App\Listeners;

use App\Events\StockTransferCreated;
use App\Events\StockTransferStatusChanged;
use App\Models\StockTransferActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogStockTransferActivity
{
    public function handleStatusChanged(StockTransferStatusChanged $event): void
    {
        StockTransferActivity::query()->create([
            'stock_transfer_id' => $event->stockTransfer->id,
            'action' => 'status_changed',
            'from_status' => $event->fromStatus->value,
            'to_status' => $event->toStatus->value,
            'user_id' => $event->user?->id,
            'created_at' => now(),
        ]);
    }

    public function handleCreated(StockTransferCreated $event): void
    {
        StockTransferActivity::query()->create([
            'stock_transfer_id' => $event->stockTransfer->id,
            'action' => 'created',
            'to_status' => $event->stockTransfer->status->value,
            'user_id' => $event->stockTransfer->created_by,
            'created_at' => now(),
        ]);
    }
}

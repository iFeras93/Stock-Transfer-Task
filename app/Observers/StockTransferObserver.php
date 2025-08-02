<?php

namespace App\Observers;


use App\Models\StockTransfer;
use App\Events\StockTransferCreated;
use App\Events\StockTransferStatusChanged;
use App\Enums\StockTransferStatus;

class StockTransferObserver
{
    public function created(StockTransfer $stockTransfer): void
    {
        event(new StockTransferCreated($stockTransfer));
    }

    public function updating(StockTransfer $stockTransfer): void
    {
        if ($stockTransfer->isDirty('status')) {
            $fromStatus = StockTransferStatus::from($stockTransfer->getOriginal('status'));
            $toStatus = StockTransferStatus::from($stockTransfer->status);

            event(new StockTransferStatusChanged(
                $stockTransfer,
                $fromStatus,
                $toStatus,
                auth()->user()
            ));
        }
    }
}

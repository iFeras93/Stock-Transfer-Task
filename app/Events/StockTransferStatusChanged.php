<?php

namespace App\Events;

use App\Models\StockTransfer;
use App\Enums\StockTransferStatus;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockTransferStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StockTransfer       $stockTransfer,
        public StockTransferStatus $fromStatus,
        public StockTransferStatus $toStatus,
        public ?User               $user = null
    ){}
}

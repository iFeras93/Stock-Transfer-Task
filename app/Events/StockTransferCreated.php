<?php

namespace App\Events;
use App\Models\StockTransfer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockTransferCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public StockTransfer $stockTransfer) {}
}

<?php

namespace App\Models;

use App\Enums\StockTransferStatus;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'delivery_integration_id',
        'warehouse_from_id',
        'warehouse_to_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'status' => StockTransferStatus::class,
    ];

    public function warehouseFrom(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_from_id');
    }

    public function warehouseTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_to_id');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(StockTransferProduct::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(StockTransferActivity::class)->orderBy('created_at', 'desc');
    }

    public function canChangeStatus(StockTransferStatus $newStatus, User $user): bool
    {
        return app(StockTransferService::class)->canChangeStatus($this, $newStatus, $user);
    }

    public function getNextAllowedStatuses(User $user): array
    {
        return app(StockTransferService::class)->getNextAllowedStatuses($this, $user);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];

    /**
     * Get the stock transfers where this warehouse is the sending warehouse.
     */
    public function sentTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'warehouse_from_id');
    }

    /**
     * Get the stock transfers where this warehouse is the receiving warehouse.
     */
    public function receivedTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'warehouse_to_id');
    }
}

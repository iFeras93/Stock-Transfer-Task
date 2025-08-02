<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
    ];

    /**
     * Get the stock transfer products that belong to this product.
     */
    public function stockTransferProducts(): HasMany
    {
        return $this->hasMany(StockTransferProduct::class);
    }
}

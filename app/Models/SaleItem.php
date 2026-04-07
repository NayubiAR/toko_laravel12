<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'product_name', 'product_sku',
        'quantity', 'unit_price', 'buy_price', 'discount',
        'tax_amount', 'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'buy_price'  => 'decimal:2',
        'discount'   => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    // ── Relationships ──

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ──

    public function getProfitAttribute(): float
    {
        return ($this->unit_price - $this->buy_price) * $this->quantity;
    }
}

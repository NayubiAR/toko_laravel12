<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'type', 'quantity', 'stock_before', 'stock_after',
        'reference_type', 'reference_id', 'cost_per_unit', 'notes', 'user_id',
    ];

    protected $casts = [
        'type'          => StockMovementType::class,
        'cost_per_unit' => 'decimal:2',
    ];

    // ── Relationships ──

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ──

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }
}

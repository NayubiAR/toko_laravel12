<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'sku', 'barcode', 'name', 'description', 'image',
        'category_id', 'supplier_id',
        'buy_price', 'sell_price', 'wholesale_price',
        'stock', 'min_stock', 'unit',
        'is_active', 'is_taxable',
    ];

    protected $casts = [
        'buy_price'      => 'decimal:2',
        'sell_price'     => 'decimal:2',
        'wholesale_price'=> 'decimal:2',
        'is_active'      => 'boolean',
        'is_taxable'     => 'boolean',
    ];

    // ── Relationships ──

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // ── Accessors ──

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getProfitAttribute(): float
    {
        return $this->sell_price - $this->buy_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->sell_price == 0) return 0;
        return round(($this->profit / $this->sell_price) * 100, 2);
    }

    public function getFormattedSellPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->sell_price, 0, ',', '.');
    }

    public function getFormattedBuyPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->buy_price, 0, ',', '.');
    }

    // ── Activity Log ──

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'buy_price', 'sell_price', 'stock', 'min_stock', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('product');
    }
}

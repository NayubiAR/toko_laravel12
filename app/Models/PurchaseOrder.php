<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'po_number', 'supplier_id', 'user_id', 'status',
        'subtotal', 'tax_amount', 'shipping_cost', 'discount_amount', 'grand_total',
        'order_date', 'expected_date', 'received_at', 'notes',
    ];

    protected $casts = [
        'status'          => OrderStatus::class,
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'shipping_cost'   => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total'     => 'decimal:2',
        'order_date'      => 'date',
        'expected_date'   => 'date',
        'received_at'     => 'datetime',
    ];

    // ── Relationships ──

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // ── Activity Log ──

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'grand_total'])
            ->logOnlyDirty()
            ->useLogName('purchase_order');
    }
}

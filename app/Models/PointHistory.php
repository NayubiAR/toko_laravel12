<?php

namespace App\Models;

use App\Enums\PointHistoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointHistory extends Model
{
    protected $fillable = [
        'customer_id', 'sale_id', 'type',
        'points', 'balance_before', 'balance_after',
        'notes', 'expires_at',
    ];

    protected $casts = [
        'type'       => PointHistoryType::class,
        'expires_at' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}

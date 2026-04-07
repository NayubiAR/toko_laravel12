<?php

namespace App\Models;

use App\Enums\CashFlowType;
use App\Enums\CashFlowCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashFlow extends Model
{
    protected $fillable = [
        'type', 'category', 'amount', 'description',
        'reference_type', 'reference_id', 'user_id', 'date',
    ];

    protected $casts = [
        'type'     => CashFlowType::class,
        'category' => CashFlowCategory::class,
        'amount'   => 'decimal:2',
        'date'     => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ──

    public function scopeIncome($query)
    {
        return $query->where('type', CashFlowType::Income);
    }

    public function scopeExpense($query)
    {
        return $query->where('type', CashFlowType::Expense);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}

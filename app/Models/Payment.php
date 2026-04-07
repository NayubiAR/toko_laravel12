<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'sale_id', 'method', 'amount', 'reference_number', 'status',
        'bank_name', 'account_number', 'account_holder',
        'proof_image', 'paid_at', 'verified_at', 'verified_by',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'method'      => PaymentMethod::class,
        'status'      => PaymentStatus::class,
        'paid_at'     => 'datetime',
        'verified_at' => 'datetime',
    ];

    // ── Relationships ──

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

<?php

namespace App\Models;

use App\Enums\PaymentMethod;
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
        'paid_at'     => 'datetime',
        'verified_at' => 'datetime',
        // status NOT cast to enum — tabel payments pakai 'success/pending/failed/refunded'
        // yang berbeda dari PaymentStatus enum ('paid/pending/partial/failed/refunded')
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

    // ── Accessors ──

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'success'  => 'Berhasil',
            'pending'  => 'Menunggu',
            'failed'   => 'Gagal',
            'refunded' => 'Refund',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'success'  => 'emerald',
            'pending'  => 'amber',
            'failed'   => 'red',
            'refunded' => 'gray',
            default    => 'slate',
        };
    }
}
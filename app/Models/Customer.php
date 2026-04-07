<?php

namespace App\Models;

use App\Enums\CustomerTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'address', 'gender',
        'birth_date', 'points', 'tier', 'total_spent',
        'member_since', 'is_active',
    ];

    protected $casts = [
        'tier'         => CustomerTier::class,
        'birth_date'   => 'date',
        'member_since' => 'date',
        'total_spent'  => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    // ── Relationships ──

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function pointHistories(): HasMany
    {
        return $this->hasMany(PointHistory::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Accessors ──

    public function getDiscountPercentAttribute(): float
    {
        return $this->tier->discountPercent();
    }

    // ── Activity Log ──

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'points', 'tier', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('customer');
    }
}

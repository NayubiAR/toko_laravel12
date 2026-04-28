<?php

namespace App\Providers;

use App\Events\SaleCompleted;
use App\Listeners\CalculateLoyaltyPoints;
use App\Listeners\ClearDashboardCache;
use App\Listeners\DeductStock;
use App\Listeners\RecordSaleCashFlow;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SaleCompleted::class => [
            DeductStock::class,
            RecordSaleCashFlow::class,
            CalculateLoyaltyPoints::class,
            ClearDashboardCache::class, // ← Cache otomatis di-clear setelah transaksi
        ],
    ];

    public function boot(): void
    {
        //
    }
}
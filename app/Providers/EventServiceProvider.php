<?php

namespace App\Providers;

use App\Events\SaleCompleted;
use App\Listeners\CalculateLoyaltyPoints;
use App\Listeners\DeductStock;
use App\Listeners\RecordSaleCashFlow;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Event-listener mapping.
     *
     * Saat SaleCompleted di-dispatch, ketiga listener ini dijalankan berurutan:
     * 1. DeductStock         → Kurangi stok produk + catat stock_movements
     * 2. RecordSaleCashFlow  → Catat pemasukan di cash_flows
     * 3. CalculateLoyaltyPoints → Hitung & tambah poin member
     */
    protected $listen = [
        SaleCompleted::class => [
            DeductStock::class,
            RecordSaleCashFlow::class,
            CalculateLoyaltyPoints::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
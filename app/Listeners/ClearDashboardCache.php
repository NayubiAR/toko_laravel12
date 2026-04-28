<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    public function handle(SaleCompleted $event): void
    {
        // Clear cache dashboard agar data terbaru muncul
        Cache::forget('dashboard.today_stats');
        Cache::forget('dashboard.admin_stats');
        Cache::forget('dashboard.weekly_sales');
        Cache::forget('dashboard.top_products');
        Cache::forget('dashboard.low_stock_products');
    }
}
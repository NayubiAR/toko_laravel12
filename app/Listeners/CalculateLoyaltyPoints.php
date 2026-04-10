<?php

namespace App\Listeners;

use App\Enums\PointHistoryType;
use App\Events\SaleCompleted;
use App\Models\PointHistory;
use App\Models\Setting;

class CalculateLoyaltyPoints
{
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        // Hanya proses jika ada customer (member)
        if (!$sale->customer_id) {
            return;
        }

        $customer = $sale->customer;
        $pointsPerAmount = (int) Setting::get('points_per_amount', 10000);
        $expiryDays = (int) Setting::get('points_expiry_days', 365);

        if ($pointsPerAmount <= 0) return;

        // Hitung poin: setiap Rp 10.000 belanja = 1 poin
        $pointsEarned = (int) floor($sale->grand_total / $pointsPerAmount);

        if ($pointsEarned <= 0) return;

        $balanceBefore = $customer->points;
        $balanceAfter = $balanceBefore + $pointsEarned;

        // Catat riwayat poin
        PointHistory::create([
            'customer_id'    => $customer->id,
            'sale_id'        => $sale->id,
            'type'           => PointHistoryType::Earned->value,
            'points'         => $pointsEarned,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'notes'          => "Poin dari transaksi {$sale->invoice_number}",
            'expires_at'     => now()->addDays($expiryDays),
        ]);

        // Update poin & total belanja customer
        $customer->update([
            'points'      => $balanceAfter,
            'total_spent' => $customer->total_spent + $sale->grand_total,
        ]);

        // Update poin earned di sale
        $sale->update(['points_earned' => $pointsEarned]);
    }
}
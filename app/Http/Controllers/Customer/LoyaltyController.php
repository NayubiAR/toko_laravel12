<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PointHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoyaltyController extends Controller
{
    /**
     * Adjust poin manual (tambah/kurangi).
     */
    public function adjustPoints(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'type'   => 'required|in:add,subtract',
            'points' => 'required|integer|min:1',
            'notes'  => 'nullable|string|max:500',
        ]);

        $points = (int) $validated['points'];
        $isAdd = $validated['type'] === 'add';

        if (!$isAdd && $points > $customer->points) {
            return back()->with('error', "Poin tidak cukup. Poin saat ini: {$customer->points}");
        }

        DB::transaction(function () use ($customer, $points, $isAdd, $validated) {
            $balanceBefore = $customer->points;
            $change = $isAdd ? $points : -$points;
            $balanceAfter = $balanceBefore + $change;

            PointHistory::create([
                'customer_id'    => $customer->id,
                'sale_id'        => null,
                'type'           => $isAdd ? 'bonus' : 'adjusted',
                'points'         => $change,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'notes'          => $validated['notes'] ?? ($isAdd ? 'Penambahan poin manual' : 'Pengurangan poin manual'),
            ]);

            $customer->update(['points' => $balanceAfter]);

            // Cek upgrade tier
            $this->checkTierUpgrade($customer);

            activity('loyalty')
                ->causedBy(Auth::user())
                ->performedOn($customer)
                ->withProperties([
                    'action' => $isAdd ? 'add' : 'subtract',
                    'points' => $points,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                ])
                ->log($isAdd ? 'Penambahan poin manual' : 'Pengurangan poin manual');
        });

        $action = $isAdd ? 'ditambahkan' : 'dikurangi';
        return back()->with('success', "{$points} poin berhasil {$action} untuk {$customer->name}.");
    }

    /**
     * Cek dan upgrade tier customer berdasarkan total_spent.
     */
    private function checkTierUpgrade(Customer $customer): void
    {
        $silverThreshold = (int) Setting::get('silver_threshold', 1000000);
        $goldThreshold = (int) Setting::get('gold_threshold', 5000000);
        $platinumThreshold = (int) Setting::get('platinum_threshold', 15000000);

        $newTier = 'bronze';
        if ($customer->total_spent >= $platinumThreshold) {
            $newTier = 'platinum';
        } elseif ($customer->total_spent >= $goldThreshold) {
            $newTier = 'gold';
        } elseif ($customer->total_spent >= $silverThreshold) {
            $newTier = 'silver';
        }

        $currentTier = $customer->tier instanceof \App\Enums\CustomerTier
            ? $customer->tier->value
            : $customer->tier;

        if ($currentTier !== $newTier) {
            $customer->update(['tier' => $newTier]);
        }
    }
}
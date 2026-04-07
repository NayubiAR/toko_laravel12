<?php

namespace App\Support\Traits;

use App\Support\Helpers\CurrencyHelper;

trait HasFormattedPrice
{
    /**
     * Format any price column.
     * Usage: $product->formatPrice('sell_price') => "Rp 150.000"
     */
    public function formatPrice(string $column): string
    {
        return CurrencyHelper::format($this->{$column});
    }
}

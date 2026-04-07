<?php

namespace App\Support\Helpers;

use App\Models\Product;
use Illuminate\Support\Str;

class SkuGenerator
{
    /**
     * Generate SKU: PRD-ELK-00001
     * Format: PRD-{3 huruf kategori}-{5 digit sequence}
     */
    public static function generate(string $categoryName): string
    {
        $categoryCode = strtoupper(Str::substr(
            preg_replace('/[^A-Za-z]/', '', $categoryName), 0, 3
        ));

        if (strlen($categoryCode) < 3) {
            $categoryCode = str_pad($categoryCode, 3, 'X');
        }

        $last = Product::withTrashed()
            ->where('sku', 'like', "PRD-{$categoryCode}-%")
            ->count() + 1;

        return sprintf('PRD-%s-%05d', $categoryCode, $last);
    }
}

<?php

namespace App\Support\Helpers;

class CurrencyHelper
{
    /**
     * Format angka ke format Rupiah.
     * CurrencyHelper::format(150000) => "Rp 150.000"
     */
    public static function format(float|int $amount, bool $withPrefix = true): string
    {
        $formatted = number_format(abs($amount), 0, ',', '.');
        $prefix = $withPrefix ? 'Rp ' : '';
        $sign = $amount < 0 ? '-' : '';

        return $sign . $prefix . $formatted;
    }

    /**
     * Parse string Rupiah ke angka.
     * CurrencyHelper::parse("Rp 150.000") => 150000
     */
    public static function parse(string $value): float
    {
        $cleaned = preg_replace('/[^\d,\-]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }
}

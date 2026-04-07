<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    */
    'tax_rate' => env('POS_TAX_RATE', 11),
    'tax_included' => env('POS_TAX_INCLUDED', false),

    /*
    |--------------------------------------------------------------------------
    | Invoice Configuration
    |--------------------------------------------------------------------------
    */
    'invoice_prefix' => env('POS_INVOICE_PREFIX', 'INV'),
    'po_prefix' => env('POS_PO_PREFIX', 'PO'),

    /*
    |--------------------------------------------------------------------------
    | Loyalty Configuration
    |--------------------------------------------------------------------------
    */
    'loyalty' => [
        'points_per_amount' => env('POS_POINTS_PER_AMOUNT', 10000),
        'point_value' => env('POS_POINT_VALUE', 100),
        'min_redeem' => env('POS_MIN_REDEEM_POINTS', 100),
        'expiry_days' => env('POS_POINTS_EXPIRY_DAYS', 365),
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Configuration
    |--------------------------------------------------------------------------
    */
    'receipt' => [
        'width' => env('POS_RECEIPT_WIDTH', 80),
        'header' => env('POS_RECEIPT_HEADER', 'Terima Kasih'),
        'footer' => env('POS_RECEIPT_FOOTER', 'Barang yang sudah dibeli tidak dapat dikembalikan'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stock Configuration
    |--------------------------------------------------------------------------
    */
    'default_min_stock' => env('POS_DEFAULT_MIN_STOCK', 5),
    'low_stock_notify' => env('POS_LOW_STOCK_NOTIFY', true),
];

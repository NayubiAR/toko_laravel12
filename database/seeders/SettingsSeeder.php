<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── General ──
            ['group' => 'general', 'key' => 'store_name',     'value' => 'Toko Saya',             'type' => 'string',  'description' => 'Nama toko'],
            ['group' => 'general', 'key' => 'store_address',  'value' => 'Jl. Contoh No. 123',    'type' => 'string',  'description' => 'Alamat toko'],
            ['group' => 'general', 'key' => 'store_phone',    'value' => '08123456789',            'type' => 'string',  'description' => 'Nomor telepon toko'],
            ['group' => 'general', 'key' => 'store_email',    'value' => 'info@tokosaya.com',      'type' => 'string',  'description' => 'Email toko'],
            ['group' => 'general', 'key' => 'store_logo',     'value' => null,                     'type' => 'string',  'description' => 'Path logo toko'],
            ['group' => 'general', 'key' => 'currency',       'value' => 'IDR',                    'type' => 'string',  'description' => 'Mata uang'],

            // ── Tax ──
            ['group' => 'tax', 'key' => 'tax_rate',           'value' => '11',                     'type' => 'integer', 'description' => 'Rate PPN dalam persen'],
            ['group' => 'tax', 'key' => 'tax_included',       'value' => 'false',                  'type' => 'boolean', 'description' => 'Apakah harga sudah termasuk pajak'],

            // ── Loyalty ──
            ['group' => 'loyalty', 'key' => 'points_per_amount',     'value' => '10000',           'type' => 'integer', 'description' => 'Setiap berapa rupiah dapat 1 poin'],
            ['group' => 'loyalty', 'key' => 'point_value',           'value' => '100',             'type' => 'integer', 'description' => '1 poin bernilai berapa rupiah'],
            ['group' => 'loyalty', 'key' => 'min_redeem_points',     'value' => '100',             'type' => 'integer', 'description' => 'Minimal poin untuk redeem'],
            ['group' => 'loyalty', 'key' => 'points_expiry_days',    'value' => '365',             'type' => 'integer', 'description' => 'Masa berlaku poin (hari)'],
            ['group' => 'loyalty', 'key' => 'silver_threshold',      'value' => '1000000',         'type' => 'integer', 'description' => 'Total belanja untuk tier Silver'],
            ['group' => 'loyalty', 'key' => 'gold_threshold',        'value' => '5000000',         'type' => 'integer', 'description' => 'Total belanja untuk tier Gold'],
            ['group' => 'loyalty', 'key' => 'platinum_threshold',    'value' => '15000000',        'type' => 'integer', 'description' => 'Total belanja untuk tier Platinum'],

            // ── Receipt ──
            ['group' => 'receipt', 'key' => 'receipt_header', 'value' => 'Terima Kasih',           'type' => 'string',  'description' => 'Header struk'],
            ['group' => 'receipt', 'key' => 'receipt_footer', 'value' => 'Barang yang sudah dibeli tidak dapat dikembalikan', 'type' => 'string', 'description' => 'Footer struk'],
            ['group' => 'receipt', 'key' => 'receipt_width',  'value' => '80',                     'type' => 'integer', 'description' => 'Lebar struk thermal (mm)'],

            // ── Invoice ──
            ['group' => 'invoice', 'key' => 'invoice_prefix',        'value' => 'INV',            'type' => 'string',  'description' => 'Prefix nomor invoice'],
            ['group' => 'invoice', 'key' => 'po_prefix',             'value' => 'PO',             'type' => 'string',  'description' => 'Prefix nomor PO'],
            ['group' => 'invoice', 'key' => 'customer_prefix',       'value' => 'MBR',            'type' => 'string',  'description' => 'Prefix kode member'],
            ['group' => 'invoice', 'key' => 'supplier_prefix',       'value' => 'SUP',            'type' => 'string',  'description' => 'Prefix kode supplier'],

            // ── Low Stock ──
            ['group' => 'inventory', 'key' => 'default_min_stock',   'value' => '5',              'type' => 'integer', 'description' => 'Default minimum stok untuk alert'],
            ['group' => 'inventory', 'key' => 'low_stock_notify',    'value' => 'true',           'type' => 'boolean', 'description' => 'Aktifkan notifikasi low stock'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

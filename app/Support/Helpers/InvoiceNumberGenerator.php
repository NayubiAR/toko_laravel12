<?php

namespace App\Support\Helpers;

use App\Models\Sale;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Setting;

class InvoiceNumberGenerator
{
    /**
     * Generate invoice number: INV-20260408-00001
     */
    public static function sale(): string
    {
        $prefix = Setting::get('invoice_prefix', 'INV');
        $date = now()->format('Ymd');
        $last = Sale::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $date, $last);
    }

    /**
     * Generate PO number: PO-20260408-00001
     */
    public static function purchaseOrder(): string
    {
        $prefix = Setting::get('po_prefix', 'PO');
        $date = now()->format('Ymd');
        $last = PurchaseOrder::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $date, $last);
    }

    /**
     * Generate member code: MBR-00001
     */
    public static function customer(): string
    {
        $prefix = Setting::get('customer_prefix', 'MBR');
        $last = Customer::withTrashed()->count() + 1;

        return sprintf('%s-%05d', $prefix, $last);
    }

    /**
     * Generate supplier code: SUP-001
     */
    public static function supplier(): string
    {
        $prefix = Setting::get('supplier_prefix', 'SUP');
        $last = Supplier::withTrashed()->count() + 1;

        return sprintf('%s-%03d', $prefix, $last);
    }
}

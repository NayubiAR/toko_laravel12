<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Cetak struk thermal (58mm / 80mm).
     */
    public function thermal(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'customer', 'payments']);

        $store = [
            'name'    => Setting::get('store_name', 'Kios Adiva'),
            'address' => Setting::get('store_address', ''),
            'phone'   => Setting::get('store_phone', ''),
            'header'  => Setting::get('receipt_header', 'Terima Kasih'),
            'footer'  => Setting::get('receipt_footer', 'Barang yang sudah dibeli tidak dapat dikembalikan'),
        ];

        $width = (int) Setting::get('receipt_width', 80);
        // 80mm = ~226pt, 58mm = ~164pt
        $paperWidth = $width === 58 ? 164 : 226;

        $pdf = Pdf::loadView('pdf.receipt-thermal', compact('sale', 'store'))
            ->setPaper([0, 0, $paperWidth, 800], 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->stream("struk-{$sale->invoice_number}.pdf");
    }

    /**
     * Cetak struk format A4.
     */
    public function a4(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'customer', 'payments']);

        $store = [
            'name'    => Setting::get('store_name', 'Kios Adiva'),
            'address' => Setting::get('store_address', ''),
            'phone'   => Setting::get('store_phone', ''),
            'email'   => Setting::get('store_email', ''),
        ];

        $pdf = Pdf::loadView('pdf.receipt-a4', compact('sale', 'store'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->stream("invoice-{$sale->invoice_number}.pdf");
    }

    /**
     * Download struk (bukan stream).
     */
    public function download(Sale $sale, string $format = 'thermal')
    {
        if ($format === 'a4') {
            $sale->load(['items.product', 'user', 'customer', 'payments']);

            $store = [
                'name'    => Setting::get('store_name', 'Kios Adiva'),
                'address' => Setting::get('store_address', ''),
                'phone'   => Setting::get('store_phone', ''),
                'email'   => Setting::get('store_email', ''),
            ];

            $pdf = Pdf::loadView('pdf.receipt-a4', compact('sale', 'store'))
                ->setPaper('a4', 'portrait');

            return $pdf->download("invoice-{$sale->invoice_number}.pdf");
        }

        return $this->thermal($sale);
    }
}
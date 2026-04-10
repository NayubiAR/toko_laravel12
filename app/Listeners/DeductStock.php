<?php

namespace App\Listeners;

use App\Enums\StockMovementType;
use App\Events\SaleCompleted;
use App\Models\Sale;
use App\Models\StockMovement;

class DeductStock
{
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        foreach ($sale->items as $item) {
            $product = $item->product;
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore - $item->quantity;

            // Update stok produk
            $product->update(['stock' => $stockAfter]);

            // Catat mutasi stok
            StockMovement::create([
                'product_id'     => $product->id,
                'type'           => StockMovementType::Out->value,
                'quantity'       => -$item->quantity,
                'stock_before'   => $stockBefore,
                'stock_after'    => $stockAfter,
                'reference_type' => Sale::class,
                'reference_id'   => $sale->id,
                'cost_per_unit'  => $item->buy_price,
                'notes'          => "Penjualan {$sale->invoice_number}",
                'user_id'        => $sale->user_id,
            ]);
        }
    }
}
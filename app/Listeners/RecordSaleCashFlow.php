<?php

namespace App\Listeners;

use App\Enums\CashFlowCategory;
use App\Enums\CashFlowType;
use App\Events\SaleCompleted;
use App\Models\CashFlow;
use App\Models\Sale;

class RecordSaleCashFlow
{
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        CashFlow::create([
            'type'           => CashFlowType::Income->value,
            'category'       => CashFlowCategory::Sale->value,
            'amount'         => $sale->grand_total,
            'description'    => "Penjualan {$sale->invoice_number}",
            'reference_type' => Sale::class,
            'reference_id'   => $sale->id,
            'user_id'        => $sale->user_id,
            'date'           => $sale->created_at->toDateString(),
        ]);
    }
}
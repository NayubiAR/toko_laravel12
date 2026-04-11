<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            line-height: 1.4;
            color: #000;
            padding: 8px;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .small { font-size: 7.5px; }
        .large { font-size: 12px; }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .divider-double {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 4px;
        }

        .row .label { flex: 1; }
        .row .value { text-align: right; white-space: nowrap; }

        .item-name {
            font-weight: bold;
            margin-bottom: 1px;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            padding-left: 8px;
            font-size: 8px;
        }

        .total-section .row {
            margin-bottom: 2px;
        }

        .grand-total {
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- ═══ HEADER ═══ --}}
    <div class="center bold large">{{ $store['name'] }}</div>
    @if($store['address'])
        <div class="center small">{{ $store['address'] }}</div>
    @endif
    @if($store['phone'])
        <div class="center small">Telp: {{ $store['phone'] }}</div>
    @endif

    <div class="divider-double"></div>

    {{-- ═══ INFO TRANSAKSI ═══ --}}
    <div class="row small">
        <span>{{ $sale->invoice_number }}</span>
        <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="row small">
        <span>Kasir: {{ $sale->user?->name ?? '-' }}</span>
    </div>
    @if($sale->customer)
        <div class="row small">
            <span>Member: {{ $sale->customer->name }}</span>
        </div>
    @endif

    <div class="divider"></div>

    {{-- ═══ ITEMS ═══ --}}
    @foreach($sale->items as $item)
        <div class="item-name">{{ $item->product_name }}</div>
        <div class="item-detail">
            <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($item->discount > 0)
            <div class="item-detail small">
                <span>&nbsp;&nbsp;Diskon</span>
                <span>-Rp {{ number_format($item->discount, 0, ',', '.') }}</span>
            </div>
        @endif
    @endforeach

    <div class="divider"></div>

    {{-- ═══ TOTALS ═══ --}}
    <div class="total-section">
        <div class="row">
            <span class="label">Subtotal</span>
            <span class="value">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>

        @if($sale->discount_amount > 0)
            <div class="row">
                <span class="label">Diskon ({{ $sale->discount_percent }}%)</span>
                <span class="value">-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="row">
            <span class="label">PPN ({{ $sale->tax_rate }}%)</span>
            <span class="value">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
        </div>

        <div class="divider-double"></div>

        <div class="row grand-total">
            <span class="label">TOTAL</span>
            <span class="value">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div class="row">
            <span class="label">{{ $sale->payment_method->label() }}</span>
            <span class="value">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>

        @if($sale->change_amount > 0)
            <div class="row bold">
                <span class="label">Kembalian</span>
                <span class="value">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if($sale->points_earned > 0)
            <div class="divider"></div>
            <div class="row small">
                <span class="label">Poin didapat</span>
                <span class="value bold">+{{ $sale->points_earned }} poin</span>
            </div>
            @if($sale->customer)
                <div class="row small">
                    <span class="label">Total poin</span>
                    <span class="value">{{ $sale->customer->points }} poin</span>
                </div>
            @endif
        @endif
    </div>

    <div class="divider-double"></div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="center small" style="margin-top: 4px;">
        {{ $store['header'] }}
    </div>
    <div class="center small" style="margin-top: 2px;">
        {{ $store['footer'] }}
    </div>
    <div class="center small" style="margin-top: 6px; color: #666;">
        {{ $sale->created_at->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
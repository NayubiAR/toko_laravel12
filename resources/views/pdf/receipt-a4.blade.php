<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1e293b;
            padding: 40px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }

        .store-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .store-info {
            font-size: 10px;
            color: #64748b;
            line-height: 1.6;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .invoice-number {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        .invoice-date {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── Info Grid ── */
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
        }

        .info-box h3 {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .info-box p {
            font-size: 11px;
            color: #334155;
            margin-bottom: 2px;
        }

        .info-box .name {
            font-weight: bold;
            font-size: 12px;
            color: #0f172a;
        }

        /* ── Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table thead th {
            background: #f1f5f9;
            padding: 10px 12px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: bold;
            border-bottom: 2px solid #e2e8f0;
        }

        .items-table thead th.right { text-align: right; }
        .items-table thead th.center { text-align: center; }

        .items-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }

        .items-table tbody td.right { text-align: right; }
        .items-table tbody td.center { text-align: center; }

        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #e2e8f0;
        }

        .product-name {
            font-weight: bold;
            color: #0f172a;
        }

        .product-sku {
            font-size: 9px;
            color: #94a3b8;
            font-family: 'Courier New', monospace;
        }

        /* ── Totals ── */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .totals-box {
            width: 280px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 11px;
        }

        .totals-row .label { color: #64748b; }
        .totals-row .value { font-weight: bold; color: #334155; }
        .totals-row.discount .value { color: #ef4444; }

        .totals-divider {
            border-top: 2px solid #0f172a;
            margin: 6px 0;
        }

        .totals-row.grand {
            font-size: 16px;
            padding: 8px 0;
        }

        .totals-row.grand .label { color: #0f172a; font-weight: bold; }
        .totals-row.grand .value { color: #3b82f6; }

        /* ── Payment ── */
        .payment-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 30px;
        }

        .payment-info h3 {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
        }

        .payment-row .label { color: #64748b; }
        .payment-row .value { font-weight: bold; }

        .status-paid {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        .footer p { margin-bottom: 2px; }
    </style>
</head>
<body>

    {{-- ═══ HEADER ═══ --}}
    <div class="header">
        <div>
            <div class="store-name">{{ $store['name'] }}</div>
            <div class="store-info">
                @if($store['address']){{ $store['address'] }}<br>@endif
                @if($store['phone'])Telp: {{ $store['phone'] }}@endif
                @if($store['email']) | {{ $store['email'] }}@endif
            </div>
        </div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="invoice-number">{{ $sale->invoice_number }}</div>
            <div class="invoice-date">{{ $sale->created_at->translatedFormat('d F Y, H:i') }}</div>
        </div>
    </div>

    {{-- ═══ INFO ═══ --}}
    <div class="info-grid">
        <div class="info-box">
            <h3>Kasir</h3>
            <p class="name">{{ $sale->user?->name ?? '-' }}</p>
        </div>
        <div class="info-box">
            <h3>Customer</h3>
            @if($sale->customer)
                <p class="name">{{ $sale->customer->name }}</p>
                <p>{{ $sale->customer->code }}</p>
                @if($sale->customer->phone)
                    <p>{{ $sale->customer->phone }}</p>
                @endif
            @else
                <p class="name">Umum</p>
            @endif
        </div>
        <div class="info-box" style="text-align: right;">
            <h3>Metode Bayar</h3>
            <p class="name">{{ $sale->payment_method->label() }}</p>
            <p>
                <span class="{{ $sale->payment_status->value === 'paid' ? 'status-paid' : 'status-pending' }}">
                    {{ $sale->payment_status->label() }}
                </span>
            </p>
        </div>
    </div>

    {{-- ═══ ITEMS TABLE ═══ --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 40%;">Produk</th>
                <th class="center" style="width: 10%;">Qty</th>
                <th class="right" style="width: 18%;">Harga</th>
                <th class="right" style="width: 12%;">Diskon</th>
                <th class="right" style="width: 18%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        <div class="product-sku">{{ $item->product_sku }}</div>
                    </td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="right" style="color: {{ $item->discount > 0 ? '#ef4444' : '#94a3b8' }};">
                        {{ $item->discount > 0 ? '-Rp '.number_format($item->discount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="right" style="font-weight: bold;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ═══ TOTALS ═══ --}}
    <div class="totals-section">
        <div class="totals-box">
            <div class="totals-row">
                <span class="label">Subtotal</span>
                <span class="value">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
            </div>

            @if($sale->discount_amount > 0)
                <div class="totals-row discount">
                    <span class="label">Diskon ({{ $sale->discount_percent }}%)</span>
                    <span class="value">-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="totals-row">
                <span class="label">PPN ({{ $sale->tax_rate }}%)</span>
                <span class="value">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
            </div>

            <div class="totals-divider"></div>

            <div class="totals-row grand">
                <span class="label">TOTAL</span>
                <span class="value">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ PAYMENT INFO ═══ --}}
    <div class="payment-info">
        <h3>Detail Pembayaran</h3>
        <div class="payment-row">
            <span class="label">Dibayar</span>
            <span class="value">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>
        @if($sale->change_amount > 0)
            <div class="payment-row">
                <span class="label">Kembalian</span>
                <span class="value" style="color: #16a34a;">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($sale->points_earned > 0)
            <div class="payment-row">
                <span class="label">Poin Didapat</span>
                <span class="value" style="color: #d97706;">+{{ $sale->points_earned }} poin</span>
            </div>
        @endif
    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda berbelanja di {{ $store['name'] }}</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        <p style="margin-top: 8px; font-size: 8px;">Dicetak pada {{ now()->translatedFormat('d F Y, H:i:s') }}</p>
    </div>

</body>
</html>
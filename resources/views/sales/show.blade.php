@extends('components.layouts.app')

@section('title', 'Detail Transaksi')
@section('subtitle', $sale->invoice_number)

@section('content')
<div class="max-w-4xl">

    {{-- Top Bar: Back + Print Buttons --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Kembali
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('sales.receipt.thermal', $sale) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m0 0a48.159 48.159 0 018.5 0m0 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.5c-.621 0-1.125.504-1.125 1.125v3.659M18.25 7.209V3.375"/></svg>
                Cetak Struk
            </a>
            <a href="{{ route('sales.receipt.a4', $sale) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                Cetak Invoice A4
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Invoice Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Header Card --}}
            <div class="stat-card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 font-mono">{{ $sale->invoice_number }}</h2>
                        <p class="text-sm text-slate-500">{{ $sale->created_at->translatedFormat('l, d F Y — H:i') }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        {{ $sale->payment_status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($sale->payment_status->value === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                        {{ $sale->payment_status->label() }}
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-slate-400">Kasir</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $sale->user?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Customer</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $sale->customer?->name ?? 'Umum' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Metode Bayar</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $sale->payment_method->label() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Poin Didapat</p>
                        <p class="text-sm font-semibold text-amber-600">+{{ $sale->points_earned }}</p>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="stat-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Item Pembelian</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase">Produk</th>
                                <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase">Qty</th>
                                <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase">Harga</th>
                                <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase">Diskon</th>
                                <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-800">{{ $item->product_name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $item->product_sku }}</p>
                                </td>
                                <td class="px-6 py-3 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 text-right text-slate-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right text-red-500">{{ $item->discount > 0 ? '-Rp '.number_format($item->discount, 0, ',', '.') : '—' }}</td>
                                <td class="px-6 py-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($sale->notes)
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-2">Catatan</h3>
                <p class="text-sm text-slate-600">{{ $sale->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Right: Payment Summary --}}
        <div class="space-y-6">
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($sale->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Diskon ({{ $sale->discount_percent }}%)</span>
                        <span class="font-semibold text-red-500">-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">PPN ({{ $sale->tax_rate }}%)</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <hr class="border-slate-100">
                    <div class="flex justify-between">
                        <span class="font-bold text-slate-800">Grand Total</span>
                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <hr class="border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dibayar</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($sale->change_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kembalian</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payments --}}
            @if($sale->payments->isNotEmpty())
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Riwayat Pembayaran</h3>
                <div class="space-y-3">
                    @foreach($sale->payments as $payment)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $payment->method->label() }}</p>
                            <p class="text-xs text-slate-400">{{ $payment->paid_at?->format('d/m/Y H:i') ?? 'Belum dibayar' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            <span class="text-xs font-semibold {{ $payment->status === 'success' ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $payment->status_label }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('components.layouts.app')

@section('title', 'Detail Transaksi')
@section('subtitle', $sale->invoice_number)

@section('content')
<div class="max-w-4xl">

    <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>

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
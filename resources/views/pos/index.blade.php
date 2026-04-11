<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS — Kios Adiva</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }

        .pos-layout { height: 100vh; display: flex; flex-direction: column; background: #f1f5f9; }
        .pos-body { flex: 1; overflow: hidden; display: flex; }
        .product-grid { flex: 1; overflow-y: auto; padding: 16px; }
        .cart-panel { width: 400px; background: white; border-left: 1px solid #e2e8f0; display: flex; flex-direction: column; }

        .product-card {
            background: white; border: 1px solid #e2e8f0; border-radius: 12px;
            cursor: pointer; transition: all 0.15s ease; overflow: hidden;
        }
        .product-card:hover { border-color: #3b82f6; box-shadow: 0 2px 8px rgba(59,130,246,0.1); transform: translateY(-1px); }
        .product-card:active { transform: scale(0.98); }

        .cart-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .cart-item:hover { background: #f8fafc; }

        .qty-btn {
            width: 28px; height: 28px; border-radius: 8px; border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            background: white; transition: all 0.15s; font-size: 14px; font-weight: 600; color: #475569;
        }
        .qty-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }

        .payment-badge {
            padding: 10px 16px; border-radius: 12px; border: 2px solid #e2e8f0;
            cursor: pointer; transition: all 0.2s; text-align: center;
        }
        .payment-badge.active { border-color: #3b82f6; background: #eff6ff; }

        .pos-navbar { background: #0f172a; padding: 0 16px; height: 56px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 16px; }

        .success-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 60; display: flex; align-items: center; justify-content: center; }

        @keyframes cartPop { 0% { transform: scale(1); } 50% { transform: scale(1.08); } 100% { transform: scale(1); } }
        .cart-pop { animation: cartPop 0.2s ease; }

        @media (max-width: 1024px) { .cart-panel { width: 340px; } }
        @media (max-width: 768px) {
            .pos-body { flex-direction: column; }
            .cart-panel { width: 100%; height: 45vh; border-left: none; border-top: 1px solid #e2e8f0; }
            .product-grid { height: 55vh; }
        }
    </style>
</head>
<body>
<div class="pos-layout" x-data="posApp()" x-init="init()">

    {{-- ═══ NAVBAR ═══ --}}
    <div class="pos-navbar">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6, #10b981);">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h1 class="text-white font-bold text-sm">Kios Adiva</h1>
                <p class="text-slate-500 text-[10px]">Point of Sales</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-slate-400 text-xs hidden sm:block" x-text="currentTime"></span>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <span class="text-blue-400 text-xs font-bold">{{ Auth::user()->initials }}</span>
                </div>
                <span class="text-slate-300 text-sm font-medium hidden sm:block">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ BODY ═══ --}}
    <div class="pos-body">

        {{-- ── LEFT: Product Grid ── --}}
        <div class="product-grid">
            {{-- Search & Filter --}}
            <div class="flex gap-3 mb-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" x-model="search" @input.debounce.300ms="filterProducts()" placeholder="Cari produk atau scan barcode..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white" autofocus>
                </div>
                <select x-model="selectedCategory" @change="filterProducts()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Products --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="product-card" @click="addToCart(product)">
                        <div class="aspect-square bg-slate-50 flex items-center justify-center overflow-hidden">
                            <template x-if="product.image">
                                <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!product.image">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            </template>
                        </div>
                        <div class="p-3">
                            <p class="text-xs text-slate-800 font-semibold leading-tight line-clamp-2" x-text="product.name"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="product.sku"></p>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-sm font-bold text-blue-600" x-text="formatRupiah(product.sell_price)"></p>
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md"
                                    :class="product.stock <= 5 ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'"
                                    x-text="product.stock + ' ' + product.unit"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="filteredProducts.length === 0">
                <div class="text-center py-16">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <p class="text-sm text-slate-400">Produk tidak ditemukan</p>
                </div>
            </template>
        </div>

        {{-- ── RIGHT: Cart Panel ── --}}
        <div class="cart-panel">
            {{-- Cart Header --}}
            <div class="p-4 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-800">Keranjang</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg" x-text="cart.length + ' item'"></span>
                        <button @click="clearCart()" x-show="cart.length > 0" class="text-xs text-red-500 hover:text-red-600 font-semibold">Hapus</button>
                    </div>
                </div>
                {{-- Customer Select --}}
                <select x-model="customerId" class="w-full mt-3 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Tanpa member</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->code }}) — {{ ucfirst($cust->tier->value) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto">
                <template x-if="cart.length === 0">
                    <div class="text-center py-16 px-4">
                        <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                        <p class="text-sm text-slate-400">Keranjang kosong</p>
                        <p class="text-xs text-slate-300 mt-1">Klik produk untuk menambahkan</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="cart-item">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate" x-text="item.name"></p>
                            <p class="text-xs text-slate-400" x-text="formatRupiah(item.sell_price) + ' / ' + item.unit"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="qty-btn" @click="updateQty(index, -1)">−</button>
                            <span class="text-sm font-bold text-slate-800 w-8 text-center" x-text="item.quantity"></span>
                            <button class="qty-btn" @click="updateQty(index, 1)">+</button>
                        </div>
                        <div class="text-right min-w-[80px]">
                            <p class="text-sm font-bold text-slate-800" x-text="formatRupiah(item.sell_price * item.quantity)"></p>
                        </div>
                        <button @click="removeFromCart(index)" class="text-slate-300 hover:text-red-500 transition-colors ml-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Cart Footer: Totals --}}
            <div class="border-t border-slate-100 p-4 space-y-2 bg-slate-50/50" x-show="cart.length > 0" x-cloak>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Subtotal</span>
                    <span class="font-semibold text-slate-700" x-text="formatRupiah(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm items-center">
                    <span class="text-slate-500">Diskon (%)</span>
                    <input type="number" x-model.number="discountPercent" min="0" max="100" step="0.5"
                        class="w-20 px-2 py-1 rounded-lg border border-slate-200 text-sm text-right focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div class="flex justify-between text-sm" x-show="discountAmount > 0">
                    <span class="text-red-500">Potongan</span>
                    <span class="font-semibold text-red-500" x-text="'-' + formatRupiah(discountAmount)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">PPN (<span x-text="taxRate"></span>%)</span>
                    <span class="font-semibold text-slate-700" x-text="formatRupiah(taxAmount)"></span>
                </div>
                <hr class="border-slate-200">
                <div class="flex justify-between">
                    <span class="text-base font-bold text-slate-800">Total</span>
                    <span class="text-xl font-bold text-blue-600" x-text="formatRupiah(grandTotal)"></span>
                </div>

                <button @click="showPaymentModal = true"
                    class="w-full mt-3 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                    Bayar
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ PAYMENT MODAL ═══ --}}
    <div x-show="showPaymentModal" x-cloak class="modal-overlay" @keydown.escape.window="showPaymentModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6" @click.away="showPaymentModal = false" x-transition>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Pembayaran</h3>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Total Display --}}
            <div class="text-center p-4 bg-slate-50 rounded-xl mb-6">
                <p class="text-sm text-slate-500">Total Pembayaran</p>
                <p class="text-3xl font-bold text-slate-800 mt-1" x-text="formatRupiah(grandTotal)"></p>
            </div>

            {{-- Payment Method --}}
            <p class="text-sm font-semibold text-slate-700 mb-3">Metode Pembayaran</p>
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="payment-badge" :class="paymentMethod === 'cash' ? 'active' : ''" @click="paymentMethod = 'cash'">
                    <svg class="w-6 h-6 mx-auto mb-1 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75"/></svg>
                    <p class="text-xs font-semibold text-slate-700">Cash</p>
                </div>
                <div class="payment-badge" :class="paymentMethod === 'qris' ? 'active' : ''" @click="paymentMethod = 'qris'">
                    <svg class="w-6 h-6 mx-auto mb-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/></svg>
                    <p class="text-xs font-semibold text-slate-700">QRIS</p>
                </div>
                <div class="payment-badge" :class="paymentMethod === 'bank_transfer' ? 'active' : ''" @click="paymentMethod = 'bank_transfer'">
                    <svg class="w-6 h-6 mx-auto mb-1 text-violet-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                    <p class="text-xs font-semibold text-slate-700">Transfer</p>
                </div>
            </div>

            {{-- Cash Input --}}
            <template x-if="paymentMethod === 'cash'">
                <div>
                    <p class="text-sm font-semibold text-slate-700 mb-2">Jumlah Bayar</p>
                    <div class="relative mb-3">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold">Rp</span>
                        <input type="number" x-model.number="paidAmount" min="0"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-lg font-bold text-right focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                            @input="calculateChange()">
                    </div>
                    {{-- Quick Amount Buttons --}}
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <template x-for="amount in quickAmounts" :key="amount">
                            <button @click="paidAmount = amount; calculateChange()" class="py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors" x-text="formatShort(amount)"></button>
                        </template>
                    </div>
                    {{-- Uang Pas Button --}}
                    <button @click="paidAmount = grandTotal; calculateChange()" class="w-full py-2 rounded-lg border border-blue-200 bg-blue-50 text-sm font-semibold text-blue-600 hover:bg-blue-100 transition-colors mb-4">Uang Pas</button>

                    {{-- Change --}}
                    <div class="flex justify-between items-center p-3 rounded-xl" :class="changeAmount >= 0 ? 'bg-emerald-50' : 'bg-red-50'">
                        <span class="text-sm font-semibold" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-700'">Kembalian</span>
                        <span class="text-lg font-bold" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-700'" x-text="formatRupiah(changeAmount)"></span>
                    </div>
                </div>
            </template>

            {{-- Reference Number for non-cash --}}
            <template x-if="paymentMethod !== 'cash'">
                <div>
                    <p class="text-sm font-semibold text-slate-700 mb-2">No. Referensi (opsional)</p>
                    <input type="text" x-model="referenceNumber" placeholder="Nomor referensi transaksi"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </template>

            {{-- Catatan --}}
            <div class="mt-4">
                <input type="text" x-model="saleNotes" placeholder="Catatan transaksi (opsional)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            {{-- Submit --}}
            <button @click="processCheckout()"
                :disabled="processing || (paymentMethod === 'cash' && paidAmount < grandTotal)"
                class="w-full mt-6 py-3.5 bg-blue-500 hover:bg-blue-600 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                <svg x-show="processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="processing ? 'Memproses...' : 'Proses Pembayaran'"></span>
            </button>
        </div>
    </div>

    {{-- ═══ SUCCESS MODAL ═══ --}}
    <div x-show="showSuccess" x-cloak class="success-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 text-center" x-transition>
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-1">Transaksi Berhasil!</h3>
            <p class="text-sm text-slate-500 mb-4" x-text="successData.invoice_number"></p>

            <div class="bg-slate-50 rounded-xl p-4 mb-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-bold text-slate-800" x-text="formatRupiah(successData.grand_total || 0)"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Dibayar</span><span class="font-semibold text-slate-700" x-text="formatRupiah(successData.paid_amount || 0)"></span></div>
                <div class="flex justify-between" x-show="successData.change_amount > 0"><span class="text-slate-500">Kembalian</span><span class="font-bold text-emerald-600" x-text="formatRupiah(successData.change_amount || 0)"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Metode</span><span class="font-semibold text-slate-700" x-text="successData.payment_method"></span></div>
                <div class="flex justify-between" x-show="successData.points_earned > 0"><span class="text-slate-500">Poin Didapat</span><span class="font-bold text-amber-600" x-text="'+' + successData.points_earned + ' poin'"></span></div>
            </div>

            <div class="flex gap-3 mb-3">
                <a :href="'/sales/' + successData.id + '/receipt'" target="_blank"
                    class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-colors text-center flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m0 0a48.159 48.159 0 018.5 0m0 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.5c-.621 0-1.125.504-1.125 1.125v3.659M18.25 7.209V3.375"/></svg>
                    Cetak Struk
                </a>
                <a :href="'/sales/' + successData.id + '/invoice'" target="_blank"
                    class="flex-1 py-2.5 border border-blue-200 text-blue-600 text-sm font-semibold rounded-xl hover:bg-blue-50 transition-colors text-center flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Invoice A4
                </a>
            </div>

            <div class="flex gap-3">
                <a :href="'/sales/' + successData.id" class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors text-center">Detail</a>
                <button @click="newTransaction()" class="flex-1 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl transition-colors">Transaksi Baru</button>
            </div>
        </div>
    </div>

</div>

<script>
function posApp() {
    return {
        // State
        allProducts: @json($productsJson),
        filteredProducts: [],
        search: '',
        selectedCategory: '',
        cart: [],
        customerId: '',
        discountPercent: 0,
        taxRate: {{ $taxRate }},

        // Payment
        showPaymentModal: false,
        paymentMethod: 'cash',
        paidAmount: 0,
        changeAmount: 0,
        referenceNumber: '',
        saleNotes: '',
        processing: false,

        // Success
        showSuccess: false,
        successData: {},

        // Time
        currentTime: '',

        init() {
            this.filteredProducts = this.allProducts;
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },

        updateTime() {
            this.currentTime = new Date().toLocaleString('id-ID', {
                weekday: 'short', day: '2-digit', month: 'short',
                year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        },

        filterProducts() {
            let result = this.allProducts;
            if (this.search) {
                const s = this.search.toLowerCase();
                result = result.filter(p =>
                    p.name.toLowerCase().includes(s) ||
                    p.sku.toLowerCase().includes(s)
                );
            }
            if (this.selectedCategory) {
                result = result.filter(p => p.category_id == this.selectedCategory);
            }
            this.filteredProducts = result;
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.quantity >= product.stock) {
                    alert('Stok tidak mencukupi!');
                    return;
                }
                existing.quantity++;
            } else {
                this.cart.push({ ...product, quantity: 1, discount: 0 });
            }
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            const newQty = item.quantity + delta;
            if (newQty <= 0) {
                this.removeFromCart(index);
            } else if (newQty > item.stock) {
                alert('Stok tidak mencukupi!');
            } else {
                item.quantity = newQty;
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Hapus semua item dari keranjang?')) {
                this.cart = [];
                this.discountPercent = 0;
                this.customerId = '';
            }
        },

        get subtotal() {
            return this.cart.reduce((sum, i) => sum + (i.sell_price * i.quantity), 0);
        },

        get discountAmount() {
            return this.subtotal * (this.discountPercent / 100);
        },

        get taxAmount() {
            return (this.subtotal - this.discountAmount) * (this.taxRate / 100);
        },

        get grandTotal() {
            return this.subtotal - this.discountAmount + this.taxAmount;
        },

        get quickAmounts() {
            const gt = this.grandTotal;
            const amounts = [];
            const bases = [1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000, 500000];
            for (const b of bases) {
                if (b >= gt) { amounts.push(b); if (amounts.length >= 4) break; }
            }
            while (amounts.length < 4) amounts.push(amounts[amounts.length-1] * 2 || 50000);
            return amounts;
        },

        calculateChange() {
            this.changeAmount = this.paidAmount - this.grandTotal;
        },

        async processCheckout() {
            if (this.cart.length === 0) return;
            if (this.paymentMethod === 'cash' && this.paidAmount < this.grandTotal) {
                alert('Jumlah bayar kurang!');
                return;
            }
            this.processing = true;

            try {
                const res = await fetch('/pos/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        items: this.cart.map(i => ({
                            product_id: i.id,
                            quantity: i.quantity,
                            discount: i.discount || 0,
                        })),
                        payment_method: this.paymentMethod,
                        paid_amount: this.paymentMethod === 'cash' ? this.paidAmount : this.grandTotal,
                        customer_id: this.customerId || null,
                        discount_percent: this.discountPercent,
                        reference_number: this.referenceNumber,
                        notes: this.saleNotes,
                    })
                });

                const data = await res.json();

                if (data.success) {
                    this.successData = data.data;
                    this.showPaymentModal = false;
                    this.showSuccess = true;
                } else {
                    let errorMsg = data.message || 'Terjadi kesalahan';
                    if (data.error) errorMsg += '\n\nDetail: ' + data.error;
                    if (data.file) errorMsg += '\nFile: ' + data.file;
                    alert(errorMsg);
                }
            } catch (err) {
                alert('Gagal memproses transaksi. Coba lagi.\n\n' + err.message);
                console.error(err);
            } finally {
                this.processing = false;
            }
        },

        newTransaction() {
            this.cart = [];
            this.customerId = '';
            this.discountPercent = 0;
            this.paidAmount = 0;
            this.changeAmount = 0;
            this.paymentMethod = 'cash';
            this.referenceNumber = '';
            this.saleNotes = '';
            this.showSuccess = false;
            this.successData = {};
            // Refresh products (stok berubah)
            window.location.reload();
        },

        formatRupiah(val) {
            return 'Rp ' + Math.round(val).toLocaleString('id-ID');
        },

        formatShort(val) {
            if (val >= 1000000) return (val/1000000) + 'jt';
            if (val >= 1000) return (val/1000) + 'rb';
            return val;
        }
    }
}
</script>
</body>
</html>
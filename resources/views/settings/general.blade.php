@extends('components.layouts.app')

@section('title', 'Pengaturan Toko')
@section('subtitle', 'Konfigurasi umum aplikasi Kios Adiva')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf @method('PUT')

        @php
            $groupLabels = [
                'general'   => ['Informasi Toko', 'Nama, alamat, dan kontak toko Anda'],
                'tax'       => ['Pajak', 'Konfigurasi PPN'],
                'loyalty'   => ['Program Loyalty', 'Pengaturan poin dan tier member'],
                'receipt'   => ['Struk / Receipt', 'Tampilan struk belanja'],
                'invoice'   => ['Prefix Kode', 'Awalan untuk nomor invoice, PO, member, supplier'],
                'inventory' => ['Inventaris', 'Pengaturan stok barang'],
            ];

            $fieldLabels = [
                'store_name' => 'Nama Toko', 'store_address' => 'Alamat', 'store_phone' => 'Telepon',
                'store_email' => 'Email', 'store_logo' => 'Path Logo', 'currency' => 'Mata Uang',
                'tax_rate' => 'Rate PPN (%)', 'tax_included' => 'Harga Termasuk Pajak',
                'points_per_amount' => 'Belanja per 1 Poin (Rp)', 'point_value' => 'Nilai 1 Poin (Rp)',
                'min_redeem_points' => 'Min. Poin untuk Redeem', 'points_expiry_days' => 'Masa Berlaku Poin (hari)',
                'silver_threshold' => 'Threshold Silver (Rp)', 'gold_threshold' => 'Threshold Gold (Rp)',
                'platinum_threshold' => 'Threshold Platinum (Rp)',
                'receipt_header' => 'Header Struk', 'receipt_footer' => 'Footer Struk', 'receipt_width' => 'Lebar Struk (mm)',
                'invoice_prefix' => 'Prefix Invoice', 'po_prefix' => 'Prefix PO',
                'customer_prefix' => 'Prefix Member', 'supplier_prefix' => 'Prefix Supplier',
                'default_min_stock' => 'Default Min. Stok', 'low_stock_notify' => 'Notifikasi Stok Rendah',
            ];
        @endphp

        @foreach($settings as $group => $items)
            @php $meta = $groupLabels[$group] ?? [ucfirst($group), '']; @endphp
            <div class="stat-card p-6 mb-6">
                <div class="mb-5">
                    <h3 class="font-bold text-slate-800">{{ $meta[0] }}</h3>
                    @if($meta[1])
                        <p class="text-sm text-slate-500 mt-0.5">{{ $meta[1] }}</p>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($items as $setting)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-start">
                            <label class="text-sm font-semibold text-slate-700 sm:pt-2.5">
                                {{ $fieldLabels[$setting->key] ?? $setting->key }}
                            </label>
                            <div class="sm:col-span-2">
                                @if($setting->type === 'boolean')
                                    <select name="settings[{{ $setting->key }}]"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                        <option value="true" {{ $setting->value === 'true' ? 'selected' : '' }}>Ya</option>
                                        <option value="false" {{ $setting->value === 'false' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                @elseif($setting->type === 'integer')
                                    <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @else
                                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @endif
                                @if($setting->description)
                                    <p class="text-xs text-slate-400 mt-1">{{ $setting->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
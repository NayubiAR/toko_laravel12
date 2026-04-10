<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-products');
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'category_id'     => 'required|exists:categories,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'buy_price'       => 'required|numeric|min:0',
            'sell_price'      => 'required|numeric|min:0|gte:buy_price',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock'       => 'required|integer|min:0',
            'unit'            => 'required|string|in:pcs,kg,ltr,box,dus,pack,lusin,meter,roll',
            'barcode'         => 'nullable|string|max:50|unique:products,barcode,' . $this->route('product')->id,
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'       => 'boolean',
            'is_taxable'      => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama produk wajib diisi.',
            'category_id.required'=> 'Kategori wajib dipilih.',
            'buy_price.required'  => 'Harga beli wajib diisi.',
            'sell_price.required' => 'Harga jual wajib diisi.',
            'sell_price.gte'      => 'Harga jual harus lebih besar atau sama dengan harga beli.',
            'min_stock.required'  => 'Minimum stok wajib diisi.',
            'unit.required'       => 'Satuan wajib dipilih.',
            'barcode.unique'      => 'Barcode sudah digunakan produk lain.',
            'image.max'           => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
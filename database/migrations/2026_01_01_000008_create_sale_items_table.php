<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->constrained()
                  ->restrictOnDelete();

            $table->string('product_name');                   // Snapshot nama saat transaksi
            $table->string('product_sku', 50);                // Snapshot SKU

            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);             // Harga saat transaksi
            $table->decimal('buy_price', 15, 2);              // HPP saat transaksi (untuk laba rugi)
            $table->decimal('discount', 15, 2)->default(0);   // Diskon per item
            $table->decimal('tax_amount', 15, 2)->default(0); // Pajak per item
            $table->decimal('subtotal', 15, 2);               // (unit_price * qty) - discount

            $table->timestamps();

            $table->index('product_id');
            $table->index('sale_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

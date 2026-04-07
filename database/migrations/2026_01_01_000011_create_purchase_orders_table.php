<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique();        // PO-20260408-00001

            $table->foreignId('supplier_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->foreignId('user_id')                       // Siapa yang buat PO
                  ->constrained()
                  ->restrictOnDelete();

            $table->enum('status', [
                'draft',        // Masih diedit
                'ordered',      // Sudah dikirim ke supplier
                'partial',      // Sebagian diterima
                'received',     // Semua diterima
                'cancelled',    // Dibatalkan
            ])->default('draft');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->date('order_date');
            $table->date('expected_date')->nullable();         // Perkiraan barang datang
            $table->timestamp('received_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('status');
            $table->index('order_date');
            $table->index('user_id');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->constrained()
                  ->restrictOnDelete();

            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('buy_price', 15, 2);               // Harga beli per unit
            $table->decimal('subtotal', 15, 2);                 // buy_price * qty_ordered
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};

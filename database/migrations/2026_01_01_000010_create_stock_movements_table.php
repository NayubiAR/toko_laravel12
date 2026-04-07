<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('type', [
                'in',           // Barang masuk (PO, retur customer)
                'out',          // Barang keluar (penjualan)
                'adjustment',   // Koreksi manual (opname)
                'transfer',     // Pindah gudang (future)
                'return',       // Retur ke supplier
                'damaged',      // Barang rusak/expired
            ]);

            $table->integer('quantity');                       // Positif = masuk, negatif = keluar
            $table->integer('stock_before');                   // Stok sebelum movement
            $table->integer('stock_after');                    // Stok setelah movement

            // Polymorphic reference - bisa link ke sale, PO, atau adjustment manual
            $table->string('reference_type', 50)->nullable();  // App\Models\Sale, PurchaseOrder, dll
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->decimal('cost_per_unit', 15, 2)->nullable(); // HPP per unit saat itu
            $table->text('notes')->nullable();

            $table->foreignId('user_id')
                  ->constrained()
                  ->restrictOnDelete();

            $table->timestamps();

            $table->index('product_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['reference_type', 'reference_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

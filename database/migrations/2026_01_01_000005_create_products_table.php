<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();            // AUTO: PRD-CAT-00001
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Relasi
            $table->foreignId('category_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Harga - gunakan decimal(15,2) untuk rupiah
            $table->decimal('buy_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->decimal('wholesale_price', 15, 2)->nullable(); // Harga grosir

            // Stok
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);        // Alert threshold
            $table->string('unit', 20)->default('pcs');      // pcs, kg, ltr, box, dus

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_taxable')->default(true);    // Kena PPN atau tidak

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index('category_id');
            $table->index('supplier_id');
            $table->index('is_active');
            $table->index('stock');                          // Untuk low stock alert
            $table->index(['is_active', 'stock']);           // Composite untuk dashboard
            $table->fullText('name');                        // Full-text search POS
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

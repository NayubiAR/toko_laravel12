<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();  // INV-20260408-00001

            // Relasi
            $table->foreignId('user_id')                      // Kasir
                  ->constrained()
                  ->restrictOnDelete();
            $table->foreignId('customer_id')                  // Member (opsional)
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Nominal
            $table->decimal('subtotal', 15, 2);               // Sebelum diskon & pajak
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(11);   // PPN 11%
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);            // Final
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);

            // Pembayaran
            $table->enum('payment_method', [
                'cash', 'qris', 'bank_transfer', 'debit_card', 'credit_card', 'split'
            ]);
            $table->enum('payment_status', [
                'paid', 'pending', 'partial', 'failed', 'refunded'
            ])->default('pending');

            // Midtrans / Payment Gateway
            $table->string('payment_gateway_id')->nullable(); // Transaction ID dari Midtrans
            $table->string('payment_gateway_url')->nullable();

            // Poin loyalty
            $table->integer('points_earned')->default(0);
            $table->integer('points_used')->default(0);
            $table->decimal('points_discount', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk reporting
            $table->index('payment_status');
            $table->index('payment_method');
            $table->index('created_at');
            $table->index(['created_at', 'payment_status']); // Laporan harian
            $table->index('user_id');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

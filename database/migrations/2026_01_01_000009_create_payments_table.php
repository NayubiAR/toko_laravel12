<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('method', [
                'cash', 'qris', 'bank_transfer', 'debit_card', 'credit_card'
            ]);
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable();   // No. referensi dari bank/QRIS
            $table->enum('status', [
                'success', 'pending', 'failed', 'refunded'
            ])->default('pending');

            // Detail bank (untuk transfer)
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();

            // Bukti bayar
            $table->string('proof_image')->nullable();        // Upload bukti transfer

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')                  // Siapa yang verifikasi
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('status');
            $table->index('method');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

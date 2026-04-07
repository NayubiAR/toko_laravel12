<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['income', 'expense']);

            $table->enum('category', [
                // Income
                'sale',                  // Pendapatan penjualan
                'other_income',          // Pendapatan lain

                // Expense
                'purchase',              // Pembelian barang
                'operational',           // Biaya operasional (listrik, sewa, dll)
                'salary',               // Gaji karyawan
                'tax',                   // Pembayaran pajak
                'other_expense',         // Pengeluaran lain
            ]);

            $table->decimal('amount', 15, 2);
            $table->text('description');

            // Polymorphic reference ke transaksi sumber
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('user_id')                      // Siapa yang input
                  ->constrained()
                  ->restrictOnDelete();

            $table->date('date');                              // Tanggal transaksi keuangan
            $table->timestamps();

            $table->index('type');
            $table->index('category');
            $table->index('date');
            $table->index(['type', 'date']);                   // Laporan periodik
            $table->index(['reference_type', 'reference_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};

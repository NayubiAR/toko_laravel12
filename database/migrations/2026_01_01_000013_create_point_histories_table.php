<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('sale_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->enum('type', [
                'earned',       // Dapat poin dari belanja
                'redeemed',     // Tukar poin jadi diskon
                'adjusted',     // Koreksi manual admin
                'expired',      // Poin kadaluarsa
                'bonus',        // Bonus event/promo
            ]);

            $table->integer('points');                         // Positif = tambah, negatif = kurang
            $table->integer('balance_before');
            $table->integer('balance_after');

            $table->text('notes')->nullable();
            $table->date('expires_at')->nullable();            // Tanggal kedaluwarsa poin

            $table->timestamps();

            $table->index('customer_id');
            $table->index('sale_id');
            $table->index('type');
            $table->index('created_at');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};

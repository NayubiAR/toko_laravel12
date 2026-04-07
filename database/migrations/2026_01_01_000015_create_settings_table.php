<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Settings Table
 *
 * Menyimpan konfigurasi dinamis aplikasi:
 * - Nama toko, alamat, logo
 * - Rate PPN (bisa berubah)
 * - Konfigurasi poin loyalty (1 poin = berapa rupiah)
 * - Prefix invoice, PO number
 * - Pengaturan struk (footer, header)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);                       // general, tax, loyalty, receipt, payment
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');     // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

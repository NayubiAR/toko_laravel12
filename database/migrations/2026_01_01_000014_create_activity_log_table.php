<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spatie Activity Log Migration
 *
 * Mencatat setiap perubahan data secara otomatis:
 * - Siapa (causer) yang mengubah
 * - Apa (subject) yang diubah
 * - Kapan diubah
 * - Nilai sebelum & sesudah (di kolom properties JSON)
 *
 * Contoh log: "Admin mengubah sell_price Product #42 dari 50000 ke 55000"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();           // Grup log: product, sale, user, dll
            $table->text('description');                        // created, updated, deleted, dll
            $table->nullableMorphs('subject', 'subject');      // Model yang diubah
            $table->string('event')->nullable();               // Event type
            $table->nullableMorphs('causer', 'causer');        // User yang melakukan
            $table->json('properties')->nullable();            // { old: {...}, attributes: {...} }
            $table->json('batch_uuid')->nullable();            // Untuk grup perubahan
            $table->timestamps();

            $table->index('log_name');
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};

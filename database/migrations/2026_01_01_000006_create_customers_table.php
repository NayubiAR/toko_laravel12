<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();            // MBR-00001
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();

            // Loyalty
            $table->integer('points')->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])
                  ->default('bronze');
            $table->decimal('total_spent', 15, 2)->default(0); // Akumulasi belanja

            $table->date('member_since');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tier');
            $table->index('is_active');
            $table->index('points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

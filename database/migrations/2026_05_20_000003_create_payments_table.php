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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan_slug', 64);
            $table->string('transaction_id')->unique();
            $table->string('external_reference')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 8)->default('ngn');
            $table->string('status', 32)->default('pending');
            $table->json('checkout_payload')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

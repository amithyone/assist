<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('voucher_code', 64)->nullable()->after('currency');
            $table->decimal('original_amount', 10, 2)->nullable()->after('amount');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('original_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'original_amount', 'discount_amount']);
        });
    }
};

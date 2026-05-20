<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('price_ngn')->nullable()->after('limits');
            $table->decimal('price_usd', 8, 2)->nullable()->after('price_ngn');
            $table->string('usage_period', 16)->default('monthly')->after('price_usd');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('usage_period');
            $table->text('description')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['price_ngn', 'price_usd', 'usage_period', 'sort_order', 'description']);
        });
    }
};

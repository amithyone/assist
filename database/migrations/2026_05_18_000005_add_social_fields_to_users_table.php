<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('youtube')->nullable()->after('email');
            $table->string('instagram')->nullable()->after('youtube');
            $table->boolean('marketing_opt_in')->default(false)->after('instagram');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['youtube', 'instagram', 'marketing_opt_in']);
        });
    }
};

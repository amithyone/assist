<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usage_counters', function (Blueprint $table) {
            $table->unsignedInteger('music_video_cuts')->default(0)->after('beat_edits');
            $table->unsignedInteger('ai_edits')->default(0)->after('music_video_cuts');
            $table->unsignedInteger('preproduction')->default(0)->after('ai_edits');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE usage_counters MODIFY period VARCHAR(10) NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('usage_counters', function (Blueprint $table) {
            $table->dropColumn(['music_video_cuts', 'ai_edits', 'preproduction']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE usage_counters MODIFY period VARCHAR(7) NOT NULL');
        }
    }
};

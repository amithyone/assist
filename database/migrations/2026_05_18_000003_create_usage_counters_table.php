<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period', 7);
            $table->unsignedInteger('timelines')->default(0);
            $table->unsignedInteger('transcribe_clips')->default(0);
            $table->unsignedInteger('reel_clones')->default(0);
            $table->unsignedInteger('beat_edits')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_counters');
    }
};

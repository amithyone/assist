<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('client_event_id')->nullable()->unique();
            $table->string('feature');
            $table->string('event');
            $table->string('status')->default('success');
            $table->unsignedInteger('units')->default(1);
            $table->string('project_type')->nullable();
            $table->string('app_version')->nullable();
            $table->string('resolve_project_name')->nullable();
            $table->json('metrics')->nullable();
            $table->json('content_summary')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['feature', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_events');
    }
};

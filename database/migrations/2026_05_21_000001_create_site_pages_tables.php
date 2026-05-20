<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->boolean('is_published')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->json('schema_json')->nullable();
            $table->json('intro')->nullable();
            $table->timestamps();
        });

        Schema::create('site_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_page_id')->constrained()->cascadeOnDelete();
            $table->string('section_key');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('content')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->timestamps();

            $table->unique(['site_page_id', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_page_sections');
        Schema::dropIfExists('site_pages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id('blog_id');
            $table->string('blog_title')->unique();
            $table->string('blog_slug')->unique();
            $table->unsignedBigInteger('blog_category_id')->nullable();
            $table->string('blog_img_url')->nullable();
            $table->string('blog_img_alt')->nullable();
            $table->text('blog_content')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->boolean('blog_status')->default(true);
            $table->longText('seo_other_details')->nullable();
            $table->json('blog_tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};

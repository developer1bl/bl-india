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
        Schema::create('notices', function (Blueprint $table) {
            $table->id('notice_id');
            $table->string('notice_title');
            $table->string('notice_slug')->unique();
            $table->unsignedBigInteger('notice_image_id')->nullable();
            $table->string('notice_img_alt')->nullable();
            $table->text('notice_content')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->unsignedBigInteger('notice_document_id')->nullable();
            $table->boolean('notice_status')->default(true);
            $table->json('products_tag')->nullable();
            $table->longText('seo_other_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};

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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('product_slug')->unique();
            $table->unsignedBigInteger('product_image_id')->nullable();
            $table->string('product_img_alt')->nullable();
            $table->text('product_compliance')->nullable();
            $table->longText('product_content')->nullable();
            $table->unsignedBigInteger('product_service_id')->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->unsignedBigInteger('information')->nullable();
            $table->unsignedBigInteger('guidelines')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->boolean('product_status')->default(true);
            $table->integer('product_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

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
        Schema::create('services', function (Blueprint $table) {
            $table->id('service_id');
            $table->string('service_name', 150);
            $table->string('service_slug')->unique();
            $table->unsignedBigInteger('service_image_id')->nullable();
            $table->string('service_img_alt')->nullable();
            $table->longText('service_description')->nullable();
            $table->text('service_compliance')->nullable();
            $table->json('faqs')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->boolean('service_featured')->default(false);
            $table->boolean('service_product_show')->default(true);
            $table->integer('service_order')->default(0);
            $table->boolean('service_status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

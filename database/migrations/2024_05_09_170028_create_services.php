<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceCategory;

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
            $table->string('service_img_url')->nullable();
            $table->string('service_img_alt')->nullable();
            $table->json('service_description')->nullable();
            $table->json('service_compliance')->nullable();
            $table->json('faqs')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->boolean('service_featured')->default(false);
            $table->integer('service_order')->default(0);
            $table->boolean('service_status')->default(true);
            $table->foreignIdFor(ServiceCategory::class)->nullable();
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

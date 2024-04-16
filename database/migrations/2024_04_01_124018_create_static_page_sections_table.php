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
        Schema::create('static_page_sections', function (Blueprint $table) {
            $table->id('static_page_section_id');
            $table->unsignedBigInteger('static_page_id');
            $table->string('section_img_url')->nullable();
            $table->string('section_img_alt')->nullable();
            $table->string('section_name');
            $table->string('section_slug')->unique();
            $table->string('section_tagline')->nullable();
            $table->text('section_description')->nullable();
            $table->text('section_content')->nullable();
            $table->boolean('section_status')->default(true)->comment('default status is true');
            $table->integer('section_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_page_sections');
    }
};

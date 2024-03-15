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
        Schema::create('service_sections', function (Blueprint $table) {
            $table->id('service_section_id');
            $table->string('service_section_name');
            $table->string('service_section_slug')->unique();
            $table->longText('service_section_content')->nullable();
            $table->boolean('service_section_status')->default(true);
            $table->integer('service_section_order')->default(0);
            $table->unsignedBigInteger('service_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_sections');
    }
};

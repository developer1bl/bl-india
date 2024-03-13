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
        Schema::create('product_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreignId('service_id')->references('service_id')->on('services')->onDelete('cascade');
            $table->enum('service_type', [1,2])->default(1)->comment('1 for mandatory service, 2 for voluntary service');
            $table->json('service_compliance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_services');
    }
};
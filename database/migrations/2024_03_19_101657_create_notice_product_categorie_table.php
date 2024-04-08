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
        Schema::create('notice_product_categorie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->references('notice_id')->on('notices')->onDelete('cascade');
            $table->foreignId('product_category_id')->references('product_category_id')->on('product_categories')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_product_categorie');
    }
};

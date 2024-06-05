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
        Schema::create('knowledgebase_categories', function (Blueprint $table) {
            $table->id('knowledgebase_category_id');
            $table->string('knowledgebase_category_name');
            $table->string('knowledgebase_category_slug');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledgebase_categories');
    }
};

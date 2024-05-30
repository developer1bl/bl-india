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
        Schema::create('notification_categories', function (Blueprint $table) {
            $table->id('notification_category_id');
            $table->string('notification_category_name');
            $table->string('notification_category_slug');
            $table->boolean('notification_category_type')->default(false);
            $table->boolean('notification_category_status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_categories');
    }
};

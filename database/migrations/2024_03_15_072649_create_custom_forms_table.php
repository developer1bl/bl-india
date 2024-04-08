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
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->id('custom_form_id');
            $table->string('form_name');
            $table->string('form_slug')->unique();
            $table->string('form_email');
            $table->longText('form_components');
            $table->boolean('form_status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_forms');
    }
};

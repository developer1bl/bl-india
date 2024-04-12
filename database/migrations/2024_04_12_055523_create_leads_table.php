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
        Schema::create('leads', function (Blueprint $table) {
            $table->id('lead_id');
            $table->string('name');
            $table->string('email');
            $table->string('country');
            $table->string('phone');
            $table->string('service');
            $table->string('message')->nullable();
            $table->string('status')->default('open');
            $table->string('source')->default('website');
            $table->string('ip_address')->nullable();
            $table->text('pdf_path')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('organization')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

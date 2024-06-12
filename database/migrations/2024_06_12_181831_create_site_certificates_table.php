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
        Schema::create('site_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('site_certificate_name');
            $table->string('site_certificate_slug');
            $table->string('site_certificate_url');
            $table->boolean('site_certificate_status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_certificates');
    }
};

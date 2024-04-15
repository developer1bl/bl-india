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
        Schema::create('partner_forms', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('org_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->text('user_message')->nullable();
            $table->enum('status',[0,1,2])->default(0)->comment('0 status for pending, 1 status for accepted, 2 status for rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_forms');
    }
};

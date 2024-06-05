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
            $table->id('partner_form_id');
            $table->string('contact_person_name');
            $table->string('designation_name');
            $table->string('organization_name')->nullable();
            $table->string('industry_name')->nullable();
            $table->text('address_street');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('country');
            $table->string('country_code');
            $table->string('phone_number');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('experience')->nullable();
            $table->text('partner_details')->nullable();
            $table->enum('partner_type',[1,2])->nullable()->comment('1 for Business Associate, 2 for Resident Executive');
            $table->timestamps();
            $table->softDeletes();
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

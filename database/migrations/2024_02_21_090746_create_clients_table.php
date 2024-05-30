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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->boolean('is_email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone');
            $table->string('country');
            $table->string('password')->nullable();
            $table->dateTime('login_at')->nullable()->comment('this field will be update everytime when the user is logged');
            $table->string('otp')->nullable()->comment('this field store the OTP at the time of login/register');
            $table->dateTime('otp_generated_at')->nullable()->comment('we are storing date and time information of the otp generated');
            $table->macAddress('otp_generated_address')->nullable()->comment("here we store the address of request otp user's device");
            $table->boolean('is_online')->default(false)->comment('this column help to check is user online or offline');
            $table->boolean('is_active')->default(true)->comment('this column help to check is user active or not');
            $table->string('email_verification_token')->nullable()->comment('this column help to');
            $table->dateTime('email_verify_till_valid')->nullable()->comment('in this column we are store till when the email verfication token is valid');
            $table->dateTime('otp_verify_till_valid')->nullable()->comment('in this column we are store till when the otp verfication token is valid');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email')->unique();
            $table->string('phone',100);
            $table->boolean('is_email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_approved')->default(false);
            $table->foreignIdFor(Role::class)->default(3);
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('is_online')->default(false)->comment('this column help to check is user online or offline');
            $table->boolean('is_active')->default(true)->comment('this column help to check is user active or not');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

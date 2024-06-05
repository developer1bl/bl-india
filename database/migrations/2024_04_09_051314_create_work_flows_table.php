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
        Schema::create('work_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('step_img_url')->nullable();
            $table->string('step_img_alt')->nullable();
            $table->integer('flow_order')->default(0);
            $table->boolean('flow_status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_flows');
    }
};

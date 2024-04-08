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
        Schema::create('document_download', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('download_id');
            $table->string('download_type')->nullable()->comment('either INFOMATION or GUIDELINES');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_download');
    }
};

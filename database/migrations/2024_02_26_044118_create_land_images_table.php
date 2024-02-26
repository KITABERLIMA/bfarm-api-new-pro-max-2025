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
        Schema::create('land_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('land_id');
            $table->string('image_url');
            $table->text('description')->nullable();
            $table->timestamps();

            // $table->foreign('land_id')->references('id')->on('lands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_images');
    }
};

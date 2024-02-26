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
        Schema::create('mapped_lands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('land_id');
            $table->unsignedBigInteger('land_content_id');
            $table->unsignedBigInteger('mapping_type_id');
            $table->text('mapping_details');
            $table->timestamp('map_date')->nullable();
            $table->timestamps();

            $table->foreign('land_id')->references('id')->on('land');
            $table->foreign('mapping_type_id')->references('id')->on('mapping_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapped_lands');
    }
};

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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('village_id', 255);
            $table->unsignedBigInteger('sub_district_id');
            $table->unsignedBigInteger('city_district_id');
            $table->unsignedBigInteger('province_id');
            $table->string('postal_code', 255);
            $table->timestamps();

            // $table->foreign('village_id')->references('id')->on('village');
            // $table->foreign('sub_district_id')->references('id')->on('sub_district');
            // $table->foreign('city_district_id')->references('id')->on('city_district');
            // $table->foreign('province_id')->references('id')->on('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
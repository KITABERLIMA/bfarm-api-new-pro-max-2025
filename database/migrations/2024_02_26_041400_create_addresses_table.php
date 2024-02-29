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
            $table->string('village_id', 255)->required();
            $table->string('sub_district_id', 255)->required();
            $table->string('city_district_id', 255)->required();
            $table->string('province_id', 255)->required();
            $table->string('postal_code', 255, 255)->required();
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
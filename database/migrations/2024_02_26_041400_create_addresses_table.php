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
            $table->string('village', 255)->required();
            $table->string('sub_district', 255)->required();
            $table->string('city_district', 255)->required();
            $table->string('province', 255)->required();
            $table->string('postal_code', 255, 255)->required();
            $table->timestamps();

            // $table->foreign('village')->references('id')->on('village');
            // $table->foreign('sub_district')->references('id')->on('sub_district');
            // $table->foreign('city_district')->references('id')->on('city_district');
            // $table->foreign('province')->references('id')->on('province');
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

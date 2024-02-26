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
        Schema::create('land_content_histories', function (Blueprint $table) {
            $table->id();
            $table->decimal('air_temperature', 8, 2);
            $table->decimal('air_humidity', 8, 2);
            $table->decimal('air_pressure', 8, 2);
            $table->decimal('nitrogen', 8, 2);
            $table->decimal('phosphorus', 8, 2);
            $table->decimal('potassium', 8, 2);
            $table->decimal('pH', 8, 2);
            $table->decimal('soil_moisture', 8, 2);
            $table->decimal('soil_temperature', 8, 2);
            $table->decimal('electrical_conductivity', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_content_histories');
    }
};

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
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->required();
            $table->unsignedBigInteger('address_id')->required();
            $table->string('land_status', 255)->required();
            $table->text('land_description')->nullable();
            $table->enum('ownership_status', ['owned', 'rented']);
            $table->string('location', 255)->required();
            $table->float('land_area')->required();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('user');
            // $table->foreign('address_id')->references('id')->on('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};

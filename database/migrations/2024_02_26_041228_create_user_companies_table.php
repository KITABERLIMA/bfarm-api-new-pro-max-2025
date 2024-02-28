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
        Schema::create('user_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('address_id')->required();
            $table->unsignedBigInteger('user_id')->required();
            $table->string('first_name', 255)->required();
            $table->string('last_name', 255)->required();
            $table->string('position', 255)->required();
            $table->string('company_name', 255)->required();
            $table->string('company_email', 255)->unique()->required();
            $table->string('company_password', 255)->required();
            $table->string('company_phone', 20)->required();
            $table->timestamps();

            // $table->foreign('address_id')->references('id')->on('address');
            // $table->foreign('user_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_companies');
    }
};

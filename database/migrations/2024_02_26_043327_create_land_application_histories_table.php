<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('land_application_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->required();
            $table->unsignedBigInteger('land_id')->required();
            $table->timestamp('application_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('application_status', ['processed', 'approved', 'rejected'])->default('processed');
            $table->unsignedBigInteger('mapping_type_id')->required();
            $table->text('description')->nullable();
            $table->timestamp('decision_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('land_id')->references('id')->on('lands');
            // $table->foreign('mapping_type_id')->references('id')->on('mapping_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_application_histories');
    }
};

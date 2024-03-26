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
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->required();
            $table->unsignedBigInteger('user_id')->required();
            $table->integer('quantity')->required();
            $table->decimal('unit_price', 8, 2)->required();
            $table->decimal('total_price', 8, 2)->required();
            $table->timestamp('transaction_date')->required();
            $table->enum('transaction_status', ['in_process', 'completed', 'cancelled'])->default('in_process');
            $table->timestamps();

            // $table->foreign('product_id')->references('id')->on('products');
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_transactions');
    }
};

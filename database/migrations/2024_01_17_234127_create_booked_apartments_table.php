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
        Schema::create('booked_apartments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('app_users')->onDelete('cascade');
            $table->unsignedBigInteger('apartment_id');
            $table->foreign('apartment_id')->references('id')->on('apartments')->onDelete('cascade');
            $table->date('date_from');
            $table->date('date_to');
            $table->integer('total_price');
            $table->enum('status', ['recent','pending', 'past', 'canceled'])->default('canceled');
            $table->integer('paid')->default(0);
            $table->integer('coupon_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_apartments');
    }
};

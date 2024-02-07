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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('app_users')->onDelete('cascade');
            $table->string('billing_country')->nullable();
            $table->string('billing_fname')->nullable();
            $table->string('billing_lname')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_number')->nullable();
            $table->string('shpping_country')->nullable();
            $table->string('shpping_fname')->nullable();
            $table->string('shpping_lname')->nullable();
            $table->string('shpping_address')->nullable();
            $table->string('shpping_city')->nullable();
            $table->string('shpping_email')->nullable();
            $table->string('shpping_number')->nullable();
            $table->decimal('booked_total')->default(0.00);
            $table->decimal('discount')->default(0.00);
            $table->decimal('total')->default(0.00);
            $table->string('method')->nullable();
            $table->string('gateway_type')->nullable();
            $table->string('order_number')->nullable();
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_charge')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('order_status')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('receipt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

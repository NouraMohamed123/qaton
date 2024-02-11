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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('invoice_url')->nullable();
            $table->unsignedBigInteger('booked_id');
            $table->foreign('booked_id')->references('id')->on('booked_apartments')->onDelete('cascade');
            $table->decimal('price')->default(0.00);
            $table->string('invoice_status')->nullable();
            $table->boolean('is_success')->nullable();
            $table->date('Transaction_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};

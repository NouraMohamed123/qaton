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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit_space');
            $table->string('price');
            $table->integer('bathrooms');
            $table->integer('lounges');
            $table->boolean('dining_session');
            $table->boolean('balcony');
            $table->boolean('yard');
            $table->boolean('terrace');
            $table->enum('view',['street','passage','park','promenade'])->default('street');
            $table->json('additional_features');
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->text('images')->nullable();
            $table->string('video')->nullable();
            $table->enum('parking',['external','basement'])->default('external');
            $table->integer('max_guests')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};

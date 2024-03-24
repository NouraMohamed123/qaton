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
            $table->string('code')->nullable();
            $table->string('unit_space')->nullable();
            $table->string('price');
            $table->integer('bathrooms');
            $table->integer('lounges');
            $table->boolean('dining_session');
            $table->json('features')->nullable();
            $table->enum('view',['street','passage','park','promenade'])->default('street');
            $table->json('additional_features')->nullable();
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->string('video')->nullable();
            $table->enum('parking',['external','basement'])->default('external');
            $table->integer('max_guests')->nullable();
            $table->integer('max_rooms')->nullable();
            // $table->foreign('owner_id')->references('id')->on('app_users')->nullable()->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->integer('available')->default(0);
            $table->tinyInteger('beds_childs')->default(0);
            ////////////////////////////////////access data
            $table->string('website_link')->nullable();
            $table->string('login_instructions')->nullable();
            $table->string('internet_name')->nullable();
            $table->string('internet_password')->nullable();
            $table->string('instructions_prohibitions')->nullable();
            $table->string('apartment_features')->nullable();
            $table->json('contact_numbers')->nullable();
            $table->string('access_video')->nullable();
            $table->string('secret_door')->nullable();
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

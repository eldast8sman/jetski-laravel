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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('description');
            $table->double('amount');
            $table->boolean('available')->default(true);
            $table->string('category')->default('Main Course');
            $table->integer('g5_id')->nullable();
            $table->integer('screen_id')->nullable()->unique();
            $table->integer('parent_id')->nullable();
            $table->integer('modifier_id')->nullable();
            $table->string('photo')->default("https://thumbs.dreamstime.com/b/no-thumbnail-image-placeholder-forums-blogs-websites-148010362.jpg");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

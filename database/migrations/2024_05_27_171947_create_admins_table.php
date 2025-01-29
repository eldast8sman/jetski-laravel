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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->unique();
            $table->integer('photo')->nullable();
            $table->string('role')->default('super');
            $table->string('verification_token')->nullable();
            $table->dateTime('verification_token_expiry')->nullable();
            $table->string('token')->nullable();
            $table->dateTime('token_expiry')->nullable();
            $table->boolean('activated')->default(0);
            $table->integer('status')->default(1);
            $table->dateTime('last_login')->nullable();
            $table->dateTime('prev_login')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};

<?php

use App\Models\User;
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
        Schema::create('membership_information', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignIdFor(User::class, 'user_id')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('hin_number')->unique()->nullable();
            $table->string('year')->nullable();
            $table->string('loa')->nullable();
            $table->string('beam')->nullable();
            $table->string('draft')->nullable();
            $table->string('nwa')->unique()->nullable();
            $table->string('nwa_expiry')->nullable();
            $table->string('mmsi')->unique()->nullable();
            $table->string('call_sign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_information');
    }
};

<?php

use App\Models\Booking;
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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->foreignIdFor(Booking::class, 'booking_id')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['booking_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};

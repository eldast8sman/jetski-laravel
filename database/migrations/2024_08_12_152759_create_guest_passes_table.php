<?php

use App\Models\Event;
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
        Schema::create('guest_passes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignIdFor(Event::class)->onDelete('cascade');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->date('date');
            $table->integer('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_passes');
    }
};

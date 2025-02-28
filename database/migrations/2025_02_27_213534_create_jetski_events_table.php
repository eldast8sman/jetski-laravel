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
        Schema::create('jetski_events', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('event_title');
            $table->text('description')->nullable();
            $table->text('details')->nullable();
            $table->text('audience');
            $table->text('date_time');
            $table->date('date_from');
            $table->date('date_to');
            $table->text('location')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('photo_id')->nullable();
            $table->text('tickets_pricing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jetski_events');
    }
};
 
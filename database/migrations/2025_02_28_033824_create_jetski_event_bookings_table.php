<?php

use App\Models\EventTicketPricing;
use App\Models\JetskiEvent;
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
        Schema::create('jetski_event_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(JetskiEvent::class, 'jetski_event_id');
            $table->string('booking_reference');
            $table->string('g5_order_number');
            $table->string('g5_id');
            $table->text('tickets');
            $table->integer('total_quantity');
            $table->double('total_amount');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jetski_event_bookings');
    }
};

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
        Schema::create('jetski_event_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignIdFor(EventTicketPricing::class, 'event_ticket_pricing_id');
            $table->foreignIdFor(JetskiEvent::class, 'jetski_event_id');
            $table->foreignIdFor(User::class, 'user_id');
            $table->double('unit_price');
            $table->integer('quantity');
            $table->double('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jetski_event_tickets');
    }
};

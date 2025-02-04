<?php

use App\Models\OrderCart;
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
        Schema::create('order_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderCart::class, 'order_cart_id');
            $table->string('status')->nullable();
            $table->string('agent_type')->default('user');
            $table->integer('agent_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_trackers');
    }
};

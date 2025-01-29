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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignIdFor(User::class, 'user_id')->onDelete('cascade');
            $table->double('amount')->nullable();
            $table->text('description');
            $table->string('address')->nullable();
            $table->string('type')->default('Delivery');
            $table->string('category')->default('Meal');
            $table->string('paid_from')->default('Wallet');
            $table->string('delivery_status')->default('Opened');
            $table->string('payment_status')->default('Opened');
            $table->date('date_ordered');
            $table->integer('g5_id')->nullable();
            $table->string('g5_order_number')->nullable();
            $table->string('served_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

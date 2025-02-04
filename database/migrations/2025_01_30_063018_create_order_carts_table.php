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
        Schema::create('order_carts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('order_no')->nullable();
            $table->foreignIdFor(User::class, 'user_id');
            $table->string('g5_id')->nullable();
            $table->string('g5_order_number')->nullable();
            $table->string('user_name');
            $table->string('order_type')->default('Delivery');
            $table->text('delivery_address')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->string('delivery_email')->nullable();
            $table->string('status')->default('Checkout');
            $table->double('delivery_amount')->default(0);
            $table->boolean('service_charge')->default(false);
            $table->double('service_charge_amount')->default(0);
            $table->double('tip_amount')->default(0);
            $table->double('item_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->boolean('open')->default(true);
            $table->string('order_by_type')->default('user');
            $table->integer('order_by_id')->nullable();
            $table->dateTime('time_ordered')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_carts');
    }
};

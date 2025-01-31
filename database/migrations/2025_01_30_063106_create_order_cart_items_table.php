<?php

use App\Models\FoodMenu;
use App\Models\OrderCart;
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
        Schema::create('order_cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(OrderCart::class, 'order_cart_id');
            $table->foreignIdFor(FoodMenu::class, 'food_menu_id');
            $table->text('add_ons')->nullable();
            $table->double('add_on_price')->default(0);
            $table->double('unit_price')->default(0);
            $table->double('total_unit_price')->default(0);
            $table->integer('quantity')->default(1);
            $table->double('total_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_items');
    }
};

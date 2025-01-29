<?php

use App\Models\MenuCategory;
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
        Schema::create('food_menus', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('uuid');
            $table->foreignIdFor(MenuCategory::class, 'menu_category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->double('amount')->nullable();
            $table->boolean('availability')->default(false);
            $table->string('g5_id');
            $table->date('shelf_life_from')->nullable();
            $table->date('shelf_life_to')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('details')->nullable();
            $table->integer('total_orders')->default(0);
            $table->integer('parent_id')->nullable();
            $table->integer('modifier_id')->nullable();
            $table->boolean('is_stand_alone')->default(true);
            $table->boolean('is_add_on')->default(false);
            $table->text('add_ons')->nullable();
            $table->boolean('is_new')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_menus');
    }
};

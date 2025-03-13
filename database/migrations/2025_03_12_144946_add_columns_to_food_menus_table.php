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
        Schema::table('food_menus', function (Blueprint $table) {
            $table->string('type')->default('screen')->after('menu_category_id');
            $table->boolean('is_modifier')->default(false)->after('type');
            $table->integer('group_id')->nullable()->after('is_mofifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_menus', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('is_modifier');
            $table->dropColumn('group_id');
        });
    }
};

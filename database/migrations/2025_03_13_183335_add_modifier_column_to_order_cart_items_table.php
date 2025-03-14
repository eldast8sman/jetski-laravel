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
        Schema::table('order_cart_items', function (Blueprint $table) {
            $table->integer('modifier_id')->nullable()->after('add_ons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_items', function (Blueprint $table) {
            $table->dropColumn('modifier_id');
        });
    }
};

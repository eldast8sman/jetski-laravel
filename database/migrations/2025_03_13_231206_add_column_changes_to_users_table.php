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
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_id')->nullable()->change();
        });

        Schema::table('user_memberships', function (Blueprint $table) {
            $table->string('membership_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('membership_id')->nullable()->change();
        });
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->integer('membership_id')->nullable()->change();
        });
    }
};

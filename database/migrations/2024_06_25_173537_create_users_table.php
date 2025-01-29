<?php

use App\Models\Product;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->default('Single');
            $table->text('address')->nullable();
            $table->foreignIdFor(Product::class, 'membership_id')->nullable()->onDelete('cascade');
            $table->date('exp_date')->nullable();
            $table->integer('email_verified')->default(0);
            $table->string('verification_token')->nullable();
            $table->dateTime('verification_token_expiry')->nullable();
            $table->string('token')->nullable();
            $table->dateTime('token_expiry')->nullable();
            $table->string('photo');
            $table->string('g5_id')->nullable()->unique();
            $table->string('sparkle_id')->nullable();
            $table->string('external_sparkle_reference')->unique()->nullable();
            $table->string('account_number')->unique()->nullable();
            $table->string('last_synced')->nullable();
            $table->string('notification_token')->nullable();
            $table->string('relationship')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('notifications')->default(true);
            $table->boolean('can_use')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

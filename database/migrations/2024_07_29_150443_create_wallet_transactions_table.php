<?php

use App\Models\Wallet;
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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignIdFor(Wallet::class, 'wallet_id')->onDelete('cascade');
            $table->double('amount');
            $table->string('type')->default('Debit');
            $table->boolean('is_user_credited');
            $table->string('external_reference');
            $table->string('payment_processor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

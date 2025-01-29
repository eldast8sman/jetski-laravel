<?php

use App\Models\Admin;
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
        Schema::create('admin_bank_account_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Admin::class, 'admin_id');
            $table->string('bank_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('nip_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_bank_account_details');
    }
};

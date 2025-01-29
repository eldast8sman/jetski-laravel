<?php

use App\Models\FileManager;
use App\Models\NotificationImage;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignIdFor(User::class, 'user_id')->nullable()->onDelete('cascade');
            $table->string('title');
            $table->string('description');
            $table->string('spnsored_by')->nullable();
            $table->timestamp('date');
            $table->string('notification_type')->default('both');
            $table->foreignIdFor(NotificationImage::class, 'notification_image_id')->onDelete('cascade');
            $table->integer('guest_amount')->nullable();
            $table->integer('created_guests')->nullable();
            $table->foreignIdFor(FileManager::class, 'photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

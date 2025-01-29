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
        Schema::table('adverts', function (Blueprint $table) {
            $table->string('campaign_name')->nullable()->after('uuid');
            $table->date('campaign_start')->nullable()->after('image_banner');
            $table->date('campaign_end')->nullable()->after('campaign_start');
            $table->integer('clicks')->default(0)->after('status');
            $table->integer('impressions')->default(0)->after('clicks');
            $table->integer('conversions')->default(0)->after('impressions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->dropColumn('campaign_name');
            $table->dropColumn('campaign_start');
            $table->dropColumn('campaign_end');
            $table->dropColumn('clicks');
            $table->dropColumn('impressions');
            $table->dropColumn('conversions');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('series_episodes', function (Blueprint $t) {
            $t->boolean('ad_required')->default(false);
            $t->string('ad_video')->nullable();           // path/URL mp4 iklan
            $t->unsignedInteger('ad_reward_minutes')->default(60); // akses berlaku X menit
        });
    }
    public function down(): void
    {
        Schema::table('series_episodes', function (Blueprint $t) {
            $t->dropColumn(['ad_required', 'ad_video', 'ad_reward_minutes']);
        });
    }
};

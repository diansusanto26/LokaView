<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episode_ad_views', function (Blueprint $t) {
            $t->id();
            $t->foreignId('series_episode_id')->constrained('series_episodes')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamp('expires_at');   // sampai kapan akses gratis berlaku
            $t->timestamps();

            $t->unique(['series_episode_id', 'user_id']); // satu record per user-episode
            $t->index('expires_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('episode_ad_views');
    }
};

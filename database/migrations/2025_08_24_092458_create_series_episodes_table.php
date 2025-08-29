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
        Schema::create('series_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series');
            $table->integer('episode_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video');
            $table->boolean('is_locked')->default(false);
            $table->integer('unlock_cost')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_episodes');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchProgress extends Model
{
    protected $fillable = [
        'user_id',
        'series_episode_id',
        'last_watched_at',
        'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seriesEpisode()
    {
        return $this->belongsTo(SeriesEpisode::class);
    }
}

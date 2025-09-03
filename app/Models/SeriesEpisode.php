<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeriesEpisode extends Model
{
    protected $fillable = [
        'series_id',
        'episode_number',
        'title',
        'description',
        'video',
        'is_locked',
        'unlock_cost',
        // new:
        'ad_required',
        'ad_video',
        'ad_reward_minutes',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'unlock_cost' => 'integer',
        'ad_required' => 'boolean',
        'ad_reward_minutes' => 'integer',
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }
    public function unlockedEpisodes()
    {
        return $this->hasMany(UnlockedEpisode::class);
    }
    public function WatchProgress()
    {
        return $this->hasMany(WatchProgress::class);
    }

    public function adViews()
    {  // <— baru
        return $this->hasMany(EpisodeAdView::class, 'series_episode_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeAdView extends Model
{
    protected $fillable = ['series_episode_id', 'user_id', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public function episode()
    {
        return $this->belongsTo(SeriesEpisode::class, 'series_episode_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

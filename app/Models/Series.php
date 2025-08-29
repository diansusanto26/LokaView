<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $fillable = [
        'genre_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'age_rating',
        'release_year',
        'is_trending',
        'is_top_choice',
    ];

    protected $casts = [
        'is_trending' => 'boolean',
        'is_top_choice' => 'boolean',
        'release_year' => 'integer'
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function episodes()
    {
        return $this->hasMany(SeriesEpisode::class);
    }
}

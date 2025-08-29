<?php

namespace App\Repositories;

use App\Interfaces\SeriesEpisodeRepositoryInterface;
use App\Models\SeriesEpisode;

class SeriesEpisodeRepository implements SeriesEpisodeRepositoryInterface
{
    public function getById($id)
    {
        return SeriesEpisode::findOrFail($id);
    }
}

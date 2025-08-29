<?php

namespace App\Interfaces;

interface SeriesRepositoryInterface
{
    public function getAll(
        $filters = [],
        $limit = 10,
    );

    public function getBySlug($slug);
}

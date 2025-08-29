<?php

namespace App\Http\Controllers;

use App\Interfaces\SeriesEpisodeRepositoryInterface;
use App\Interfaces\SeriesRepositoryInterface;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    protected $seriesRepository;
    protected $seriesEpisodeRepository;


    public function __construct(SeriesRepositoryInterface $seriesRepository, SeriesEpisodeRepositoryInterface $seriesEpisodeRepository)
    {
        $this->seriesRepository = $seriesRepository;
        $this->seriesEpisodeRepository = $seriesEpisodeRepository;
    }


    public function show($slug)
    {
        $series = $this->seriesRepository->getBySlug($slug);
        $relatedSeries = $this->seriesRepository->getAll(['genre_id' => $series->genre_id], 4);



        return view('pages.series.show', compact('series', 'relatedSeries'));
    }

    public function play($slug, $episodeId)
    {
        $series = $this->seriesRepository->getBySlug($slug);
        $episode = $this->seriesEpisodeRepository->getById($episodeId);

        return view('pages.series.play', compact('series', 'episode'));
    }
}

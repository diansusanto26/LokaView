<?php

namespace App\Http\Controllers;

use App\Interfaces\BannerRepositoryInterface;
use App\Interfaces\SeriesRepositoryInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $bannerRepository;
    protected $seriesRepository;

    public function __construct(BannerRepositoryInterface $bannerRepository, SeriesRepositoryInterface $seriesRepository)
    {
        $this->bannerRepository = $bannerRepository;
        $this->seriesRepository = $seriesRepository;
    }

    public function index()
    {
        $banners = $this->bannerRepository->getAll();
        $trendingSeries = $this->seriesRepository->getAll(['is_trending' => true]);
        $topChoiceSeries = $this->seriesRepository->getAll(['is_top_choice' => true]);

        return view('pages.home', compact('banners', 'trendingSeries', 'topChoiceSeries'));
    }
}

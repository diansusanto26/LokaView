<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Detail series (publik)
Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Pemutar
    Route::get('/series/{slug}/play/{episodeId}', [SeriesController::class, 'play'])
        ->whereNumber('episodeId')->name('series.play');

    // Streaming
    Route::get('/series/{episode}/stream', [SeriesController::class, 'stream'])
        ->whereNumber('episode')->name('series.stream');

    // Gerbang iklan & callback selesai iklan
    Route::get('/series/{slug}/ad/{episode}', [SeriesController::class, 'ad'])
        ->whereNumber('episode')->name('series.ad');

    Route::post('/series/{episode}/ad-complete', [SeriesController::class, 'adComplete'])
        ->whereNumber('episode')->name('series.adComplete');

    // Buka kunci dengan koin (WAJIB POST)
    Route::post('/series/{episode}/unlock', [SeriesController::class, 'unlock'])
        ->whereNumber('episode')->name('series.unlock');
});

// Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'storeLogin'])->name('login.store');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');

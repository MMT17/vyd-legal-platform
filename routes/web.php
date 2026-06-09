<?php

use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PracticeAreaController;
use App\Http\Controllers\Public\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/nosotros', [PageController::class, 'about'])->name('public.about');
Route::get('/equipo', [TeamController::class, 'index'])->name('public.team');
Route::get('/areas', [PracticeAreaController::class, 'index'])->name('public.practice-areas.index');
Route::get('/areas/{slug}', [PracticeAreaController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('public.practice-areas.show');
Route::get('/contacto', [ContactController::class, 'index'])->name('public.contact');
Route::post('/contacto', [ContactController::class, 'store'])->name('public.contact.store');

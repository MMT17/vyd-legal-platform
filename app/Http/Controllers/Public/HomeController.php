<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->active()
            ->with('activeSections')
            ->where('slug', 'home')
            ->first();

        return view('public.home', [
            'page' => $page,
            'heroSection' => $page?->activeSections?->firstWhere('image_path') ?? $page?->activeSections?->first(),
        ]);
    }
}

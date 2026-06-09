<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('public.about', [
            'page' => Page::query()
                ->active()
                ->with('activeSections')
                ->where('slug', 'nosotros')
                ->first(),
        ]);
    }
}

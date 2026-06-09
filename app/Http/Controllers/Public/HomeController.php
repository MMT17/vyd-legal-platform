<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Models\Cms\PracticeArea;
use App\Models\Cms\TeamMember;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'page' => Page::query()
                ->active()
                ->with('activeSections')
                ->where('slug', 'home')
                ->first(),
            'practiceAreas' => PracticeArea::query()
                ->active()
                ->select(['id', 'title', 'slug', 'excerpt', 'icon', 'sort_order'])
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
            'teamMembers' => TeamMember::query()
                ->active()
                ->select(['id', 'name', 'position', 'photo_path', 'sort_order'])
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
        ]);
    }
}

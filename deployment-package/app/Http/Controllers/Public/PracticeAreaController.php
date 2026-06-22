<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\PracticeArea;
use Illuminate\Contracts\View\View;

class PracticeAreaController extends Controller
{
    public function index(): View
    {
        return view('public.practice-areas.index', [
            'practiceAreas' => PracticeArea::query()
                ->active()
                ->orderBy('sort_order')
                ->paginate(12),
        ]);
    }

    public function show(string $slug): View
    {
        return view('public.practice-areas.show', [
            'practiceArea' => PracticeArea::query()
                ->active()
                ->where('slug', $slug)
                ->firstOrFail(),
        ]);
    }
}

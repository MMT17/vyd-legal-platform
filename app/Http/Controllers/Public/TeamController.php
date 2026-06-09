<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\TeamMember;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('public.team', [
            'teamMembers' => TeamMember::query()
                ->active()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}

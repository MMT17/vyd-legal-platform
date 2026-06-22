<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\TeamMember;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teamMembers = TeamMember::query()
            ->active()
            ->orderByDesc('is_partner')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.team', [
            'partners' => $teamMembers->where('is_partner', true)->values(),
            'teamMembers' => $teamMembers->where('is_partner', false)->values(),
        ]);
    }
}

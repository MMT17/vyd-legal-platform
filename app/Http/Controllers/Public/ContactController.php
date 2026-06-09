<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cms\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('public.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        ContactMessage::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]));

        return back()->with('status', 'Mensaje enviado correctamente.');
    }
}

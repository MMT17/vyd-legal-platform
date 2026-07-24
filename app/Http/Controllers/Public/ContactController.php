<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReceived;
use App\Models\Cms\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('public.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $fingerprint = sha1(implode('|', [
            mb_strtolower($data['email']),
            $data['name'],
            $data['subject'] ?? '',
            $data['message'],
        ]));

        if (session('last_contact_fingerprint') === $fingerprint) {
            return back()->with('success', 'Mensaje enviado correctamente.');
        }

        $contactMessage = ContactMessage::create($data);

        session(['last_contact_fingerprint' => $fingerprint]);

        try {
            $recipients = $this->contactRecipients();

            if ($recipients !== []) {
                Mail::to($recipients)->send(new ContactMessageReceived($contactMessage));
            }
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar correo de contacto publico.', [
                'contact_message_id' => $contactMessage->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', 'Mensaje enviado correctamente.');
    }

    /**
     * @return array<int, string>
     */
    private function contactRecipients(): array
    {
        $configured = (string) config('app.contact_recipient_email', '');

        return collect(preg_split('/[,;]|\s+y\s+/iu', $configured) ?: [])
            ->map(fn (string $email): string => trim($email))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->values()
            ->all();
    }
}

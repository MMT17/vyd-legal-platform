<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\Cms\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class PublicContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_message_and_sends_email(): void
    {
        Mail::fake();

        config(['app.contact_recipient_email' => 'cvicencio@vydabogados.cl, mdominguez@vydabogados.cl']);

        $payload = $this->validPayload();

        $this->post(route('public.contact.store'), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $message = ContactMessage::query()->firstOrFail();

        $this->assertSame($payload['email'], $message->email);

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail): bool {
            return $mail->hasTo('cvicencio@vydabogados.cl')
                && $mail->hasTo('mdominguez@vydabogados.cl')
                && $mail->hasReplyTo('cliente@example.com');
        });
    }

    public function test_contact_message_is_kept_when_email_fails(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new RuntimeException('SMTP unavailable'));

        config(['app.contact_recipient_email' => 'contacto@vydabogados.cl']);

        $this->post(route('public.contact.store'), $this->validPayload())
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'cliente@example.com',
            'subject' => 'Consulta inicial',
        ]);
    }

    public function test_repeated_identical_submission_does_not_create_duplicate_message(): void
    {
        Mail::fake();

        $payload = $this->validPayload();

        $this->post(route('public.contact.store'), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->post(route('public.contact.store'), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(1, ContactMessage::query()->count());
    }

    /**
     * @return array<string, string>
     */
    private function validPayload(): array
    {
        return [
            'name' => 'Cliente VYD',
            'email' => 'cliente@example.com',
            'subject' => 'Consulta inicial',
            'message' => 'Necesito apoyo juridico para revisar un caso.',
        ];
    }
}

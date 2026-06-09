@extends('public.layouts.app', [
    'title' => 'Contacto | VYD Abogados',
    'description' => 'Formulario de contacto de VYD Abogados.',
])

@section('content')
    <section class="content">
        <h1>Contacto</h1>
        <p class="content__lead">Formulario básico para validar recepción de mensajes en el CMS.</p>

        @if (session('status'))
            <p style="padding: 12px 16px; border: 1px solid #b7d9bd; background: #eef8f0;">
                {{ session('status') }}
            </p>
        @endif

        <form method="post" action="{{ route('public.contact.store') }}" style="display: grid; gap: 16px; margin-top: 28px;">
            @csrf

            <input name="name" value="{{ old('name') }}" placeholder="Nombre" required>
            <input name="email" value="{{ old('email') }}" placeholder="Email" type="email" required>
            <input name="phone" value="{{ old('phone') }}" placeholder="Teléfono">
            <input name="subject" value="{{ old('subject') }}" placeholder="Asunto">
            <textarea name="message" placeholder="Mensaje" rows="7" required>{{ old('message') }}</textarea>

            @if ($errors->any())
                <div style="color: #a83232;">
                    Revisa los campos del formulario.
                </div>
            @endif

            <button type="submit" style="width: fit-content; padding: 12px 18px; border: 0; background: var(--brand); color: white; font-weight: 700;">
                Enviar mensaje
            </button>
        </form>
    </section>
@endsection

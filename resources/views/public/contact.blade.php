@extends('public.layouts.app', [
    'title' => 'Contacto | VYD Abogados',
    'description' => 'Formulario de contacto de VYD Abogados.',
])

@section('content')
    <section class="content">
        <p class="eyebrow">Contacto</p>
        <h1 class="page-title">Conversemos</h1>
        <p class="lead">Escr&iacute;benos y te responderemos a la brevedad.</p>

        @if (session('success'))
            <p class="notice">{{ session('success') }}</p>
        @endif

        <form method="post" action="{{ route('public.contact.store') }}" class="simple-form">
            @csrf

            <input name="name" value="{{ old('name') }}" placeholder="Nombre" required>
            <input name="email" value="{{ old('email') }}" placeholder="Email" type="email" required>
            <input name="subject" value="{{ old('subject') }}" placeholder="Asunto">
            <textarea name="message" placeholder="Mensaje" rows="7" required>{{ old('message') }}</textarea>

            @if ($errors->any())
                <div style="color: #a83232;">
                    Revisa los campos del formulario.
                </div>
            @endif

            <button class="button" type="submit" style="border: 0;">
                Enviar mensaje
            </button>
        </form>
    </section>
@endsection

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body>
    <h1>Nuevo mensaje de contacto</h1>

    <p><strong>Nombre:</strong> {{ $contactMessage->name }}</p>
    <p><strong>Correo:</strong> {{ $contactMessage->email }}</p>

    @if ($contactMessage->subject)
        <p><strong>Asunto:</strong> {{ $contactMessage->subject }}</p>
    @endif

    <p><strong>Fecha y hora:</strong> {{ $contactMessage->created_at?->format('d-m-Y H:i') }}</p>

    <p><strong>Mensaje:</strong></p>
    <p>{!! nl2br(e($contactMessage->message)) !!}</p>

    @if ($adminUrl)
        <p>
            <a href="{{ $adminUrl }}">Revisar mensaje en el panel administrativo</a>
        </p>
    @endif
</body>
</html>

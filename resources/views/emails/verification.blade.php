<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de correo</title>
</head>
<body>
    <h1>Hola, {{ $name }}</h1>
    <p>Gracias por registrarte. Tu código de verificación es:</p>
    <h2>{{ $verificationCode }}</h2>
    <p>Por favor, ingresa este código en la aplicación para verificar tu cuenta.</p>
</body>
</html>

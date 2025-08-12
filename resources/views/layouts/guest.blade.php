@php
    $backgroundUrl = asset('images/bg.jpg'); // ton image de fond ici
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Connexion - Registre de Passation | CHU de Fès</title>
    <meta name="description" content="Connexion au registre de passation du CHU de Fès - Service des urgences.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url('{{ $backgroundUrl }}');
            background-repeat: no-repeat;
            background-position: center;
            background-color: #f0f4f8; /* Couleur de fond en fallback */
            min-height: 100vh;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
        }

        .login-container {
            width: 100%;
            max-width: 28rem;
            margin: 1rem auto;
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        @media (max-width: 640px) {
            body {
                background-size: auto 100%;
                background-position: top center;
                padding: 0.5rem;
            }

            .login-container {
                margin: 0.5rem;
                padding: 1.25rem;
                width: auto;
            }

            .input-field {
                font-size: 16px !important;
            }
        }

        .hospital-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .hospital-logo {
            height: 60px;
            margin-bottom: 1rem;
        }

        .hospital-title {
            color: #2b6cb0;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .hospital-subtitle {
            color: #4a5568;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="login-container">
            <!-- Logo et titre -->
            <div class="hospital-header">
                <img src="{{ asset('images/Logo_CHU-final.webp') }}" alt="Logo du Centre Hospitalier Universitaire de Fès" class="hospital-logo mx-auto" role="img">
                <h1 class="hospital-title">Registre de Passation</h1>
                <p class="hospital-subtitle">Service des urgences - CHU de Fès</p>
            </div>

            <!-- Contenu de la page (formulaire, etc.) -->
            {{ $slot }}

            <!-- Pied de page -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>© CHU de Fès - Tous droits réservés - {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

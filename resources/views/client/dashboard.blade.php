<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace client — Lotixam</title>
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('img/logo.png') }}" rel="icon" type="image/x-icon">
</head>
<body style="padding: 2rem; font-family: system-ui, sans-serif;">
    <p><img src="{{ asset('img/logo.png') }}" width="120" alt="Lotixam"></p>
    <h1>Bonjour, {{ auth()->user()->name }}</h1>
    <p>Identifiant : <strong>{{ auth()->user()->username }}</strong></p>
    <p>Cet espace remplacera progressivement l’ancien sous-domaine <code>accounts.lotixam.fr</code>.</p>
    <form action="{{ route('logout') }}" method="post" style="margin-top: 2rem;">
        @csrf
        <button type="submit">Déconnexion</button>
    </form>
    <p style="margin-top: 2rem;"><a href="{{ url('/') }}">← Retour au site</a></p>
</body>
</html>

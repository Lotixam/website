<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Lotixam</title>
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/banniere/banniere.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sign.css') }}" rel="stylesheet">
    <link href="{{ asset('img/logo.png') }}" rel="icon" type="image/x-icon">
</head>
<body>
<div class="sign-page">
    <div class="td-1">
        <div class="div-connexion">
            <div class="logo-sign">
                <img src="{{ asset('img/logo.png') }}" alt="Lotixam">
            </div>
            <div class="back">
                <a href="{{ route('home') }}">&lsaquo; Accueil Lotixam</a>
            </div>
            <h2>Connexion</h2>
            <form id="loginForm" action="{{ url('/login') }}" method="post">
                @csrf
                <span>
                    @if ($errors->any())
                        <p class="error">{{ $errors->first('username') }}</p>
                    @endif
                </span>
                <label for="username">Identifiant :</label>
                <br>
                <input name="username" id="username" type="text" value="{{ old('username') }}" required
                    autocomplete="username"
                    oninvalid="this.setCustomValidity('Veuillez entrer votre identifiant')"
                    oninput="this.setCustomValidity('')">
                <br><br>
                <label for="password">Mot de passe :</label>
                <br>
                <input name="password" id="password" type="password" required
                    autocomplete="current-password"
                    oninvalid="this.setCustomValidity('Veuillez entrer votre mot de passe')"
                    oninput="this.setCustomValidity('')">
                <br>
                <div class="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    <label for="remember">Se souvenir de moi</label>
                </div>
                <br>
                <div class="submit-zone">
                    <button class="submit-button" type="submit">Valider</button>
                </div>
            </form>
        </div>
    </div>
    <div class="td-2">
        <div class="lotixam">
            <img src="{{ asset('img/lotixam.png') }}" alt="Lotixam">
        </div>
    </div>
</div>
</body>
</html>

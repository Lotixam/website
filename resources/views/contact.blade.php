<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — Lotixam</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/banniere/banniere.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    @if(request()->query())
        <link rel="canonical" href="{{ url('/contact') }}">
    @endif
</head>
<body>
<div class="sign-page">
    <div class="td-1">
        <div class="div-connexion">
            @if(session('message_sent'))
                <div class="response response--inline" role="status">
                    <p>Merci de nous avoir contacté, nous reviendrons vers vous bientôt.</p>
                </div>
            @endif
            <div class="logo-sign">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Lotixam" class="logo-img">
            </div>
            <br>
            <h1>Contact</h1>
            <form id="loginForm" action="{{ url('/contact') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="name">Nom &amp; Prénom :</label><br>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    oninvalid="this.setCustomValidity('Veuillez entrer votre nom')" oninput="this.setCustomValidity('')">
                <p></p>
                <label for="mail">E-mail :</label><br>
                <input type="email" id="mail" name="mail" value="{{ old('mail') }}" required
                    oninvalid="this.setCustomValidity('Veuillez entrer votre mail')" oninput="this.setCustomValidity('')">
                <p></p>
                <label for="tel">Téléphone :</label><br>
                <input type="tel" id="tel" name="tel" value="{{ old('tel') }}" required
                    oninvalid="this.setCustomValidity('Veuillez entrer votre téléphone.')" oninput="this.setCustomValidity('')">
                <p></p>
                <label for="msg">Votre message :</label><br>
                <textarea id="msg" name="msg" required
                    oninvalid="this.setCustomValidity('Renseignez votre message.')" oninput="this.setCustomValidity('')">{{ old('msg') }}</textarea>
                <p></p>
                <div class="attachments-block">
                    <span class="attachments-block__title" id="attachments-heading">Pièces jointes <span class="attachments-block__hint">(optionnel · jusqu’à 3 fichiers)</span></span>
                    <div class="file-picker" role="group" aria-labelledby="attachments-heading">
                        <input type="file" id="join1" name="join1" class="file-picker__input" aria-describedby="join1-filename">
                        <label for="join1" class="file-picker__btn">Parcourir</label>
                        <span class="file-picker__name" id="join1-filename">Aucun fichier</span>
                    </div>
                    <div class="file-picker" role="group" aria-labelledby="attachments-heading">
                        <input type="file" id="join2" name="join2" class="file-picker__input" aria-describedby="join2-filename">
                        <label for="join2" class="file-picker__btn">Parcourir</label>
                        <span class="file-picker__name" id="join2-filename">Aucun fichier</span>
                    </div>
                    <div class="file-picker" role="group" aria-labelledby="attachments-heading">
                        <input type="file" id="join3" name="join3" class="file-picker__input" aria-describedby="join3-filename">
                        <label for="join3" class="file-picker__btn">Parcourir</label>
                        <span class="file-picker__name" id="join3-filename">Aucun fichier</span>
                    </div>
                </div>
                <p></p>
                <div class="submit-zone">
                    <button type="submit" class="submit-button">Valider</button>
                </div>
                <br>
                <div class="back">
                    <a href="{{ url('/') }}">&lsaquo; Retour</a>
                </div>
            </form>
        </div>
    </div>
    <div class="td-2" aria-hidden="true"></div>
    <div class="lotixam">
        <img src="{{ asset('img/lotixam.png') }}" alt="Lotixam">
    </div>
</div>
<script>
document.querySelectorAll('.file-picker__input').forEach(function (input) {
    var out = document.getElementById(input.id + '-filename');
    if (!out) return;
    input.addEventListener('change', function () {
        out.textContent = input.files && input.files.length ? input.files[0].name : 'Aucun fichier';
    });
});
</script>
</body>
</html>

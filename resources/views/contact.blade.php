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
                <div class="attachments-block" id="attachments-root" data-max="{{ max(1, (int) ($maxAttachments ?? 20)) }}">
                    <span class="attachments-block__title" id="attachments-heading">Pièces jointes <span class="attachments-block__hint">(optionnel)</span></span>
                    <div id="attachment-rows" class="attachment-rows" aria-labelledby="attachments-heading"></div>
                    <div class="attachments-toolbar">
                        <button type="button" class="attachments-add-btn" id="attachment-add">+ Ajouter un fichier</button>
                        <span class="attachments-counter" id="attachment-counter" aria-live="polite"></span>
                    </div>
                </div>
                <p></p>
                <div class="submit-zone">
                    <button type="submit" class="submit-button">Valider</button>
                </div>
                <br>
                <div class="back">
                    <a href="{{ route('home') }}">&lsaquo; Accueil Lotixam</a>
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
(function () {
    var root = document.getElementById('attachments-root');
    var rowsEl = document.getElementById('attachment-rows');
    var addBtn = document.getElementById('attachment-add');
    var counterEl = document.getElementById('attachment-counter');
    if (!root || !rowsEl || !addBtn) return;

    var max = parseInt(root.getAttribute('data-max'), 10) || 20;
    var idSeq = 0;

    function updateCounter() {
        var n = rowsEl.querySelectorAll('.attachment-row').length;
        counterEl.textContent = n ? (n + ' / ' + max + ' fichier' + (n > 1 ? 's' : '')) : '';
        addBtn.disabled = n >= max;
    }

    function bindRow(row) {
        var input = row.querySelector('.file-picker__input');
        var out = row.querySelector('.file-picker__name');
        var removeBtn = row.querySelector('.attachment-remove');
        if (input && out) {
            input.addEventListener('change', function () {
                out.textContent = input.files && input.files.length ? input.files[0].name : 'Aucun fichier';
            });
        }
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                updateCounter();
            });
        }
    }

    function addRow() {
        if (rowsEl.querySelectorAll('.attachment-row').length >= max) return;
        idSeq += 1;
        var uid = 'att-' + idSeq;
        var row = document.createElement('div');
        row.className = 'attachment-row file-picker';
        row.setAttribute('role', 'group');
        row.innerHTML =
            '<input type="file" id="' + uid + '" name="attachments[]" class="file-picker__input">' +
            '<label for="' + uid + '" class="file-picker__btn">Parcourir</label>' +
            '<span class="file-picker__name">Aucun fichier</span>' +
            '<button type="button" class="attachment-remove" aria-label="Retirer ce fichier">×</button>';
        rowsEl.appendChild(row);
        bindRow(row);
        updateCounter();
    }

    addBtn.addEventListener('click', addRow);
    updateCounter();
})();
</script>
</body>
</html>

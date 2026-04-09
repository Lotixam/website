<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance du site — Lotixam</title>
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/banniere/banniere.css') }}" rel="stylesheet">
    <link href="{{ asset('css/maintenance.css') }}" rel="stylesheet">
    <link href="{{ asset('img/logo.png') }}" rel="icon" type="image/x-icon">
</head>
<body>
<div id="bg"></div>
<div id="maintenance">
    <div id="first-plan">
        <div id="lotixam">
            <div id="logo">
                <img src="{{ asset('img/logo.png') }}" width="170" alt="Logo Lotixam">
                <div id="devanture">
                    <img src="{{ asset('img/lotixam.png') }}" width="150" alt="Lotixam">
                    <label id="sentence">L'INVESTISSEUR IMMOBILIER PROFESSIONNEL SI PARTICULIER</label>
                </div>
            </div>
        </div>
        <div>
            <h1 id="announce">SITE EN MAINTENANCE</h1>
            <h3 id="announce">DISPONIBLE TRES PROCHAINEMENT !</h3>
            @if($depuis !== '')
                <p class="maintenance-depuis">Depuis le {{ e($depuis) }}</p>
            @endif
        </div>
    </div>
    <div id="fancy"></div>
</div>
<p class="maintenance-footer">© LOTIXAM SAS. Tous droits réservés.</p>
</body>
</html>

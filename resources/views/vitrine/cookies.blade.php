<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Politique de cookies du site Lotixam : finalités, durées de conservation et moyens de paramétrage." />

    <title>Lotixam — Politique de cookies</title>
    <link href="/img/logo.png" rel="icon" type="image/x-icon">

    <link href="/css/common.css" rel="stylesheet">
    <link href="/css/about.css" rel="stylesheet">
    <link href="/css/banniere/banniere.css" rel="stylesheet">
    <link href="/css/foot.css" rel="stylesheet">
    <link href="/css/legals.css" rel="stylesheet">

</head>

<body id="page">
    <script src="/script/about.js"></script>
    <div id="banniere">

        <div class="sign ">
            @include('vitrine.partials.login-nav-link', ['guestLabel' => "Se connecter / S'inscrire"])
        </div>

        <div id="logo">
            <img src="/img/logo.png" width="170" alt="Logo Lotixam">
            <div id="devanture">
                <img src="/img/lotixam.png" width="150" alt="Enseigne Lotixam">
                <label id="sentence">L'INVESTISSEUR IMMOBILIER PROFESSIONNEL SI PARTICULIER</label>
            </div>
        </div>
        @include('vitrine.partials.main-nav', ['contactPrev' => 'about'])
    </div>

    <div class="longueur">
        <div class="entete">
            <h1 class="h1-up" id="h1up">POLITIQUE DE COOKIES</h1>
        </div>
    </div>
    <div class="body-main legals">
        <h1>1. Qu’est-ce qu’un cookie&nbsp;?</h1>
        <span>
            Un cookie est un petit fichier texte déposé sur votre terminal (ordinateur, tablette, smartphone) lors de la visite d’un site
            web. Il permet notamment de mémoriser des préférences, d’assurer le bon fonctionnement technique du site ou de mesurer
            l’audience.
        </span>

        <h1>2. Cookies utilisés sur ce site</h1>
        <span>
            Lotixam utilise uniquement des cookies et mécanismes similaires nécessaires au fonctionnement du service&nbsp;:
            <ul>
                <li><strong>Cookies de session et de sécurité</strong> (par exemple jeton CSRF Laravel)&nbsp;: ils permettent de sécuriser
                    les formulaires et l’espace connecté. Ils sont en général de courte durée et peuvent être qualifiés de cookies
                    strictement nécessaires.</li>
                <li><strong>Cookies de préférences d’affichage</strong> sur certaines pages (ex.&nbsp; thème clair/sombre dans l’espace
                    membre)&nbsp;: stockés localement dans le navigateur lorsque vous en faites le choix.</li>
            </ul>
            Nous ne déposons pas de cookies publicitaires de tiers sur la vitrine dans le cadre décrit ci-dessus. Si des outils de mesure
            d’audience ou des contenus tiers intégrés (vidéos, cartes) venaient à évoluer, cette page sera mise à jour en conséquence.
        </span>

        <h1>3. Base légale et durée</h1>
        <span>
            Les cookies strictement nécessaires au service sollicité sont fondés sur l’intérêt légitime et/ou l’exécution de mesures
            précontractuelles ou contractuelles. Leur durée de vie est limitée à la session ou à quelques heures/jours selon les
            réglages du framework. Pour les préférences stockées côté navigateur (localStorage), la durée dépend de votre choix et de
            votre effacement manuel des données du site.
        </span>

        <h1>4. Comment refuser ou supprimer les cookies&nbsp;?</h1>
        <span>
            Vous pouvez à tout moment configurer votre navigateur pour refuser les cookies ou être averti avant dépôt. Le refus des cookies
            indispensables peut empêcher certaines fonctionnalités (formulaires, connexion). Des informations pratiques sont disponibles
            sur le site de la <a href="https://www.cnil.fr/fr/cookies-et-autres-traceurs" rel="noopener noreferrer">CNIL</a>.
        </span>

        <h1>5. Données personnelles et contact</h1>
        <span>
            Pour les traitements liés aux données personnelles (hors simples cookies techniques), voir également nos
            <a href="{{ \Illuminate\Support\Facades\Route::has('vitrine.legals') ? route('vitrine.legals') : url('/mentions-legales') }}">mentions légales</a> et la page
            <a href="{{ route('contact') }}">contact</a> pour toute question.
        </span>
    </div>
</body>
@include('vitrine.partials.footer')

</html>

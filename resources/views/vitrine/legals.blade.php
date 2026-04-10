<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    <title>Lotixam - Mentions Légals</title>
    <link href="/img/logo.png" rel="icon" type="image/x-icon">

    <link href="/css/common.css" rel="stylesheet">
    <link href="/css/about.css" rel="stylesheet">
    <link href="/css/banniere/banniere.css" rel="stylesheet">
    <link href="/css/foot.css" rel="stylesheet">
    <link href="/css/legals.css" rel="stylesheet">

</head>

<body id="page">
    <script src="/script/about.js"></script>
    <!--Bannière-->
    <div id="banniere">

        <div class="sign ">
            @include('vitrine.partials.login-nav-link', ['guestLabel' => "Se connecter / S'inscrire"])
        </div>

        <div id="logo">
            <img src="/img/logo.png" width="170">
            <div id="devanture">
                <img src="/img/lotixam.png" width="150">
                <label id="sentence">L'INVESTISSEUR IMMOBILIER PROFESSIONNEL SI PARTICULIER</label>
            </div>
        </div>
        @include('vitrine.partials.main-nav', ['contactPrev' => 'about'])
    </div>

    <!--Corps de la page-->

    <div class="longueur">
        <div class="entete">
            <h1 class="h1-up" id="h1up">MENTIONS LEGALES</h1>
        </div>
    </div>
    <div class="body-main legals">
        <h1>1. Collecte et utilisation des informations</h1>
        <span>
            Toute les informations recueillis par le formulaire de contact ainsi que les cookies peuvent être utilisés pour :
            <ul>
                <li>Personnaliser votre expérience de navigation</li>
                <li>Améliorer le site web</li>
                <li>Récolter des statistiques de visite</li>
                <li>Vous contacter par e-mail</li>
            </ul>
        </span>
        <h1>2. Confidentialité de vos informations</h1>
        <span>
            Vos informations personnelles ne seront pas vendues, échangées, transférées, ou données à une autre société sans votre
            consentement, en dehors de ce qui est nécessaire pour répondre à une demande.
        </span>
        <h1>3. Protection des informations</h1>
        <span>
            Notre engagement envers la protection des informations personnelles comprend plusieurs mesures essentielles. Tout
            d'abord, nous utilisons le protocole sécurisé HTTPS pour garantir la sécurité des informations personnelles que vous
            nous communiquez via le formulaire de contact ou pendant la phase de connexion. De plus, les mots de passe ne sont
            jamais sauvegardés en clair dans la base de données utilisée pour le site, ni aucune autre information sensible. Chaque
            utilisateur, connecté ou non, se voit attribuer un identifiant unique qui retient les choix effectués, assurant ainsi
            une cohérence d'une page à l'autre. Cet identifiant n'est jamais sauvegardé par le site, sauf en cas de connexion, et
            est utilisé uniquement pour parcourir les connexions afin de protéger le compte en cas d'activité suspecte. De plus, les
            mots de passe sont cryptés à l'aide de l'algorithme SHA256, garantissant ainsi qu'ils ne sont jamais transmis en clair
            lors des transferts, sauf dans le cas d'une inscription, où cela est nécessaire. Enfin, nous respectons pleinement les
            droits des utilisateurs concernant leurs données personnelles, y compris le droit d'accès, de rectification et de
            suppression. Pour plus d'informations sur la manière dont nous gérons vos données, veuillez consulter notre politique de
            confidentialité complète.
        </span>
        <h1>4. Utilisation des cookies</h1>
        <span>
            Notre site utilise des cookies pour améliorer l'expérience des utilisateurs et faciliter l'accès aux fonctionnalités du
            site. Les cookies sont de petits fichiers texte placés sur votre appareil par le serveur du site web. Ils permettent au
            site de se souvenir de vos actions et préférences (telles que la langue choisie et les paramètres de connexion) sur une
            période de temps donnée, ce qui vous évite de les saisir à nouveau chaque fois que vous revenez sur le site ou naviguez
            entre ses pages. De plus, les cookies sont utilisés pour suivre et analyser la manière dont les utilisateurs
            interagissent avec le site, ce qui nous permet d'améliorer constamment son contenu et son fonctionnement, ainsi que de
            comprendre les tendances d'utilisation. Il est important de noter que l'utilisation des cookies sur notre site n'est en
            aucune façon liée à des informations personnelles identifiables. Les informations collectées par le biais des cookies
            sont agrégées et anonymisées, et ne sont utilisées que dans le but d'améliorer l'expérience globale de nos utilisateurs
            sur le site. Pour le détail des cookies déposés et vos choix, consultez notre
            <a href="{{ \Illuminate\Support\Facades\Route::has('vitrine.cookies') ? route('vitrine.cookies') : url('/politique-cookies') }}">politique de cookies</a>.
        </span>
        <h1>5. Consentement</h1>
        <span>
            En utilisant notre site, vous consentez à notre politique de confidentialité et à l'utilisation des cookies, comme
            décrit dans les sections précédentes. Conformément à la loi n° 78-17 du 6 janvier 1978 relative à l'informatique, aux
            fichiers et aux libertés, vous avez le droit d'accéder, de rectifier et de supprimer les informations personnelles vous
            concernant. Si vous souhaitez exercer ces droits ou avez des questions concernant notre politique de confidentialité,
            veuillez nous contacter via les coordonnées fournies sur le site. Nous nous engageons à traiter vos demandes dans les
            meilleurs délais et à garantir la protection de vos données personnelles conformément à la réglementation en vigueur.
        </span>
        <h1>6. Entreprise</h1>
        <span>
            LOTIXAM SAS<br>
            5 Chemin des Chênes<br>
            17210 - Bussac-forêt<br>
            France<br><br>

            Tél : <a href="tel:+33603356836" id="tel">06.03.35.68.36</a><br><br>

            SIRET : 949 549 299 00012
        </span>
        <h1>7. Responsables de publication</h1>
        <span>
            <div>
                Lucas ESPINAR
            </div>
            <div>
                Xavier ESPINAR
            </div>
        </span>
        <h1>8. Hébergement</h1>
        <span>
            Le site est hébergé par la société LWS (Ligne Web Services), spécialisée dans les services d'hébergement web. Cette entreprise assure le
            stockage sécurisé des données du site et garantit sa disponibilité en ligne. LWS, entreprise française, a été choisie pour son service fiable et son support technique en français.
            <br><br>
            LWS (Ligne Web Services)<br>

            10 Rue de Penthièvre<br>
            75008 Paris<br>
            France
        </span>
    </div>
    <!-- Fin du corps de page -->
</body>
@include('vitrine.partials.footer')

</html>
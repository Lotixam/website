<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    <title>Lotixam - Partenaires</title>
    <link href="/img/logo.png" rel="icon" type="image/x-icon">

    <link href="/css/common.css" rel="stylesheet">
    <link href="/css/about.css" rel="stylesheet">
    <link href="/css/banniere/banniere.css" rel="stylesheet">
    <link href="/css/foot.css" rel="stylesheet">
    <link href="/css/contributors.css" rel="stylesheet">

</head>

<body id="page">
    <script src="/script/investor.js"></script>
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
        <div class="entete" id="entete-spe">
            <h1 class="h1-up" id="h1up">CONTRIBUTEURS &amp; PARTENAIRES</h1>
        </div>
    </div>
    <div class="body-main legals">
        <div id="xavier" class="contri">
            <h1>Président de LOTIXAM SAS</h1>
            <h2>Xavier ESPINAR</h2>
            <img src="/img/xavier.jpeg" class="trombinoscope">
            <div class="icon">
                <a href="https://www.facebook.com/xavier.espinarramirez">
                    <img src="/img/icons/facebook.svg">
                </a>
                <a href="https://www.linkedin.com/in/xavier-espinar-120113239/">
                    <img src="/img/icons/linkedin.svg">
                </a>
            </div>
        </div>
        <br><br>
        <div class="traitv2"></div>
        <div id="lucas" class="contri">
            <h1>Développeur Stack Full Dev</h1>
            <h2>Lucas ESPINAR</h2>
            <img src="/img/lucas.jpg" class="trombinoscope">
            <div class="icon">
                <a href="https://www.instagram.com/lucas_shaya/">
                    <img src="/img/icons/instagram.svg">
                </a>
                <a href="https://www.facebook.com/shaya2k15">
                    <img src="/img/icons/facebook.svg">
                </a>
                <a href="https://www.linkedin.com/in/lucas-espinar-4480a078/">
                    <img src="/img/icons/linkedin.svg">
                </a>
                <a href="https://github.com/Shayajs">
                    <img src="/img/icons/github.svg">
                </a>
            </div>
        </div>
        <br><br>
        <div class="traitv2"></div>
        <div id="cannelle" class="contri">
            <h1>Prestataire photographe</h1>
            <h2>Cannelle NEBOT</h2>
            <img src="/img/knl_photo.png" class="trombinoscope">
            <div class="icon">
                <a href="https://www.instagram.com/nebotcannelle/">
                    <img src="/img/icons/instagram.svg">
                </a>
                <a href="https://www.facebook.com/profile.php?id=100087262663627">
                    <img src="/img/icons/facebook.svg">
                 </a>
                <a href="https://www.linkedin.com/in/cannelle-nebot-50422422b/">
                    <img src="/img/icons/linkedin.svg">
                </a>
                <a href="https://nebotcannelle.wordpress.com/">
                    <img src="/img/icons/site.svg">
                </a>
            </div>
        </div>
    </div>
    <!-- Fin du corps de page -->
</body>
@include('vitrine.partials.footer')

</html>
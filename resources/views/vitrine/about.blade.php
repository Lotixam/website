<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        
        <title>Lotixam - Qui sommes nous</title>
        <link href="/img/logo.png" rel="icon" type="image/x-icon">
    
        <link href="/css/common.css" rel="stylesheet">
        <link href="/css/about.css" rel="stylesheet">
        <link href="/css/banniere/banniere.css" rel="stylesheet">
        <link href="/css/foot.css" rel="stylesheet">

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
                <h1 class="h1-up" id="h1up">QUI SOMMES NOUS</h1>
            </div>
        </div>
        <div class="body-main">
            <div class="summary" id="sum">
                <div class="text-sumary">
                    <div class="one">
                        Présentation
                    </div>
                    <div class="trait"></div>
                    <div class="two">
                        Notre vision
                    </div>
                    <div class="trait"></div>
                    <div class="three">
                        Nos valeurs
                    </div>
                </div>
            </div>
            <div class="main margin-top-div">
                <div class="center">
                    <div style="text-align: center;">
                        <h1>Nos secteurs d'influence</h1>
                    </div>
                    <div class="zone-d-influence">
                        Charente-Maritime  -  Nord-Gironde  -  Charente
                    </div>
                </div>

                <!-- Paragraphe 1 -->
                <div class="padding-top-div paragraphe">
                    <div class="margin-top-div moitier texte left enter-txt texte-left">
                        <div>
                            Chez <b>LOTIXAM</b>, notre objectif est d'identifier et d'acquérir des propriétés immobilières présentant un
                            potentiel exceptionnel de
                            valorisation.
                            <br><br>
                            En tant qu'investisseurs et marchands de biens expérimentés, nous recherchons constamment de nouvelles
                            opportunités dans divers secteurs du marché immobilier.
                            <br><br>
                            <ul>
                                <li>
                                    Terrains constructibles
                                </li>
                                <li>
                                    Maisons individuelles
                                </li>
                                <li>
                                    Immeubles
                                </li>
                                <li>
                                    Ensembles immobiliers
                                </li>
                                <li>
                                    Locaux commerciaux
                                </li>
                                <li>
                                    Bureaux
                                </li>
                            </ul>
                            <br><br>
                            <h2>
                                A quoi sont destinés les biens acquis ?
                            </h2>
                            Les biens sont :
                            <br>
                            <ul>
                                <li>
                                    Revendus à des particuliers ou des professionnels.
                                </li>
                                <li>
                                    Gardés au sein de notre entreprise qui en assurera la gestion locative. 
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="img-1" class="right moitier margin-top-div">
                        <img src="/img/fortboyard.jpeg" class="image">
                    </div>
                </div>

                <!-- Paragraphe 2 -->
                <div id="para2" class="padding-top-div paragraphe">
                    <div id="img-2" class="moitier margin-top-div left ">
                        <img src="/img/meeting.jpg" class="image" width="100%">
                    </div>
                    <div class="margin-top-div moitier texte texte-right">
                        Chez <b>LOTIXAM</b>, nous sommes passionnés d'immobilier et avons vocation à fournir les solutions
                        les plus adaptées à vos exigences.
                        <br><br>
                        Que vous envisagiez l'acquisition d'un terrain pour concrétiser votre projet de
                        construction ou que vous souhaitiez investir dans un logement prêt à l'emploi, notre priorité est de vous accompagner à
                        chaque étape.
                        <br><br>
                        La relation de confiance est au cœur de notre approche.
                        <br><br>
                        Notre expertise se manifeste à travers notre engagement à soutenir nos clients tout au long des étapes de leur projet
                        immobilier, de l'amorce jusqu'à la réalisation.
                        <div class="contact contact-pc" id="contact-welcome">
                            <button class="contact-button" id="contact-button-index-1"
                                onclick="window.location.href = '/contact?prev=about&button=true';">CONTACTEZ-NOUS</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="diagramm padding-top-div">
                <img src="/img/diagLotixam.png" class="image-diag"/>
            </div>
        </div>
        <div class="contact" id="contact-welcome">
            <button class="contact-button" id="contact-button-index-1"
                onclick="window.location.href = '/contact?prev=about&button=true';">CONTACTEZ-NOUS</button>
        </div>

        <!-- Fin du corps de page -->
    </body>
    @include('vitrine.partials.footer')
</html>
<?php
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Connexion</title>
        <link href="../css/common.css" rel="stylesheet"/>
        <link href="../css/banniere/banniere.css" rel="stylesheet"/>
        <link href="../css/sign.css" rel="stylesheet" />
        <link href="../img/logo.png" rel="icon"/>
    </head>
    <body>
        <!-- Page principale -->
        <div class="sign-page">
            <!-- Page de gauche -->
            <div class="td-1">
                <div class="div-connexion">
                    <div class="logo-sign">
                        <img src="../img/logo.png"/>
                    </div>
                    <div class="back">
                        <a href="javascript:history.go(-1)">&lsaquo; Retour</a>
                    </div>
                    <h1>Connexion</h1>
                    <form id="loginForm" action="sign.php" method="post">
                        <label>Identifiant :</label>
                        <br>
                        <input name="username" id="username" type="text" required
                        oninvalid="this.setCustomValidity('Veuillez entrer votre identifiant')"
                        oninput="this.setCustomValidity('')" />
                        <br><br>
                        <label>Mot de passe :</label>
                        <br>
                        <input name="password" id="password" type="password" required
                        oninvalid="this.setCustomValidity('Veuillez entrer votre mot de passe')"
        oninput="this.setCustomValidity('')" />
                        <br></p>
                        <div class="remember">
                            <input id="check-remember" type="checkbox">
                            <label>Se souvenir de moi</label>
                        </div>
                        <br></p>
                        <div class="submit-zone">
                            <button class="submit-button" type="submit">Valider</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Page de droite -->
            <div class="td-2">
                <div class="lotixam">
                    <img src="../img/lotixam.png" />
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        <script src="../script/sign.js"></script>
    </body>
</html>

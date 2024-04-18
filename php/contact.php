<?php
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Contact</title>
        <link href="../css/common.css" rel="stylesheet"/>
        <link href="../css/banniere/banniere.css" rel="stylesheet"/>
        <link href="../css/contact.css" rel="stylesheet" />
        <link href="../img/logo.png" rel="icon"/>
    </head>
    <body>
        <!-- Page principale -->
        <div class="sign-page">
            <!-- Page de gauche -->
            <div class="td-1">
                <!-- Formulaire et logo -->
                <div class="div-connexion">
                    <div class="logo-sign">
                        <img src="../img/logo.png"/>
                    </div>
                    <h1>Contact</h1>
                    <form id="loginForm" action="" method="post">
                        <label>Nom &amp; Prénom :</label>
                        <br>
                        <input name="name" id="name" type="text" required
                        oninvalid="this.setCustomValidity('Veuillez entrer votre nom')"
                        oninput="this.setCustomValidity('')" />
                        </p>
                        <label>E-mail :</label>
                        <br>
                        <input name="mail" id="mail" type="mail" required
                        oninvalid="this.setCustomValidity('Veuillez entrer votre mail')"
                        oninput="this.setCustomValidity('')" />
                        </p>
                        <label>Téléphone :</label>
                        <br>
                        <input name="tel" id="tel" type="text" required
                        oninvalid="this.setCustomValidity('Veuillez entrer votre prénom')"
                        oninput="this.setCustomValidity('')" />
                        </p>
                        <label>Votre message :</label>
                        <br>
                        <textarea name="msg" id="msg" type="text" required
                        oninvalid="this.setCustomValidity('Veuillez entrer votre identifiant')"
                        oninput="this.setCustomValidity('')"></textarea>
                        </p>
                        <label for="joindre">Joindre un ou plusieurs fichiers</label>
                        <br><br>
                        <input type="file" id="join1" name="join1" />
                        <br>
                        <input type="file" id="join2" name="join2" />
                        <br>
                        <input type="file" id="join3" name="join3" />
                        <br></p>
                        <div class="submit-zone">
                            <button class="submit-button" type="submit">Valider</button>
                        </div>
                        <br>
                        <div class="back">
                            <a href="javascript:history.go(-1)">&lsaquo; Retour</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Page de droite -->
            <div class="td-2">
                <p>
                    <pre class="response">
                        <?php
                            if($_SERVER["REQUEST_METHOD"] == "POST"){
                                echo print_r($_POST);
                            }
                        ?>
                    </pre>
                </p>
            </div>
            <div class="lotixam">
                <img src="../img/lotixam.png" />
            </div>
        </div>
    </body>
</html>

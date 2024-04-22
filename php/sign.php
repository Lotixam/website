<?php
    session_start();

    function kodex_random_string($length=40){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for($i=0; $i<$length; $i++){
            $string .= $chars[rand(0, strlen($chars)-1)];
        }
        return $string;
    }

    $current_user = array();

    function exists() : bool {
        global $current_user;
        try
        {
            $mysqlClient = new PDO(
	        'mysql:host=lotixam.fr:3306;dbname=esxa6815_vues;charset=utf8',
	        'esxa6815_connexion',
	        'Z@JV@HtxThT2'
        );
        }
        catch (Exception $e)
        {
            die('Erreur : ' . $e->getMessage());
        }

        $recipesStatement = $mysqlClient->prepare('SELECT * FROM esxa6815_vues.data_vue');
        $recipesStatement->execute();
        $recipes = $recipesStatement->fetchAll();

        foreach($recipes as $user) {
            if(in_array($_POST['username'], $user)) {
                $current_user = $user;
                return true;
            }
            else {
                return false;
            }
        }
    }

    $connected = false;
    $good_password = false;
    $good_user = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // La requête provient d'un formulaire POST
        if(exists()) {
            if($current_user['sha_password'] == $_POST['password']) {
                setcookie('id', kodex_random_string(), time()+1200, '/', 'lotixam.fr');
                setcookie('username', $_POST['username'], time()+1200, '/', 'lotixam.fr');

                header('Location: http://accounts.lotixam.fr/index.php');
                $connected = true;
                $good_password = true;
                $good_user = true;
                exit();
            } else {
                $good_user = true;
            }
        }
    }
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
                        <a href="../html/index.html">&lsaquo; Retour</a>
                    </div>
                    <h1>Connexion</h1>
                    <form id="loginForm" action="sign.php" method="post">
                        <span>
                            <?php
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                if($good_user == false) {
                                    echo '<p class="error">Nom d\'utilisateur invalide.</p>';
                                } elseif ($good_password == false) {
                                    echo '<p class="error">Mot de passe incorrect.</p>';
                                }
                            }
                            ?>
                        </span>
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

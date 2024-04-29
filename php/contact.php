<?php
    session_start();

    $message_sent = false;
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = strip_tags(trim($_POST["name"]));
        $email = filter_var(trim($_POST["mail"]), FILTER_SANITIZE_EMAIL);
        $tel = strip_tags(trim($_POST["tel"]));
        $message = trim($_POST["msg"]);

        if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $recipients = ["xavier.espinar@lotixam.fr", $email];
            $subject = "Contact de $name";
            $email_content = "Nom: $name\n";
            $email_content .= "Email: $email\n\n";
            $email_content .= "Tel: $tel\n\n";
            $email_content .= "Message:\n$message\n";

            // Préparation des fichiers attachés
            $attachments = [];
            for ($i = 1; $i <= 3; $i++) {
                if (isset($_FILES["join$i"]) && $_FILES["join$i"]["error"] == UPLOAD_ERR_OK) {
                    $attachments[] = $_FILES["join$i"];
                }
            }

            // Préparation de l'email
            $mime_boundary = "----".md5(time())."----";
            $email_headers = "From: $name <$email>\r\n";
            $email_headers .= "MIME-Version: 1.0\r\n";
            $email_headers .= "Content-Type: multipart/mixed; boundary=\"$mime_boundary\"\r\n";

            // Création du corps du message
            $email_message = "--$mime_boundary\r\n";
            $email_message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
            $email_message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $email_message .= $email_content . "\r\n\r\n";

            // Ajout des fichiers attachés
            foreach ($attachments as $attachment) {
                $file_data = file_get_contents($attachment['tmp_name']);
                $file_data = chunk_split(base64_encode($file_data));

                $email_message .= "--$mime_boundary\r\n";
                $email_message .= "Content-Type: application/octet-stream; name=\"".$attachment['name']."\"\r\n";
                $email_message .= "Content-Description: ".$attachment['name']."\r\n";
                $email_message .= "Content-Disposition: attachment; filename=\"".$attachment['name']."\"; size=".filesize($attachment['tmp_name']).";\r\n";
                $email_message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $email_message .= $file_data . "\r\n\r\n";
            }

            $email_message .= "--$mime_boundary--";

            // Envoi de l'email à chaque destinataire
            foreach ($recipients as $recipient) {
                if (mail($recipient, $subject, $email_message, $email_headers)) {
                    $message_sent = true;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact</title>
        <link rel="stylesheet" href="../css/common.css">
        <link rel="stylesheet" href="../css/banniere/banniere.css">
        <link rel="stylesheet" href="../css/contact.css">
        <link rel="icon" href="../img/logo.png">
    </head>
    <body>
        <!-- Page principale -->
        <div class="sign-page">
            <!-- Page de gauche -->
            <div class="td-1">
                <!-- Formulaire et logo -->
                <div class="div-connexion">
                    <!-- Logo Lotixam -->
                    <div class="logo-sign">
                        <img src="../img/logo.png" alt="Logo Lotixam" class="logo-img" />
                    </div>
                    <br>
                    <h1>Contact</h1>
                    <form id="loginForm" action="contact.php" method="post" enctype="multipart/form-data">
                        <label for="name">Nom &amp; Prénom :</label><br>
                        <input type="text" id="name" name="name" required oninvalid="this.setCustomValidity('Veuillez entrer votre nom')" oninput="this.setCustomValidity('')">
                        <p></p>
                        <label for="mail">E-mail :</label><br>
                        <input type="email" id="mail" name="mail" required oninvalid="this.setCustomValidity('Veuillez entrer votre mail')" oninput="this.setCustomValidity('')">
                        <p></p>
                        <label for="tel">Téléphone :</label><br>
                        <input type="tel" id="tel" name="tel" required oninvalid="this.setCustomValidity('Veuillez entrer votre téléphone.')" oninput="this.setCustomValidity('')">
                        <p></p>
                        <label for="msg">Votre message :</label><br>
                        <textarea id="msg" name="msg" required oninvalid="this.setCustomValidity('Renseignez votre message.')" oninput="this.setCustomValidity('')"></textarea>
                        <p></p>
                        <label for="join1">Joindre un ou plusieurs fichiers</label><br><br>
                        <input type="file" id="join1" name="join1">
                        <br>
                        <input type="file" id="join2" name="join2">
                        <br>
                        <input type="file" id="join3" name="join3">
                        <br><p></p>
                        <div class="submit-zone">
                            <button type="submit" class="submit-button">Valider</button>
                        </div>
                        <br>
                        <div class="back">
                            <a href="../html/index.html">&lsaquo; Retour</a>
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
                                if($message_sent) {
                                    echo "<p>Merci de nous avoir contacté, nous reviendrons vers vous bientôt.</p>";
                                }
                            }
                        ?>
                    </pre>
                </p>
            </div>
            <div class="lotixam">
                <img src="../img/lotixam.png" alt="Lotixam">
            </div>
        </div>
    </body>
</html>


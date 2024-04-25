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
                    <form id="loginForm" action="" method="post" enctype="multipart/form-data">
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
                        oninvalid="this.setCustomValidity('Veuillez entrer votre téléphone.')"
                        oninput="this.setCustomValidity('')" />
                        </p>
                        <label>Votre message :</label>
                        <br>
                        <textarea name="msg" id="msg" type="text" required
                        oninvalid="this.setCustomValidity('Renseignez votre message.')"
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
                <img src="../img/lotixam.png" />
            </div>
        </div>
    </body>
</html>

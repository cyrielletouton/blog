<?php
// Vu qu'on utilisera PHPMailer, on importe ses fichiers
require_once('inc/PHPMailer/Exception.php');
require_once('inc/PHPMailer/PHPMailer.php');
require_once('inc/PHPMailer/SMTP.php');

// PHPMailer est un PHP Orienté Objet
// On appelle les classes Exception et PHPMailer
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// On vérifie qu'on recoit un mail dans le POST
// On vérifie si l'adresse existe dans la base
if (isset($_POST['mail']) && !empty($_POST['mail'])) {
    // On a recu une adresse email
    // On récupère et on nettoie les données
    $email = strip_tags($_POST['mail']);

    // On se connecte à la base
    require_once('inc/connect.php');

    // On va chercher un utilisateur ayant cette adresse email
    // On écrit la requête SQL
    $sql = 'SELECT * FROM `users` WHERE `email` = :email ;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':email', $email, PDO::PARAM_STR);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // On vérifie si on a un utilisateur
    if (!$user) {
        // L'email n'existe pas dans la base
        // On redirige avec un message de session
    } else {
        // On a un utilisateur, on peut générer un token et l'envoyer
        // On génère le token
        $token = md5(uniqid());

        // On stocke le token dans la base
        // On écrit la requête SQL
        $sql = "UPDATE `users` SET `reset_token` = '$token' WHERE `id`= " . $user['id'];
        // Cette concaténation remplace les bindValue

        // Donc sans les bind, pas besoin de prepare et execute, juste query
        $query = $db->query($sql);

        // On se déconnecte de la base
        require_once('inc/close.php');


        // On prépare l'envoi du mail
        // On instancie PHPMailer
        $mail = new PHPMailer();

        // On configure PHPMailer
        // On utilise SMTP
        $mail->isSMTP();

        // On définit le serveur SMTP
        $mail->Host = 'localhost';

        // On définit le port du serveur
        $mail->Port = 1025;

        // On met en place le charset utf-8
        $mail->CharSet = 'utf-8';
        // Fin de la configuration

        // On essaie d'envoyer un mail
        try {
            // On définit l'expéditeur
            $mail->setFrom('souvenirs@voyages.fr', 'Souvenirs de voyages');

            // On définit le destinaire
            $mail->addAddress($user['email'], $user['name']);

            // On définit le sujet du mail
            $mail->Subject = 'Rénitialisation du mot de passe pour le compte' . $user['email'];

            // On définit que le message sera envoyé en HTML
            $mail->isHTML();

            // On définit le corps du message
            $mail->Body = '
                    <h1>Rénitialiser votre mot de passe</h1>
                    <p>Une réniatialisation de mot de passe a été demandée pour votre compte ' . $user['email'] . '. Si vous avez effectué cette demande, veuillez cliquer sur le lien ci-dessous : </p>
                    <a href="http://localhost/blog-ct/reset_pass.php?token=' . $token . '"> http://localhost/blog-ct/reset_pass.php?token=' . $token . ' </a>
                    ';

            // On peut définir un contenu de texte
            // $email->AltBody = 'Ceci est le texte en format texte brut';

            // On envoie le mail
            $mail->send();

            echo 'Le mail est envoyé';
        } catch (Exception $e) {
            // Ici le mail n'est pas parti
            echo $e->errorMessage();
        }
    } // Fin de si !$user

} // Fin de isset post email


?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Mot de passe oublié</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="index.php">CyTravel</i></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_articles.php">Admin articles <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_users.php">Admin users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_localisation.php">Admin localisation</a>
                </li>
            </ul>
        </div>
    </nav>

    <h1 class="text-center">Mot de passe oublié</h1>
    <p class="text-center">Veuillez entrer votre adresse e-mail ci-dessous</p>
    <form method="post" class="text-center">
        <div>
            <label for="mail">E-mail : </label>
            <input type="email" id="mail" name="mail">
        </div>
        <button class="btn btn-primary">Valider</button>
    </form>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
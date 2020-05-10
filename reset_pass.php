<?php

session_start();

// Vu qu'on utilisera PHPMailer, on importe ses fichiers
require_once('inc/PHPMailer/Exception.php');
require_once('inc/PHPMailer/PHPMailer.php');
require_once('inc/PHPMailer/SMTP.php');

// PHPMailer est un PHP Orienté Objet
// On appelle les classes Exception et PHPMailer
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// On vérifie qu'on recoit un token dans l'URL
// On vérifie si le token existe dans la base
if (isset($_GET['token']) && !empty($_GET['token'])) {
    // On récupère et on nettoie les données
    $token = strip_tags($_GET['token']);

    // On se connecte à la base
    require_once('inc/connect.php');

    // On écrit la requête SQL
    $sql = 'SELECT * FROM `users` WHERE `reset_token` = :token ;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':token', $token, PDO::PARAM_STR);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // On vérifie si on a un utilisateur
    if (!$user) {
        // Le token n'existe pas dans la base
        // On redirige avec un message de session
        $_SESSION['error'] = 'Le lien de réinitialisation de mot de passe a déjà été utilisé';
        header('Location: index.php');
    } // Gestion du changement de mot de passe
    require_once('inc/lib.php');
    if (verifForm($_POST, ['pass1', 'pass2'])) {
        // On récupère les données
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];

        // On vérifie que les 2 mots de passe sont identiques
        if ($pass1 == $pass2) {
            // Ici les 2 mots de passe sont identiques
            // On chiffre le mot de passe
            $pass = password_hash($pass1, PASSWORD_BCRYPT);

            // On écrit la requête
            $sql = "UPDATE `users` SET `password` = '$pass', `reset_token` = null WHERE `id`= " . $user['id'];

            // On exécute la requête
            $query = $db->query($sql);

            // Permet de connaître le nombre d'enregistrements affectés par notre requête
            // die ("Nombre de lignes modifiées : " .$query->rowCount());
            
            // On envoie un mail de confirmation
            // On instancie le service mail
            $mail = new PHPMailer();

            // On configure le serveur
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->CharSet = 'utf-8';

            // Fin configuration

            try{
                $mail->setFrom('souvenirs@voyages.fr', 'Souvenirs de voyages');
                $mail->addAddress($user['email'], $user['name']);
                $mail->Subject = "Mot de passe changé";
                $mail->isHTML();
                $mail->Body = '
                    <h1>Mot de passe changé</h1>
                    <p>Votre mot de passe a été changé avec succès sur notre site exceptionnel</p>
                    <a href="http://localhost/blog-ct">Venez, c\'est par ici</a>
                ';
                $mail->send();

            } catch(Exception $e){
                echo $e->errorMessage();
            }

            $_SESSION['message'] = 'Votre mot de passe a été modifié avec succès';
            header('Location: connexion.php');

        } else {
            $_SESSION['error'] = 'Les deux mots de passe ne sont pas identiques';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die;
        }
    }
} else {
    $_SESSION['error'] = 'Vous n\'avez pas demandé de réinitialisation de mot de passe';
    header('Location: index.php');
}






?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renitialisation mot de passe</title>
</head>

<body>

    <h1>Rénitialisation du mot de passe</h1>

    <?php
    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
        echo $_SESSION['error'];
        unset($_SESSION['error']);
    }
    ?>

    <form method="post">
        <div>
            <label for="pass1">Entrez un nouveau mot de passe : </label>
            <input type="password" id="pass1" name="pass1">
        </div>
        <div>
            <label for="pass2">Confirmez votre mot de passe : </label>
            <input type="password" id="pass2" name="pass2">
        </div>
        <button>Confirmer le nouveau mot de passe</button>
    </form>

</body>

</html>
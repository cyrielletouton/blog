<?php
// On active l'accès à la session
session_start();

// Formulaire de connexion
// Doit afficher en haut de page "Vous êtes connecté(e)" si le mail et le mot de passe sont bons
// Doit afficher en haut de page "Email et/ou mot de passe invalide" si le mail et le mot de passe ne sont pas bons

// On vérifie que $_POST existe et qu'il n'est pas vide
if (isset($_POST) && !empty($_POST)) {
    // On vérifie que tous les champs sont remplis
    require_once('inc/lib.php');
    if (verifForm($_POST, ['mail', 'pass'])) {
        // On récupère les valeurs saisies
        $mail = strip_tags($_POST['mail']);
        $pass = $_POST['pass'];

        // On vérifie si l'email existe dans la base de données
        // On se connecte à la base
        require_once('inc/connect.php');

        // On écrit la requête
        $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs
        $query->bindValue(':email', $mail, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On récupère les données
        $user = $query->fetch(PDO::FETCH_ASSOC);

        // Soit on a une réponse dans $user, soit non
        if (!$user) {
            echo 'Email et/ou mot de passe invalide';
        } else {
            // On vérifie que le mot de passe saisi correpond à celui en base
            // password_verify($passEnClairSaisi, $passBaseDeDonnees)
            if (password_verify($pass, $user['password'])) {
                // On créé la session "user"
                // On ne stocke JAMAIS de données dont on ne maîtrise pas le contenu
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'roles' => $user['roles']
                ];

                // On vérifie si la case est cochée
                if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                    // La case est cochée
                    // On génère un "token"
                    $token = md5(uniqid());

                    // On stocke le token dans un cookie
                    // On crée un cookie valide 1 an
                    setcookie('remember', $token, [
                        'expires' => strtotime('+1 year'),
                        'sameSite' => 'strict'
                    ]);

                    // On stocke le token dans la base
                    // On écrit la requête SQL
                    $sql = "UPDATE `users` SET `remember_token` = '$token' WHERE `id`= " . $user['id'];
                    // Cette concaténation remplace les bindValue

                    // Donc sans les bind, pas besoin de prepare et execute, juste query
                    $query = $db->query($sql);

                    header('Location: index.php');
                }
                header('Location: index.php');
            } else {
                echo 'Email et/ou mot de passe invalide';
            }
        } // Fin de si on a un user

    }  // Fin de verifform post
} // Fin isset post

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Connexion</title>
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

    <h1 class="text-center">Connexion</h1>

    <?php
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <form method="post" class="text-center">
        <div>
            <label for="mail">Email : </label>
            <input type="email" id="mail" name="mail">
        </div>
        <div>
            <label for="pass">Mot de passe : </label>
            <input type="password" id="pass" name="pass">
        </div>
        <div>
            <input type="checkbox" name="remember" id="remember">
            <label for="remember"> Rester connecté(e) </label>
        </div>
        <button class="btn btn-primary">Me connecter</button>
        <a href="oubli_pass.php">Mot de passe oublié? </a>
    </form>

    <br><hr>
    <div class="text-center">
        <p>** TIPS **</p>
        <p>Pour te connecter en tant qu'administrateur :</p>
        <p>Email : contact@demo.fr</p>
        <p>Mot de passe : demo</p>
    </div>

    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
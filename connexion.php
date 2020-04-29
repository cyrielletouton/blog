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
                    'name' => $user['name']
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
                    $sql = "UPDATE `users` SET `remember_token` = '$token' WHERE `id`= ". $user['id'] ;
                    // Cette concaténation remplace les bindValue

                    // Donc sans les bind, pas besoin de prepare et execute, juste query
                    $query = $db ->query($sql) ;


                    header('Location: index.php');
                } else {
                    echo 'Email et/ou mot de passe invalide';
                }
            } else {
                echo "Veuillez remplir tous les champs";
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
    <title>Connexion</title>
</head>

<body>

    <h1>Connexion</h1>

    <?php
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <form method="post">
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
        <button>Me connecter</button>
        <a href="oubli_pass.php">Mot de passe oublié? </a>
    </form>

</body>

</html>
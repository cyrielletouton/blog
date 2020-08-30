<?php
// Cette page doit permettre à l'utilisateur de s'inscrire sur le site
// On ne gère pas le chiffrement de mots de passe pour le moment

// Ici on traite le formulaire
// A-t-on un $_POST
if (isset($_POST) && !empty($_POST)) {
    // On vérifie si tous les champs du formulaire sont remplis
    require_once('inc/lib.php');    // Correspond a : if(isset($_POST['nom']) && !empty($_POST['nom'])){
    if (verifForm($_POST, ['name', 'mail', 'pass'])) {
        // On récupère la valeur saisie du champ
        // Pour éviter les failles XSS (Cross Site Scripting)
        // Méthode 1 : Enlever les balises html
        $name = strip_tags($_POST['name']);
        $mail = strip_tags($_POST['mail']);

        // On récupère le mot de passe et on le chiffre
        $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);

        // On se connecte à la base de données
        require_once('inc/connect.php');

        // On écrit la requête SQL
        $sql = 'INSERT INTO `users`(`email`, `password`, `name`) VALUES (:email, :password, :name);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':email', $mail, PDO::PARAM_STR);
        $query->bindValue(':password', $pass, PDO::PARAM_STR);
        $query->bindValue(':name', $name, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On déconnecte la base
        require_once('inc/close.php');

        // On redirige vers la liste des users
        header('Location: admin_users.php');
    }
} else {
    // echo "Attention formulaire incorrect";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Inscription</title>
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

    <h1 class="text-center">S'inscrire :</h1>
    <form method="post" class="text-center">
        <div>
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name">
        </div>
        <div>
            <label for="mail">Email :</label>
            <input type="email" id="mail" name="mail">
        </div>
        <div>
            <label for="pass">Mot de passe :</label>
            <input type="text" id="pass" name="pass">
        </div>
        <button class="btn btn-primary">Confirmer l'inscription</button>
    </form>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
<?php
// Cette page récupère la liste de tous les articles de la bdd

// On se connecte à la base
require_once('inc/connect.php');

// On vérifie si on a un id dans l'URL et qu'il n'est pas vide
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Si on a un id et qu'il n'est pas vide,
    // On récupère l'id
    $id = $_GET['id'];

    // On écrit la requête SQL
    $sql = 'SELECT * FROM `users` WHERE `id` = :id;';

    // Requête avec variable donc utilisation d'une requête préparée
    $query = $db->prepare($sql);

    // On injecte les valeurs dans la requête
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // on récupère les données d'1 utilisateur
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // On se déconnecte de la base
    require_once('inc/close.php');
} else {
    // si on n'a pas d'id, on revient à admin_user.php
    header('Location: admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Information de l'utilisateur</title>
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

    <div class="text-center">
        <h1>Information de l'utilisateur <?= $user['id'] ?></h1>
        <p>Nom : <?= $user['name'] ?></p>
        <p>Email : <?= $user['email'] ?></p>
        <p>Mot de passe : <?= $user['password'] ?></p>
        <p><a href="<?= $_SERVER['HTTP_REFERER'] ?>">Retour</a></p>
    </div>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
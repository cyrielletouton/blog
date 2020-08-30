<?php
// Cette page permet de modifier une localisation

// On récupère dans l'url l'id de la catégorie à modifier par l'intermédiaire de $_GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // On a un id et il n'est pas vide
    // On récupère l'id et on nettoie
    $id = strip_tags($_GET['id']);

    // On va aller chercher la catégorie dans la base
    // On se connecte
    require_once('inc/connect.php');

    // On écrit la requête
    $sql = 'SELECT * FROM `localisation` WHERE `id`= :id;';

    // On a une variable donc on utilise une requête préparée
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $localisation = $query->fetch(PDO::FETCH_ASSOC);

    // Si la localisation n'existe pas
    if (!$localisation) {
        echo 'La localisation n\'existe pas !';
        die;
    }

    // On vérifie le formulaire $_POST
    // On vérifie si nom existe et n'est pas nul
    if (isset($_POST['nom']) && !empty($_POST['nom'])) {
        // On modifie l'enregistrement dans la base

        // On récupère le nom saisi et on nettoi
        $nom = strip_tags($_POST['nom']);

        // On est déjà connectés
        // On écrit la requête SQL
        $sql = 'UPDATE `localisation` SET `name` = :nom WHERE `id` = :id;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);

        // On exécute la requête
        $query->execute();

        // On redirige vers une autre page (liste des catégories par exemple)
        header('Location: admin_localisation.php');
    }



    // On se déconnecte
    require_once('inc/close.php');
} else {
    // On n'a pas d'id
    // Message d'erreur ou redirection
    header('Location: admin_localisation.php');
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Modifier une localisation</title>
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

    <h1>Modifier une localisation</h1>
    <form method="post">
        <div>
            <label for="nom">Nom de la localisation</label>
            <input type="text" id="nom" name="nom" value="<?= $localisation['name'] ?>">
        </div>
        <button class="btn btn-primary">Modifier la localisation</button>
    </form>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
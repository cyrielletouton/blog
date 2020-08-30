<?php

// Ici on traite le formulaire
// A-t-on un $_POST
if (isset($_POST) && !empty($_POST)) {
    // On vérifie si tous les champs du formulaire sont remplis
    require_once('inc/lib.php');    // Correspond a : if(isset($_POST['nom']) && !empty($_POST['nom'])){
    if (verifForm($_POST, ['nom'])) {
        // On récupère la valeur saisie dans chaque champ
        // Pour éviter les failles XSS (Cross Site Scripting)
        // Méthode 1 : Enlever les balises html
        $nom = strip_tags($_POST['nom']);

        // Méthode 2 : désactiver les balises html
        // $nom = htmlentities($_POST['nom']);

        // On se connecte à la base de données
        require_once('inc/connect.php');

        // On écrit la requête SQL
        $sql = 'INSERT INTO `localisation`(`name`) VALUES (:nom);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On déconnecte la base
        require_once('inc/close.php');

        // On redirige vers la liste des catégories
        header('Location: admin_localisation.php');
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Ajouter une localisation</title>
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

    <h1>Ajouter une localisation</h1>
    <form method="post">
        <div>
            <label for="nom">Nom de la localisation :</label>
            <input type="text" id="nom" name="nom">
        </div>
        <button class="btn btn-primary">Ajouter la localisation</button>
    </form>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
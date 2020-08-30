<?php

session_start();
require_once('inc/retour_session.php');


// Cette page récupère la liste de tous les catégories de la bdd

// On se connecte à la base
require_once('inc/connect.php');

// On écrit la requête SQL
$sql = 'SELECT `articles`.*,
        GROUP_CONCAT(`localisation`.`name`) as localisation_name
        FROM `articles`
        LEFT JOIN `articles_localisation`
        ON `articles`.`id` = `articles_localisation`.`articles_id`
        LEFT JOIN `localisation`
        ON `articles_localisation`.`localisation_id` = `localisation`.`id`
        GROUP BY `articles`.`id`
        ORDER BY `created_at` DESC';

// Requête sans variable donc utilisation de la méthode query
$query = $db->query($sql);

// On va chercher les données dans $query
$articles = $query->fetchAll(PDO::FETCH_ASSOC);

// On se déconnecte de la base
require_once('inc/close.php');

?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Souvenirs de voyages</title>
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

    <?php include_once('inc/header.php') ?>

    <h1 class="text-center">* Souvenirs de voyages *</h1>
    <br>

    <?php
    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
        echo $_SESSION['error'];
        unset($_SESSION['error']);
    }
    ?>

    <img src="img/viaduc.jpg" class="d-block w-100" alt="viaduc">
    <br>

    <?php foreach ($articles as $article) : ?>
    <article class="text-center">
        <h2><a href="article.php?id=<?= $article['id'] ?>"><?= $article['title'] ?></a></h2>
        <p>Publié le <?= date('d/m/Y à H:i:s', strtotime($article['created_at'])) ?>
            dans
            <?php
                // A ENLEVER PLUS TARD CAR CHOISIR QUE FRANCE OU QUE EUROPE !!!!
                $localisations = explode(',', $article['localisation_name']);
                foreach ($localisations as $localisation) {
                    echo '<a href=#>' . $localisation . ' </a>';
                }
                ?>
        </p>
        <?php
            // On vérifie si l'article a une image
            if ($article['featured_image'] != null) :
                // On a une image, on la traite et on l'affiche
                // On sépare le nom et l'extension
                $nom = pathinfo($article['featured_image'], PATHINFO_FILENAME);
                $extension = pathinfo($article['featured_image'], PATHINFO_EXTENSION);

                // On crée le nom de l'image à afficher
                $image =  $nom . '-150x150.' . $extension;

                // On affiche l'image
                ?>
        <img src="uploads/<?= $image ?>" alt="<?= $article['title'] ?>">

        <?php endif; ?>
        <hr>
    </article>
    <?php endforeach; ?>

    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>

<?php

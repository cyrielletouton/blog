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
    <title>Souvenirs de voyages</title>
</head>

<body>

    <?php include_once('inc/header.php') ?>

    <h1>Souvenirs de voyages</h1>

    <?php
    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
        echo $_SESSION['error'];
        unset($_SESSION['error']);
    }
    ?>

    <?php foreach ($articles as $article) : ?>
        <article>
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
            if($article['featured_image'] != null):
                // On a une image, on la traite et on l'affiche
                // On sépare le nom et l'extension
                $nom = pathinfo($article['featured_image'], PATHINFO_FILENAME);
                $extension = pathinfo($article['featured_image'], PATHINFO_EXTENSION);
                
                // On crée le nom de l'image à afficher
                $image =  $nom .'-150x150.'.$extension;

                // On affiche l'image
                ?>
                <img src="uploads/<?= $image ?>" alt="<?= $article['title']?>">

                <?php
            endif;
            ?>
            <div><?= substr(strip_tags($article['content']), 0, 300) . '...' ?></div>
        </article>
    <?php endforeach; ?>

</body>

</html>

<?php


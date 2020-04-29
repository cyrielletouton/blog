<?php

session_start();
require_once('inc/retour_session.php');


// Cette page récupère la liste de tous les catégories de la bdd

// On se connecte à la base
require_once('inc/connect.php');

// On écrit la requête SQL
$sql = 'SELECT `articles`.*,
        GROUP_CONCAT(`categories`.`name`) as category_name
        FROM `articles`
        LEFT JOIN `articles_categories`
        ON `articles`.`id` = `articles_categories`.`articles_id`
        LEFT JOIN `categories`
        ON `articles_categories`.`categories_id` = `categories`.`id`
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
    <title>Liste des articles</title>
</head>

<body>

    <?php include_once('inc/header.php') ?>

    <h1>Liste des articles</h1>
    
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
                // si je recpos "Sports,Actualités"
                $categories = explode(',', $article['category_name']);
                // Après explode j'ai [ 0 => 'Sports', 1 => 'Actualités']
                foreach ($categories as $categorie) {
                    echo '<a href=#>' . $categorie . ' </a>';
                }
                ?>
            </p>
            <div><?= substr(strip_tags($article['content']), 0, 300) . '...' ?></div>
        </article>
    <?php endforeach; ?>

</body>

</html>

<?php
// substr(strip_tags($article['content']), O, 300)
// On enlève les balises HTML
// $contenuSansHtml = strip_tags($article['content']);

// On raccourcit à 300 caractères
// $contenuRaccourci = substr($contenuSansHtml, 0, 300);

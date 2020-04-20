<?php
// Cette page récupère un article

// On se connecte à la base
require_once('inc/connect.php') ;

// On vérifie si on a un id dans l'URL et qu'il n'est pas vide
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Si on a un id et qu'il n'est pas vide,
    // On récupère l'id
    $id = $_GET['id'];

    // On écrit la requête SQL
$sql = 'SELECT * FROM `articles` WHERE `id` = :id;';

// Requête avec variable donc utilisation d'une requête préparée
$query = $db->prepare($sql) ;

// On injecte les valeurs dans la requête
$query->bindValue(':id', $id, PDO::PARAM_INT);

// On exécute la requête
$query->execute();

// on récupère les données d'1 article
$article = $query->fetch(PDO::FETCH_ASSOC);

// On se déconnecte de la base
require_once ('inc/close.php');

} else{
    // si on n'a pas d'id, on revient à admin_user.php
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article</title>
</head>
<body>

    <article>
        <h2><?= $article['title'] ?></h2>
        <p>Publié le <?= date('d/m/Y à H:i:s', strtotime($article['created_at'])) ?></p>
        <div><?= $article['content'] ?></div>
    </article>
    <p><a href="<?= $_SERVER['HTTP_REFERER'] ?>">Retour</a></p>    

</body>
</html>
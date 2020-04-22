<?php
// Cette page affiche un article

// On vérifie si on a un id dans l'URL et qu'il n'est pas vide
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Si on a un id et qu'il n'est pas vide,
    // On récupère l'id
    $id = $_GET['id'];

    
    // On se connecte à la base
    require_once('inc/connect.php') ;

    // On écrit la requête SQL
    $sql = 'SELECT `articles`.*,
        GROUP_CONCAT(`categories`.`name`) as category_name
        FROM `articles`
        LEFT JOIN `articles_categories`
        ON `articles`.`id` = `articles_categories`.`articles_id`
        LEFT JOIN `categories`
        ON `articles_categories`.`categories_id` = `categories`.`id`
        WHERE `articles`.`id`= :id
        GROUP BY `articles`.`id`';

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

     // Si l'article n'existe pas
     if(!$article){ // ($artticle == false)
        echo "L'article n'existe pas";
        die ;
    }

} else{
    // si on n'a pas d'id, on redirige vers index.php
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article['title']?></title>
</head>
<body>

    <article>
        <h1><?= $article['title'] ?></h1>
        <p>Publié le <?= date('d/m/Y à H:i:s', strtotime($article['created_at'])) ?>
        dans
                <?php
                    // si je recpos "Sports,Actualités"
                    $categories = explode(',', $article['category_name']);
                    // Après explode j'ai [ 0 => 'Sports', 1 => 'Actualités']
                    foreach ($categories as $categorie){
                        echo '<a href=#>' . $categorie . ' </a>';
                    }
                ?>    
        </p>
        <div><?= $article['content'] ?></div>
    </article>
    <p><a href="<?= $_SERVER['HTTP_REFERER'] ?>">Retour</a></p>    

</body>
</html>
<?php
// Aller chercher la liste de toutes les catégories par ordre alphabétique
// Nécessaire AVANT de pouvoir afficher le formulaire
// On se connecte à la base
require_once('inc/connect.php') ;

    // On écrit la requête SQL
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Pas de variables, donc utilisation de la méthode query
$query = $db->query($sql) ;

// on récupère les données
$categories = $query->fetchAll(PDO::FETCH_ASSOC);


// Traiter le formulaire -> Ajouter l'article dans la base et lui attribuer les bonnes catégories


// On se déconnecte de la base
require_once ('inc/close.php');


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>
</head>
<body>
    <h1>Ajouter un article</h1>
    <form method="post">
        <div>
            <label for="titre">Titre de l'article : </label>
            <input type="text" id="titre" name="titre">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu" cols="30" rows="10"></textarea>
        </div>
        <h2>Catégories</h2>
        <?php foreach($categories as $categorie): ?>
            <div>
                <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>">
                <label for="cat_<?= $categorie['id'] ?>"> <?= $categorie['name'] ?> </label>
            </div>
        <?php endforeach; ?>
        <button>Ajouter l'article</button>
    </form>
    
</body>
</html>
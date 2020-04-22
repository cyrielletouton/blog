<?php
// Cette page permet de modifier une catégorie

// On récupère dans l'url l'id de la catégorie à modifier par l'intermédiaire de $_GET
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On a un id et il n'est pas vide
    // On récupère l'id et on nettoie
    $id = strip_tags($_GET['id']);

    // On va aller chercher la catégorie dans la base
    // On se connecte
    require_once('inc/connect.php');

    // On écrit la requête
    $sql = 'SELECT * FROM `categories` WHERE `id`= :id;';

    // On a une variable donc on utilise une requête préparée
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $categorie = $query->fetch(PDO::FETCH_ASSOC);

    // Si la catégorie n'existe pas
    if(!$categorie){
        echo 'La catégorie n\'existe pas !';
        die;
    }

    // On vérifie le formulaire $_POST
    // On vérifie si nom existe et n'est pas nul
    if(isset($_POST['nom']) && !empty($_POST['nom'])){
        // On modifie l'enregistrement dans la base

        // On récupère le nom saisi et on nettoi
        $nom = strip_tags($_POST['nom']);

        // On est déjà connectés
        // On écrit la requête SQL
        $sql = 'UPDATE `categories` SET `name` = :cequonveut WHERE `id` = :id;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':cequonveut', $nom, PDO::PARAM_STR) ;
        $query->bindValue(':id', $id, PDO::PARAM_INT) ;

        // On exécute la requête
        $query->execute();

        // On redirige vers une autre page (liste des catégories par exemple)
        header('Location: admin_categories.php');

    } 


    
    // On se déconnecte
    require_once('inc/close.php');

} else {
    // On n'a pas d'id
    // Message d'erreur ou redirection
    header('Location: admin_categories.php');
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une catégorie</title>
</head>
<body>
    <h1>Modifier une catégorie</h1>
    <form method="post">
        <div>
            <label for="nom">Nom de la catégorie</label>
            <input type="text" id="nom" name="nom" value="<?= $categorie['name'] ?>">
        </div>
        <button>Modifier la catégorie</button>
    </form>
    
</body>
</html>
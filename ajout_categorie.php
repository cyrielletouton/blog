<?php

// Ici on traite le formulaire
// A-t-on un $_POST
if (isset($_POST) && !empty($_POST)){
    // On vérifie si tous les champs du formulaire sont remplis
    require_once('inc/lib.php');    // Correspond a : if(isset($_POST['nom']) && !empty($_POST['nom'])){
    if(verifForm($_POST, ['nom'])){    
        // On récupère la valeur saisie dans chaque champ
        // Pour éviter les failles XSS (Cross Site Scripting)
        // Méthode 1 : Enlever les balises html
        $nom = strip_tags($_POST['nom']);

        // Méthode 2 : désactiver les balises html
        // $nom = htmlentities($_POST['nom']);

        // On se connecte à la base de données
        require_once('inc/connect.php');

        // On écrit la requête SQL
        $sql = 'INSERT INTO `categories`(`name`) VALUES (:nom);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':nom', $nom, PDO::PARAM_STR) ;

        // On exécute la requête
        $query->execute();

        // On déconnecte la base
        require_once('inc/close.php');

        // On redirige vers la liste des catégories
        header('Location: admin_categories.php');
        }
    } else {
        echo "Attention il faut entrer un nom";
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie</title>
</head>
<body>
    <h1>Ajouter une catégorie</h1>
    <form method="post">
        <div>
            <label for="nom">Nom de la catégorie :</label>
            <input type="text" id="nom" name="nom">
        </div>
        <button>Ajouter la catégorie</button>
    </form>
    
</body>
</html>
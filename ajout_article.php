<?php

// Aller chercher la liste de toutes les catégories par ordre alphabétique
// Nécessaire AVANT de pouvoir afficher le formulaire
// On se connecte à la base
require_once('inc/connect.php');

// On écrit la requête SQL
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Pas de variables, donc utilisation de la méthode query
$query = $db->query($sql);

// on récupère les données
$categories = $query->fetchAll(PDO::FETCH_ASSOC);


// Traiter le formulaire -> Ajouter l'article dans la base et lui attribuer les bonnes catégories
// 1ère partie du traitement, créer l'article
// 2ème partie du traitement, lui affecter la/les catégorie(s)

// On vérifie que le formulaire a été envoyé
if (isset($_POST) && !empty($_POST)) {
    // On a un formulaire envoyé
    // On vérifie que tout est bien rempli
    require_once('inc/lib.php');
    if (verifForm($_POST, ['titre', 'contenu', 'categories'])) {
        // Le formulaire est complet, on peut créer l'article
        // On récupère les valeurs et on nettoie
        $titre = strip_tags($_POST['titre']);
        $contenu = strip_tags($_POST['contenu'], '<div><p><h1><h2><img><strong>');

        // On vérifie si on a une image
        if (isset($_FILES) && !empty($_FILES)) {
            // On vérifie que tous les fichiers attendus sont envoyés
            // Erreur 4 = pas de fichier (image non obligatoire)
            if (isset($_FILES['image']) && !empty($_FILES['image']) && $_FILES['image']['error'] != 4) {
                // On récupère les données
                $image = $_FILES['image'];

                // On vérifie si le transfert s'est mal déroulé 'error' = 1
                if ($image['error'] != 0) {
                    echo 'Une erreur s\'est produite';
                    die;
                }

                // On limite aux images png et jpg (jpeg aussi)
                $types = ['image/png', 'image/jpeg'];

                // On vérifie si le type du fichier est absent de la liste
                if (!in_array($image['type'], $types)) {
                    echo "Le type de fichier doit être une image jpg ou png";
                    die;
                }

                // On veut limiter la taille à 1Mo max
                if ($image['size'] > 1048576) {
                    echo 'Le fichier est trop volumineux (1Mo maxi)';
                    die;
                }

                // Le transfert s'est bien déroulé, on déplace l'image temporaire après lui avoir généré un nouveau nom
                // Générer un nom pour le fichier -> nom + extension
                // On récupère l'extension de notre fichier
                $extension = pathinfo($image['name'], PATHINFO_EXTENSION);

                // On génère un nom  "aléatoire"
                $nom = md5(uniqid()) . '.' . $extension;

                // On génère le nom complet -> nom et chemin complet vers le dossier de destination
                $nomComplet = __DIR__ . '/uploads/' . $nom;

                // On déplace le fichier
                if (!move_uploaded_file($image['tmp_name'], $nomComplet)) {
                    echo "Le fichier n'a pas été copié !";
                    die;
                }

                // On crée les différentes versions de l'image
                // Lors de la création, l'image est copiée et redimensionnée en :
                // - Miniature carrée de 300px : nom-300x300.ext
                // - Image réduite à 75% de la taille originale : nom-75.ext

                // Appel de la fonction thumb dans lib.php
                thumb(300, $nom);
                resizeImage($nom, 75);
               
            }
        }

        // On est déjà connectés
        // On écrit la requête SQL
        $sql = 'INSERT INTO `articles` (`title`, `content`, `featured_image`, `users_id`) VALUES (:titre, :contenu, :image, :userid);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':userid', 1, PDO::PARAM_INT);
        $query->bindValue(':image', $nom, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();


        // On récupère l'id de l'article nouvellement créé
        $idArticle = $db->lastInsertId();

        // On récupère dans le $_POST les catégories cochées
        $categories = $_POST['categories'];

        // On ajoute les catégories
        foreach ($categories as $categorie) {
            // On écrit la requête
            $sql = 'INSERT INTO `articles_categories`(`articles_id`, `categories_id`) VALUES (:idArticle, :idCategorie);';

            // On prépare la requête
            $query = $db->prepare($sql);

            // On injecte les valeurs
            $query->bindValue(':idArticle', $idArticle, PDO::PARAM_INT);
            $query->bindValue(':idCategorie', strip_tags($categorie), PDO::PARAM_INT);

            // On exécute la requête
            $query->execute();
        }

        // On redirige vers une autre page (liste des catégories par exemple)
        header('Location: index.php');

        // On se déconnecte de la base
        require_once('inc/close.php');
    } else {
        echo "Le formulaire doit être rempli complètement";
    }
}


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
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="titre">Titre de l'article : </label>
            <input type="text" id="titre" name="titre">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu" cols="30" rows="10"></textarea>
        </div>
        <h2>Image</h2>
        <!-- ATTENTION enctype obligatoire quand un champ "file" est utilisé -->
        <div>
            <label for="image">Image : </label>
            <input type="file" name="image" id="image">
        </div>
        <h2>Catégories</h2>
        <?php foreach ($categories as $categorie) : ?>
            <div>
                <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>">
                <label for="cat_<?= $categorie['id'] ?>"> <?= $categorie['name'] ?> </label>
            </div>
        <?php endforeach; ?>
        <button>Ajouter l'article</button>
    </form>

</body>

</html>
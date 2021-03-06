<?php

session_start();

// Aller chercher la liste de toutes les localisations par ordre alphabétique
// Nécessaire AVANT de pouvoir afficher le formulaire
// On se connecte à la base
require_once('inc/connect.php');

// On écrit la requête SQL
$sql = 'SELECT * FROM `localisation` ORDER BY `name` ASC;';

// Pas de variables, donc utilisation de la méthode query
$query = $db->query($sql);

// on récupère les données
$localisations = $query->fetchAll(PDO::FETCH_ASSOC);


// Traiter le formulaire -> Ajouter l'article dans la base et lui attribuer la bonne localisation
// 1ère partie du traitement, créer l'article
// 2ème partie du traitement, lui affecter la localisation

// On vérifie que le formulaire a été envoyé
if (isset($_POST) && !empty($_POST)) {
    // On a un formulaire envoyé
    // On vérifie que tout est bien rempli
    require_once('inc/lib.php');
    if (verifForm($_POST, ['titre', 'contenu', 'localisation'])) {
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
                    $_SESSION['error'] = 'Le fichier doit être une image png ou jpg';
                    header('Location: ajout_article.php');
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
                thumb(150, $nom);
                resizeImage($nom, 75);
            }
        }

        // On est déjà connectés
        // On écrit la requête SQL
        $sql = 'INSERT INTO `articles` (`title`, `featured_image`, `content`, `users_id`) VALUES (:titre, :image, :contenu, :userid);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans la requête
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':image', $nom, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':userid', 1, PDO::PARAM_INT);

        // On exécute la requête
        $query->execute();


        // On récupère l'id de l'article nouvellement créé
        $idArticle = $db->lastInsertId();

        // On récupère dans le $_POST les catégories cochées
        $localisations = $_POST['localisations'];

        // On ajoute la localisation
        foreach ($localisations as $localisation) {
            // On écrit la requête
            $sql = 'INSERT INTO `articles_localisation`(`articles_id`, `localisation_id`) VALUES (:idArticle, :idLocalisation);';

            // On prépare la requête
            $query = $db->prepare($sql);

            // On injecte les valeurs
            $query->bindValue(':idArticle', $idArticle, PDO::PARAM_INT);
            $query->bindValue(':idLocalisation', strip_tags($localisation), PDO::PARAM_INT);

            // On exécute la requête
            $query->execute();
        }

        $_SESSION['message'] = 'Article ajouté avec succès sous le numéro ' . $idArticle;
        // On redirige 
        header('Location: admin_articles.php');
    } else {
        echo "Le formulaire doit être rempli complètement";
    }
}

// On se déconnecte de la base
require_once('inc/close.php');

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Ajouter un article</title>
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

    <h1>Ajouter un article</h1>

    <?php
    // Y a-t-il un message d'erreur ?
    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
        echo  $_SESSION['error'];
        unset($_SESSION['error']);
    }
    ?>

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
        <h2>Localisations</h2>
        <?php foreach ($localisations as $localisation) : ?>
        <div>
            <input type="checkbox" name="localisations[]" id="loc_<?= $localisation['id'] ?>" value="<?= $localisation['id'] ?>">
            <label for="loc_<?= $localisation['id'] ?>"> <?= $localisation['name'] ?> </label>
        </div>
        <?php endforeach; ?>
        <button class="btn btn-primary">Ajouter l'article</button>
    </form>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
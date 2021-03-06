<?php
// Aller chercher la liste de toutes les localisations par ordre alphabétique
// On se connecte à la base
require_once('inc/connect.php');
// On écrit la requête SQL
$sql = 'SELECT * FROM `localisation` ORDER BY `name` ASC;';
// Pas de variables, donc utilisation de la méthode query
$query = $db->query($sql);
// on récupère les données
$localisations = $query->fetchAll(PDO::FETCH_ASSOC);

// On récupère le contenu de l'article passé dans l'URL
// On vérifie si un id est passé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Un id est donné
    // On récupère l'id et on le nettoie
    $id = strip_tags($_GET['id']);
    // On écrit la requête
    $sql = 'SELECT * FROM `articles` WHERE `id`=:id;';
    // On prépare la requête
    $query = $db->prepare($sql);
    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    // on exécute
    $query->execute();
    // On récupère les données de l'article
    $article = $query->fetch(PDO::FETCH_ASSOC);

    // On vérifie si l'article existe ou non
    if (!$article) {
        // Pas d'article, on redirige
        header('Location: admin_articles.php');
    }

    // L'article existe, on va chercher la localisation dans laquelle il se trouve
    // On écrit la requête
    $sql = 'SELECT * FROM `articles_localisation` WHERE `articles_id`= :id;';
    // On prépare la requête
    $query = $db->prepare($sql);
    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    // on exécute
    $query->execute();
    $localisationsArticle = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Pas d'id
    header('Location: admin_articles.php');
}


// Traiter le formulaire -> Modifier l'article dans la base et lui attribuer la bonne localisation
// 1ère partie du traitement, mettre à jour l'article
// 2ème partie du traitement, lui affecter la localisation
// On vérifie que le formulaire a été envoyé
if (isset($_POST) && !empty($_POST)) {
    // On a un formulaire envoyé
    // On vérifie que tout est bien rempli
    require_once('inc/lib.php');
    if (verifForm($_POST, ['titre', 'contenu', 'localisations'])) {
        // Le formulaire est complet, on peut modifier l'article
        // On récupère les valeurs et on nettoie
        $titre = strip_tags($_POST['titre']);
        $contenu = strip_tags($_POST['contenu'], '<div><p><h1><h2><img><strong>');
        // On récupère le nom de l'image dans la base de données
        $nom = $article['featured_image'];
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
                thumb(150, $nom);
                thumb(300, $nom);
                resizeImage($nom, 75);

                if ($article['featured_image'] != null) {
                    // On gère la suppression des anciennes images
                    // On récupère la 1ère partie du nom de fichier de l'ancienne image (avant l'extension)
                    $debutNom = pathinfo($article['featured_image'], PATHINFO_FILENAME);

                    // On récupère la liste des fichiers dans le dossier uploads
                    $fichiers = scandir(__DIR__ . '/uploads/');

                    // On boucle sur les fichiers
                    foreach ($fichiers as $fichier) {
                        // Si le nom du fichier commence par $debutNom alors on le supprime
                        if (strpos($fichier, $debutNom) === 0) {
                            // On supprime le fichier
                            unlink(__DIR__ . '/uploads/' . $fichier);
                        }
                    }
                } // Fin if image !=null

            } // Fin isset files image

        } // Fin isset files

    } // Fin du if verifForm

    // On récupère dans l'url l'id de l'article à modifier par l'intermédiaire de $_GET
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // On a un id et il n'est pas vide
        // On récupère l'id et on nettoie
        $id = strip_tags($_GET['id']);

        // On écrit la requête SQL
        $sql = 'UPDATE `articles` SET `title`= :titre, `content`= :contenu, `featured_image`= :image WHERE `id` = :id ;';
        // On prépare la requête
        $query = $db->prepare($sql);
        // On injecte les valeurs dans la requête
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':image', $nom, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        // On exécute la requête
        $query->execute();
        // Fin mise à jour article

        // On efface toutes les lignes correspondantes à l'article dans la table articles_categories
        // on écrit la requête
        $sql = 'DELETE FROM `articles_localisation` WHERE `articles_id` = :id;';
        // On prépare la requête
        $query = $db->prepare($sql);
        // On injecte les valeurs dans la requête
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        // On exécute la requête
        $query->execute();

        // On récupère dans le $_POST les localisations cochées
        $localisations = $_POST['localisations'];
        // On ajoute les localisations
        foreach ($localisations as $localisation) {
            // On écrit la requête
            $sql = 'INSERT INTO `articles_localisation`(`articles_id`, `localisation_id`) VALUES (:idArticle, :idLocalisation);';
            // On prépare la requête
            $query = $db->prepare($sql);
            // On injecte les valeurs
            $query->bindValue(':idArticle', $id, PDO::PARAM_INT);
            $query->bindValue(':idLocalisation', strip_tags($localisation), PDO::PARAM_INT);
            // On exécute la requête
            $query->execute();
        }


        // On redirige vers une autre page
        header('Location: admin_articles.php');
    } // Fin isset GET id


    // On se déconnecte de la base
    require_once('inc/close.php');
} // Fin isset POST



?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Modifier un article</title>
</head>

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

<body>
    <h1>Modifier un article</h1>
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="titre">Titre de l'article : </label>
            <input type="text" id="titre" name="titre" value="<?= $article['title'] ?>">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu" cols="30" rows="10"><?= $article['content'] ?></textarea>
        </div>
        <h2>Image</h2>
        <!-- ATTENTION enctype obligatoire quand un champ "file" est utilisé -->
        <div>
            <label for="image">Image : </label>
            <input type="file" name="image" id="image">
        </div>
        <h2>Localisation</h2>
        <?php
        foreach ($localisations as $localisation) :
            // On va vérifier si la localisation qu'on affiche doit être cochée
            $checked = '';
            foreach ($localisationsArticle as $loc) {
                if ($loc['localisation_id'] == $localisation['id']) {
                    $checked = 'checked';
                }
            }
            ?>
        <div>
            <input type="checkbox" name="localisations[]" id="loc_<?= $localisation['id'] ?>" value="<?= $localisation['id'] ?>" <?= $checked ?>>
            <label for="loc_<?= $localisation['id'] ?>"> <?= $localisation['name'] ?> </label>
        </div>
        <?php endforeach; ?>
        <button class="btn btn-primary">Modifier l'article</button>
    </form>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
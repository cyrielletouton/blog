<?php
session_start();

// Ce fichier sert à supprimer 1 article
// - Vérifier que l'article existe
// - Supprimer les images (physiques)
// - Supprimer l'association aux catégories
// - Supprimer l'article de la base

// On récupère dans l'url l'id de l'article à supprimer par l'intermédiaire de $_GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // On récupère l'id envoyé et on nettoie
    $id = strip_tags($_GET['id']);

    // On se connecte à la base
    require_once('inc/connect.php');

    // On écrit la requête pour aller chercher l'article
    $sql = 'SELECT * FROM `articles` WHERE `id`= :id ;';

    // On prépare la requête 
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère l'article
    $article = $query->fetch(PDO::FETCH_ASSOC);

    // On redirige si l'article n'existe pas
    header('Location: admin_articles.php');



    // L'article existe
    // On supprime les images correspondantes  (s'il y en a)
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


    // Supprimer l'association aux catégories
    // On écrit la requête
    $sql = 'DELETE FROM `articles_localisation` WHERE `articles_id` = :id;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute
    $query->execute();


    // Supprimer l'article
    // On écrit la requête
    $sql = 'DELETE FROM `articles` WHERE `id` = :id;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute
    $query->execute();
    // Fin suppression article

    // On déconnecte
    require_once('inc/close.php');

    // On écrit un message qui va confirmer la suppression 
    $_SESSION['message'] = 'Article supprimé avec succès sous le numéro ' . $id;

    // On redirige ou on affiche un message
    header('Location: admin_articles.php');
} else {
    // Pas d'id
    // On redirige le visiteur
    header('Location: admin_articles.php');
}

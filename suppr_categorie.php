<?php
// Ce fichier sert à supprimer 1 catégorie

// On récupère dans l'url l'id de la catégorie à supprimer par l'intermédiaire de $_GET
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On récupère l'id envoyé et on nettoie
    $id = strip_tags($_GET['id']);

    // On se connecte à la base
    require_once('inc/connect.php');

    // On écrit la requête
    $sql = 'DELETE FROM `categories` WHERE `id` = :id;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute
    $query->execute();

    // On déconnecte
    require_once('inc/close.php');

    // On redirige ou on affiche un message
    header('Location: admin_categories.php');

} else {
    // Pas d'id
    // On redirige le visiteur
}
<?php
session_start();
require_once 'inc/lib.php';
// Cette page récupère la liste de tous les catégories de la bdd

// VERIFICATION PERMISSIONS D'ACCES
// On vérifie qu'on a une session user
if (verifForm($_SESSION, ['user'])) {
    // L'utilisateur est connecté
    // On vérifie s'il est admin
    // On transforme les rôles en tableau PHP
    $roles = json_decode($_SESSION['user']['roles']);

    // Vérifier si $roles contient "ROLE_ADMIN", plus précisément s'il ne le contient pas
    if (!in_array("ROLE_ADMIN", $roles)) {
        // L'utilisateur est connecté mais pas Admin
        // Erreur 404 (Ici une 403 serait plus appropriée)
        // On envoie un code réponse 404
        http_response_code(404);

        // On génère le contenu
        include('errors/404.php');

        // On sort "proprement"
        exit;
    } else {
        // L'utilisateur est l'Admin
    }
} else {
    // L'utilisateur n'est pas connecté
    // Erreur 403, mais en attendant :
    // On envoie un code réponse 403
    http_response_code(403);

    // On génère le contenu
    include('errors/403.php');

    // On sort proprement
    exit;
}
// FIN VERIFICATION PERMISSIONS D'ACCES

// On se connecte à la base
require_once('inc/connect.php');

// On écrit la requête SQL
$sql = 'SELECT * FROM `localisation` ORDER BY `name` ASC;';

// Requête sans variable donc utilisation de la méthode query
$query = $db->query($sql);

// On va chercher les données dans $query
$localisations = $query->fetchAll(PDO::FETCH_ASSOC);

// On se déconnecte de la base
require_once('inc/close.php');

?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.0/journal/bootstrap.min.css" rel="stylesheet" integrity="sha384-vjBZc/DqIqR687k5rf6bUQ6IVSOxQUi9TcwtvULstA7+YGi//g3oT2qkh8W1Drx9" crossorigin="anonymous">
    <title>Liste des localisations</title>
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

    <h1>Liste des localisations</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </thead>
        <tbody>
            <?php foreach ($localisations as $localisation) : ?>
            <tr>
                <td><?= $localisation['id'] ?></td>
                <td><?= $localisation['name'] ?></td>
                <td><a href="modif_localisation.php?id=<?= $localisation['id'] ?>">Modifier</a>
                    <a href="suppr_localisation.php?id=<?= $localisation['id'] ?>">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="ajout_localisation.php">Ajouter une localisation</a>
    <br>
    <footer style="background-color:#EB6864">
        <p style="color:white" class="text-center"> Copyright © 2020 - Cyrielle.T - Juste un test en PHP ;)</p>
    </footer>

</body>

</html>
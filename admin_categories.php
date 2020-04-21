<?php
// Cette page récupère la liste de tous les catégories de la bdd

// On se connecte à la base
require_once('inc/connect.php') ;

// On écrit la requête SQL
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Requête sans variable donc utilisation de la méthode query
$query = $db->query($sql) ;

// On va chercher les données dans $query
$categories = $query->fetchAll(PDO::FETCH_ASSOC);

// On se déconnecte de la base
require_once ('inc/close.php');

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des catégories</title>
</head>
<body>

    <h1>Liste des catégories</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </thead>
        <tbody>
        <?php foreach ($categories as $categorie): ?>
            <tr>
                <td><?= $categorie['id'] ?></td>
                <td><?= $categorie['name'] ?></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="ajout_categorie.php">Ajouter une catégorie</a>

</body>
</html>
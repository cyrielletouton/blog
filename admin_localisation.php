<?php

// On se connecte à la base
require_once('inc/connect.php') ;

// On écrit la requête SQL
$sql = 'SELECT * FROM `localisation` ORDER BY `name` ASC;';

// Requête sans variable donc utilisation de la méthode query
$query = $db->query($sql) ;

// On va chercher les données dans $query
$localisations = $query->fetchAll(PDO::FETCH_ASSOC);

// On se déconnecte de la base
require_once ('inc/close.php');

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des localisations</title>
</head>
<body>

    <h1>Liste des localisations</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </thead>
        <tbody>
        <?php foreach ($localisations as $localisation): ?>
            <tr>
                <td><?= $localisation['id'] ?></td>
                <td><?= $localisation['name'] ?></td>
                <td><a href="modif_localisation.php?id=<?=$localisation['id']?>">Modifier</a>
                    <a href="suppr_localisation.php?id=<?=$localisation['id']?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="ajout_localisation.php">Ajouter une localisation</a>

</body>
</html>
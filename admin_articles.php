<?php
session_start();
require_once 'inc/lib.php';
require_once 'inc/connect.php';
$sql = 'SELECT * FROM articles ORDER BY created_at ASC';

$query = $db->prepare($sql);

$query->execute();

$articles = $query->fetchAll(PDO::FETCH_ASSOC);

require_once 'inc/close.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Articles</title>
</head>
<body>
    <h1>Liste des articles</h1>

    <?php
    // Y a-t-il un message ?
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        ?>
        <div style="color:green; font-weight:bold"><?= $_SESSION['message'] ?></div>
        <?php
        unset($_SESSION['message']);
    }
    ?>

    <table>
        <thead>
            <th>ID</th>
            <th>Titre</th>
            <th>Actions</th>
        </thead>
        <tbody>
            <?php foreach($articles as $article): ?>
                <tr>
                    <td><?= $article['id'] ?></td>
                    <td><?= $article['title'] ?></td>
                    <td><a href="modif_article.php?id=<?= $article['id'] ?>">Modifier</a> <a href="suppr_article.php?id=<?= $article['id'] ?>">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="ajout_article.php">Ajouter</a>
</body>
</html>
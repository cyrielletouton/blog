<?php
// Formulaire de connexion
// Doit afficher en haut de page "Vous êtes connecté(e)" si le mail et le mot de passe sont bons
// Doit afficher en haut de page "Email et/ou mot de passe invalide" si le mail et le mot de passe ne sont pas bons

// On vérifie que $_POST existe et qu'il n'est pas vide
if(isset($_POST) && !empty($_POST)){
    // On vérifie que tous les champs sont remplis
    require_once('inc/lib.php');
    if(verifForm($_POST, ['mail', 'pass'])){
        // On récupère les valeurs saisies
        $mail = strip_tags($_POST['mail']);
        $pass = $_POST['pass'];

        // On vérifie si l'email existe dans la base de données
        // On se connecte à la base
        require_once('inc/connect.php');

        // On écrit la requête
        $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs
        $query->bindValue(':email', $mail, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On récupère les données
        $user = $query->fetch(PDO::FETCH_ASSOC);

        // Soit on a une réponse dans $user, soit non
        if(!$user){
            echo 'Email et/ou mot de passe invalide';
        } else {
            // On vérifie que le mot de passe saisi correpond à celui en base
            // password_verify($passEnClairSaisi, $passBaseDeDonnees)
            if(password_verify($pass, $user['password'])){
                echo "Vous êtes connecté(e)";
            } else {
                echo 'Email et/ou mot de passe invalide';
            }
        }
    
    }else {
        echo "Veuillez remplir tous les champs";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>

    <h1>Connexion</h1>
    <form method="post">
        <div>
            <label for="mail">Email : </label>
            <input type="email" id="mail" name="mail">
        </div>
        <div>
            <label for="pass">Mot de passe : </label>
            <input type="password" id="pass" name="pass">
        </div>
        <button>Me connecter</button>
    </form>
    
</body>
</html>
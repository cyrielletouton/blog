<?php
// On restaure la session si besoin

// Au retour après fermeture du navigateur
// Si un cookie "remember" existe :
// - On cherche si un utilisateur a le token correspondant
// - Si oui, on restaure la session avec les données de cet utilisateur



// On vérifie si un cookie "remember" existe
if(isset($_COOKIE['remember']) && !empty($_COOKIE['remember'])){
    // On cherche un utilisateur avec le token du cookie
    // On se connecte à la base
    require_once('connect.php');
    
    // On écrit la requête SQL
    $sql = 'SELECT * FROM `users` WHERE `remember_token` = :token ;' ;

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs
    $query->bindValue(':token', $_COOKIE['remember'], PDO::PARAM_STR);

    // On exécute la requête
    $query->execute();

    // On récupère l'utilisateur
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if($user){
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];
    }
}
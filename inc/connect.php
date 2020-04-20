<?php
try{
    // On définit une constante pour DB_USER
    // define('DB_USER', 'root')

    // Connexion à la base de données
    $db = new PDO('mysql:host=localhost;dbname=blog', 'root', '');

    // On force les échanges en UTF8
    $db->exec('SET NAMES "UTF8"');

} catch(PDOException $e){
    // En cas de problème on émet un message d'erreur
    echo 'Erreur : ' .$e->getMessage();
    die;
}
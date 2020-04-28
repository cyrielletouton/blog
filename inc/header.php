<?php
// Cette page contiendra la barre supérieure de notre site
// Nous aurons 2 affichages
// - Soit Connexion et Inscription
// - Soit Bonjour name et Déconnexion

// On vérifie si l'utilisateur est connecté $_SESSION['user'] existe et n'es pas vide
// Pas de session_start (car code html dejà injecté avant car on l'inclut)
if (isset($_SESSION['user']) && !empty($_SESSION['user'])){
    // Ici, l'utilisateur est connecté
    ?>
    <p>Bonjour <?= $_SESSION['user']['name']?> <a href="deconnexion.php">Déconnexion</a></p>
    <?php
} else {
    // Ici l'utilisateur n'est pas connecté
    ?>
    <p><a href="connexion.php">Connexion</a> <a href="inscription.php">Inscription</a></p>

<?php
}
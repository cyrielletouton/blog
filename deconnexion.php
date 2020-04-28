<?php
// On déconnecte la session en effacant totalement $_SESSION
// session_start();
// session_destroy();

// On ne fait que déconnecter l'utilisateur
session_start();
unset($_SESSION['user']);

// On efface l'éventuel cookie 'remember'
setcookie('remember', '', 1);

header('Location: ' .$_SERVER['HTTP_REFERER']);

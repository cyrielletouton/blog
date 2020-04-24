<?php

/**
 * Fonction universelle de vérification de formulaire
 *
 * @param array $superglobale Variable $_GET ou $_POST
 * @param array $champs Tableau des champs à vérifier
 * @return bool
 */
function verifForm($superglobale, $champs)
{
    //     // Fonction universelle de vérification de formulaire
    // Boucler sur "champs"
    foreach ($champs as $champ) {
        // Vérifier si le champ existe et si le champ n'est pas vide
        if (isset($superglobale[$champ]) && !empty($superglobale[$champ])) {
            $reponse = true;
        } else {
            return false;
        }
        // Envoyer la réponse "return"
        return $reponse;
    }
}



/**
 * Fonction de thumb : découpe une image en créant un carré de la taille demandée
 *
 * @param int $taille du carré
 * @param string $nom du fichier image
 */
function thumb($taille, $nom)
{
    // On sépare nom et extension
    $debutNom = pathinfo($nom, PATHINFO_FILENAME);
    $extension = pathinfo($nom, PATHINFO_EXTENSION);

    // "Créer" le nom complet de l'image (chemin + nom de fichier)
    $nomComplet = __DIR__ . '/../uploads/' . $nom;

    // On récupère les informations de l'image
    $infosImage = getimagesize($nomComplet);

    // Définition des dimensions de l'image "finale"
    $largeurFinale = $taille;
    $hauteurFinale = $taille;

    // On crée l'image de destination vide 'en mémoire RAM"
    $imageDest = imagecreatetruecolor($largeurFinale, $hauteurFinale);

    // On charge l'image source en mémoire (en fonction de son type)
    switch ($infosImage['mime']) {
        case 'image/jpeg':
            $imageSrc = imagecreatefromjpeg($nomComplet);
            break;

        case 'image/png':
            $imageSrc = imagecreatefrompng($nomComplet);
            break;

        case 'image/gif':
            $imageSrc = imagecreatefromgif($nomComplet);
            break;
    }

    // On initialise les décalages et on gère le cas "image carrée"
    $decalageX = 0;
    $decalageY = 0;

    // Si largeur > hauteur
    if ($infosImage[0] > $infosImage[1]) {
        // Image paysage
        // On calcule le décalageX = (largeurImage - largeurCarré) / 2
        $decalageX = ($infosImage[0] - $infosImage[1]) / 2;
        $tailleCarreSrc = $infosImage[1];
    }

    // Si largeur < hauteur
    if ($infosImage[0] <= $infosImage[1]) {
        // Image portrait
        //hauteurCarré = largeur
        // DecalageY = (hauteurImage - hauteurCarré) / 2
        $decalageY = ($infosImage[1] - $infosImage[0]) / 2;
        $tailleCarreSrc = $infosImage[0];
    }

    // Copier le contenu du carré source dans le carré destination
    imagecopyresampled(
        $imageDest, // Image dans laquelle on copie l'image d'origine
        $imageSrc, // Image d'origine
        0, // Décalage horizontal dans l'image de destination
        0, // Décalage vertical dans l'image de destination
        $decalageX, // Décalage horizontal dans l'image source
        $decalageY, // Décalage vertical dans l'image source
        $largeurFinale, // Largeur de la zone cible dans l'image de destination
        $hauteurFinale, // Hauteur de la zone cible dans l'image de destination
        $tailleCarreSrc, // Largeur de la zone cible dans l'image source
        $tailleCarreSrc // Hauteur de la zone cible dans l'image source
    );

    // On enregistre l'image de destination
    // On définit le chemin d'enregistrement et le nom du fichier destination : nom-300x300.ext
    $nomDest = __DIR__ . '/../uploads/' . $debutNom . '-' .$taille .'x' . $taille . '.' .$extension;

    // On enregistre physiquement 
    switch ($infosImage['mime']) {
        case 'image/jpeg':
            imagejpeg($imageDest, $nomDest);
            break;

        case 'image/png':
            imagepng($imageDest, $nomDest);
            break;

        case 'image/gif':
            imagegif($imageDest, $nomDest);
            break;
    }

    // On détruit les images "en mémoire RAM"
    imagedestroy($imageDest);
    imagedestroy($imageSrc);
}


/**
 * Fonction de resize image : redimensionne une image en %
 *
 * @param string $nom du fichier image
 * @param int % de la redimension
 */
function resizeImage($nom, $pourcentage)
{
    // On sépare nom et extension
    $debutNom = pathinfo($nom, PATHINFO_FILENAME);
    $extension = pathinfo($nom, PATHINFO_EXTENSION);

    // "Créer" le nom complet de l'image (chemin + nom de fichier)
    $nomComplet = __DIR__ . '/../uploads/' . $nom;

    // On récupère les informations de l'image
    $infosImage = getimagesize($nomComplet);

    // Définition des dimensions de l'image "finale"
    $largeurFinale = $infosImage[0]*$pourcentage/100;
    $hauteurFinale = $infosImage[1]*$pourcentage/100;

    // On crée l'image de destination vide 'en mémoire RAM"
    $imageDest = imagecreatetruecolor($largeurFinale, $hauteurFinale);

    // On charge l'image source en mémoire (en fonction de son type)
    switch ($infosImage['mime']) {
        case 'image/jpeg':
            $imageSrc = imagecreatefromjpeg($nomComplet);
            break;

        case 'image/png':
            $imageSrc = imagecreatefrompng($nomComplet);
            break;

        case 'image/gif':
            $imageSrc = imagecreatefromgif($nomComplet);
            break;
    }

    // On initialise les décalages et on gère le cas "image carrée"
    $decalageX = 0;
    $decalageY = 0;

    // Si largeur > hauteur
    if ($infosImage[0] > $infosImage[1]) {
        // Image paysage
        // On calcule le décalageX = (largeurImage - largeurCarré) / 2
        $decalageX = ($infosImage[0] - $infosImage[1]) / 2;
        $tailleCarreSrc = $infosImage[1];
    }

    // Si largeur < hauteur
    if ($infosImage[0] <= $infosImage[1]) {
        // Image portrait
        //hauteurCarré = largeur
        // DecalageY = (hauteurImage - hauteurCarré) / 2
        $decalageY = ($infosImage[1] - $infosImage[0]) / 2;
        $tailleCarreSrc = $infosImage[0];
    }

    // Copier le contenu du carré source dans le carré destination
    imagecopyresampled(
        $imageDest, // Image dans laquelle on copie l'image d'origine
        $imageSrc, // Image d'origine
        0, // Décalage horizontal dans l'image de destination
        0, // Décalage vertical dans l'image de destination
        0, // Décalage horizontal dans l'image source
        0, // Décalage vertical dans l'image source
        $largeurFinale, // Largeur de la zone cible dans l'image de destination
        $hauteurFinale, // Hauteur de la zone cible dans l'image de destination
        $infosImage[0], // Largeur de la zone cible dans l'image source
        $infosImage[1] // Hauteur de la zone cible dans l'image source
    );

    // On enregistre l'image de destination
    // On définit le chemin d'enregistrement et le nom du fichier destination : nom-300x300.ext
    $nomDest = __DIR__ . '/../uploads/' . $debutNom . '-' .$pourcentage . '.'.$extension;

    // On enregistre physiquement 
    switch ($infosImage['mime']) {
        case 'image/jpeg':
            imagejpeg($imageDest, $nomDest);
            break;

        case 'image/png':
            imagepng($imageDest, $nomDest);
            break;

        case 'image/gif':
            imagegif($imageDest, $nomDest);
            break;
    }

    // On détruit les images "en mémoire RAM"
    imagedestroy($imageDest);
    imagedestroy($imageSrc);
}

<?php 

/**
 * Fonction universelle de vérification de formulaire
 *
 * @param array $superglobale Variable $_GET ou $_POST
 * @param array $champs Tableau des champs à vérifier
 * @return bool
 */
function verifForm($superglobale, $champs){
//     // Fonction universelle de vérification de formulaire
        // Boucler sur "champs"
        foreach ($champs as $champ){
            // Vérifier si le champ existe et si le champ n'est pas vide
            if (isset($superglobale[$champ]) && !empty($superglobale[$champ])){
                $reponse = true;
            } else {
                return false ;
            } 
            // Envoyer la réponse "return"
            return $reponse;
    }
}

?>
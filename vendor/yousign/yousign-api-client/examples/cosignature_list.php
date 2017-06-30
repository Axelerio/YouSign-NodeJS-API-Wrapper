<?php

/**
 * Cet exemple permet de récupérer les cosignatures créées.
 * Ici, les 30 dernières demandes de signature peu importe le statut.
 *
 * Remarque :
 * ----------
 *
 * L'utilisateur doit être authentifié (cf l'exemple: 'connection.php')
 * 
 */

// Inclusion du loader
$loader = require_once dirname(__FILE__).'/../vendor/autoload.php';

// Définition du chemin de configuration
$configFile = dirname(__FILE__).'/../ysApiParameters.ini';

// Création du client en passant les identifiants en paramètres
$client = new \YousignAPI\YsApi($configFile);

// Options de recherche
$options = array (
    'search'        => '',  // On recherche tout
    'firstResult'   => 0,   // A partir du premier résultat
    'count'         => 30,  // Nombre de résultat retourné
    'status'        => ''   // Peu importe le statut
);

// Appel du client
$result = $client->getListCosign($options);

// Affichage du/des résultats
if($result === false) {
    echo 'Une erreur est survenue :';
    var_dump($client->getErrors());
} else {
    echo 'Listing des cosignatures récupérées : ';
    
    foreach ($result as $value) {
        var_dump($value);
        echo '<hr />';
    }
}

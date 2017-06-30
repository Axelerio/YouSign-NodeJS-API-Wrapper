<?php

/**
 * Cet exemple permet de récupérer les informations/détails d'une cosignature.
 * 
 * Ici on récupère les détails de la dernière cosignature créée.
 * La variable "$idDemand" peut-être définie directement pour récupérer les informations associées à celles-ci.
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

// Récupération de la dernière cosignature créée (voir cosignature_list.php)
$result = $client->getListCosign(array ('count' => 1));
if($result === false) {
    echo 'Une erreur est survenue : ';
    var_dump($client->getErrors());
    exit;
}

if(count($result) === 0) {
    echo 'Aucune cosignature de créée pour le moment.';
    exit;
}

// Récupération des informations de la cosignature
$idDemand = $result[0]['cosignatureEvent'];
$result = $client->getCosignInfoFromIdDemand($idDemand);

// Affichage des résultats
if($result === false) {
    echo 'Une erreur est survenue :';
    var_dump($client->getErrors());
} else {
    echo 'Détails de la cosignature ayant l\'id "'.$idDemand.'"';
    var_dump($result);
}

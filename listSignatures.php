<?php
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
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

// Parse parameters
$email = isset($argv[1]) ? $argv[1] : false;

if(!$email){
     $output = array("success" => false, 
    "errors" => "Missing parameters. You must send : email\n Example : php initSignature.php jean@dubois.org");
} else {

    // Inclusion du loader
    $loader = require_once dirname(__FILE__).'/vendor/autoload.php';

    // Définition du chemin de configuration
    $configFile = dirname(__FILE__).'/../../ysApiParameters.ini';

    // Création du client en passant les identifiants en paramètres
    $client = new \YousignAPI\YsApi($configFile);

    // Options de recherche
    $options = array (
        'search'        => $email,  // On recherche tout
        'firstResult'   => 0,   // A partir du premier résultat
        'count'         => 100,  // Nombre de résultat retourné
        'status'        => ''   // Peu importe le statut
    );

    // Appel du client
    $result = $client->getListCosign($options);

    // Affichage du/des résultats
    if($result === false) {
        $output = array("success" => false, 
        "errors" => $client->getErrors());
    } else {
        $output = array("success" => true, 
        "results" => $result);
    }
}
echo (json_encode($output, JSON_PRETTY_PRINT));

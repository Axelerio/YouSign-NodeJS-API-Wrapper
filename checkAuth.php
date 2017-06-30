<?php
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
/**
 * Cet exemple permet de :
 *     - vérifier que la configuration du client soit correct 
 *     - vérifier que l'utilisateur puisse utiliser l'API
 *
 * 
 * Remarque : 
 * ----------
 * 
 * La méthode "connect()" permet de vérifier si l'utilisateur a bien accès à l'API.
 * 
 * L'utilisation des autres méthodes ne nécessite pas de passer obligatoirement
 * par la méthode "connect()".
 * 
 */

// Inclusion du loader
$loader = require_once dirname(__FILE__).'/vendor/autoload.php';

// Définition du chemin de configuration
$configFile = dirname(__FILE__).'/ysApiParameters.ini';

// Création du client en passant les identifiants en paramètres
$client = new \YousignAPI\YsApi($configFile);

// Connection à l'API
$client->connect();

// Vérification que la connexion ait fonctionnée
if(!$client->isAuthenticated()) {
    echo json_encode(array( "success" => false,
     "status" => "not_authenticated",
     "errors" => $client->getErrors()),
     JSON_PRETTY_PRINT);
} else {
    echo json_encode(array( "success" => true,
     "status" => "authenticated"), JSON_PRETTY_PRINT);
}
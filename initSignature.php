<?php
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
/**
 * Cet exemple permet de créer une cosignature.
 * 
 * Ici une signature avec 2 fichiers et 2 signataires.
 *
 * Remarque :
 * ----------
 *
 * L'utilisateur doit être authentifié (cf l'exemple: 'connection.php')
 * 
 */

 // Parse parameters
$documentToSignAbsolutePath = isset($argv[1]) ? $argv[1] : false;
$firstName = isset($argv[2]) ? $argv[2] : false;
$lastName = isset($argv[3]) ? $argv[3] : false;
$mail = isset($argv[4]) ? $argv[4] : false;
$phone = isset($argv[5]) ? $argv[5] : false;
$signatures = isset($argv[6]) ? $argv[6] : false;


if(!($documentToSignAbsolutePath && $firstName && $lastName && $mail && $phone && $signatures)){
     $output = array("success" => false, 
    "errors" => "Missing parameters. You must send : documentToSignRelativePath firstName lastName mail phone signatureRectangleCoords\n Example : php initSignature.php document1.pdf jean dubois jean@dubois.org +33674997509 351,32,551,132");
} else {
    // Inclusion du loader
    $loader = require_once dirname(__FILE__).'/vendor/autoload.php';

    // Définition du chemin de configuration
    $configFile = dirname(__FILE__).'/../../ysApiParameters.ini';

    // Création du client en passant les identifiants en paramètres
    $client = new \YousignAPI\YsApi($configFile);

    // Chemin des fichiers à signer
    $pathFile = $documentToSignAbsolutePath;

    // Création de la liste des fichiers à signer
    $listFiles = array (
        array (
            'name' => basename($pathFile),
            'content' => base64_encode(file_get_contents($pathFile)),
            'idFile' => $pathFile
        )
    );

    // Création de la liste des signataires
    $listPerson = array (
        array (
            'firstName' => $firstName,
            'lastName' => $lastName,
            'mail' => $mail,
            'phone' => $phone,
            'proofLevel' => 'LOW',
            'authenticationMode' => 'sms'
        )
    );

    // Nombre de signatures
    $signaturesArray = array();
    $splitSignatures = explode("-", $signatures);
    
    //Cas où on nous a envoyé seulement des coordonnées et pas une liste de couples coords/page (rétrocompatibilité)
    if(!isset($splitSignatures[1])){
        $signaturesArray[] =
            array (
                'visibleSignaturePage' => '1', // Sur la 1er page
                'isVisibleSignature' => true,
                'visibleRectangleSignature' => $signatures, //'351,32,551,132',
                'mail' => $mail
            );
    } else {
        $parsedSignatures = explode("_", $signatures);
        // Application des valeurs par défaut si il en manque
        foreach($parsedSignatures as $index => $signature){
            $signature = explode("-", $signature);
            $rectangle = $signature[0];
            $page = $signature[1];
            if(!isset($rectangle)){
                $rectangle = '351,32,551,132';
            }
            if(!isset($page)){
                $page = '1';
            }
            $signaturesArray[] =
                array (
                    'visibleSignaturePage' => $page,
                    'isVisibleSignature' => true,
                    'visibleRectangleSignature' => $rectangle,
                    'mail' => $mail
                );
        }
    }

    // Placement des signatures sur le document
    $visibleOptions = array
    (
        // Placement des signatures pour le 1er document
        $listFiles[0]['idFile'] => $signaturesArray
    );

    // Message vide car on est en mode Iframe
    $message = '';

    // Autres options
    $options = array 
    (
        'mode' => 'IFRAME',
        'archive' => false
    );

    // Appel du client et récupération du résultat
    $result = $client->initCoSign($listFiles, $listPerson, $visibleOptions, $message, $options);
    if($result === false) {
        $output = array("success" => false, 
        "errors" => $client->getErrors());
    } 
    else 
    {
        $output = array("success" => true,
        "signingUrl" => $client->getIframeUrl($result['tokens']['token']),
        "details" => $result);
    }
}
echo (json_encode($output, JSON_UNESCAPED_SLASHES));
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
$filesToSignPaths = isset($argv[1]) ? $argv[1] : false;
$firstName = isset($argv[2]) ? $argv[2] : false;
$lastName = isset($argv[3]) ? $argv[3] : false;
$mail = isset($argv[4]) ? $argv[4] : false;
$phone = isset($argv[5]) ? $argv[5] : false;
$signatures = isset($argv[6]) ? $argv[6] : false;


if(!($filesToSignPaths && $firstName && $lastName && $mail && $phone && $signatures)){
     $output = array("success" => false, 
    "errors" => "Missing parameters. You must send : documentToSignRelativePath firstName lastName mail phone signatureRectangleCoords\n Example : php initSignature.php document1.pdf jean dubois jean@dubois.org +33674997509 351,32,551,132");
} else {
    // Inclusion du loader
    $loader = require_once dirname(__FILE__).'/vendor/autoload.php';

    // Définition du chemin de configuration
    $configFile = dirname(__FILE__).'/../../ysApiParameters.ini';

    // Création du client en passant les identifiants en paramètres
    $client = new \YousignAPI\YsApi($configFile);

    // Création de la liste des fichiers à signer
    $listFiles = array();
    $splitFiles = explode("[]_THIS_IS_A_BIG_SEPARATOR_[]", $filesToSignPaths);
    
    // Cas où on nous a envoyé seulement un chemin de fichier et non un tableau de chemins de fichiers
    if(!isset($splitFiles[1])){
        $listFiles[] =
            array (
                'name' => basename($filesToSignPaths),
                'content' => base64_encode(file_get_contents($filesToSignPaths)),
                'idFile' => $filesToSignPaths
            );
    } else {
        // Cas où l'on a plusieurs fichiers à signer
        foreach($splitFiles as $index => $fileToSignPath){
            $listFiles[] =
                array (
                    'name' => basename($fileToSignPath),
                    'content' => base64_encode(file_get_contents($fileToSignPath)),
                    'idFile' => $fileToSignPath
                );
        }
    }

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
                'mail' => $mail,
                'document' => '0'
            );
    } else {
        
        $parsedSignatures = explode("_", $signatures);
        
        // Application des valeurs par défaut si il en manque
        foreach($parsedSignatures as $index => $signature){
            $signature = explode("-", $signature);
            $rectangle = $signature[0];
            $page = $signature[1];
            $document = $signature[2];
            if(!isset($rectangle)){
                $rectangle = '351,32,551,132';
            }
            if(!isset($page)){
                $page = '1';
            }
            if(!isset($document)){
                $document = '0';
            }
            $signaturesArray[] =
                array (
                    'visibleSignaturePage' => $page,
                    'isVisibleSignature' => true,
                    'visibleRectangleSignature' => $rectangle,
                    'mail' => $mail,
                    'document' => $document
                );
        }
    }

    // Placement des signatures sur le document
    $visibleOptions = array();
    foreach($signaturesArray as $index => $signature){
        $document = $signature["document"];
        $idFile = $listFiles[$document]['idFile'];

        // Première signature pour ce fichier, on crée un tableau
        if(!isset($visibleOptions[$idFile])){
            $visibleOptions[$idFile] = array();
        }

        //Ajout de la signature au tableau
        unset($signature[$document]); // on enleve l'id du doc pour ne pas l'envoyer à yousign inutilement
        $visibleOptions[$idFile][] = $signature;
    }

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
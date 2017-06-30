<?php

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

// Inclusion du loader
$loader = require_once dirname(__FILE__).'/../vendor/autoload.php';

// Définition du chemin de configuration
$configFile = dirname(__FILE__).'/../ysApiParameters.ini';

// Création du client en passant les identifiants en paramètres
$client = new \YousignAPI\YsApi($configFile);

// Chemin des fichiers à signer
$pathFile1 = dirname(__FILE__).'/documents/document1.pdf';
$pathFile2 = dirname(__FILE__).'/documents/document2.pdf';

// Création de la liste des fichiers à signer
$listFiles = array (
    array (
        'name' => basename($pathFile1),
        'content' => base64_encode(file_get_contents($pathFile1)),
        'idFile' => $pathFile1
    ),
    array (
        'name' => basename($pathFile2),
        'content' => base64_encode(file_get_contents($pathFile2)),
        'idFile' => $pathFile2
    )
);

// Création de la liste des signataires
$listPerson = array (
    array (
        'firstName' => 'Jean',
        'lastName' => 'Dupont',
        'mail' => 'jean.dupont@hostname.com',
        'phone' => '+33623456789',
        'proofLevel' => 'LOW',
        'authenticationMode' => 'sms'
    ),
    array (
        'firstName' => 'Hervé',
        'lastName' => 'Martin',
        'mail' => 'hmartin@hostname.com',
        'phone' => '+33632654987',
        'proofLevel' => 'LOW',
        'authenticationMode' => 'sms'
    )
);

// Placement des signatures sur le document
$visibleOptions = array
(
    // Placement des signatures pour le 1er document
    $listFiles[0]['idFile'] => array
    (
        array (
            'visibleSignaturePage' => '1', // Sur la 1er page
            'isVisibleSignature' => true,
            'visibleRectangleSignature' => '351,32,551,132',
            'mail' => 'jean.dupont@hostname.com'
        ),
        array (
            'visibleSignaturePage' => '1',
            'isVisibleSignature' => true,
            'visibleRectangleSignature' => '48,32,248,132',
            'mail' => 'hmartin@hostname.com'
        )
    ),

    // Placement des signatures pour le 2nd document
    $listFiles[1]['idFile'] => array
    (
        array (
            'visibleSignaturePage' => '2', // Sur la 2e page
            'isVisibleSignature' => true,
            'visibleRectangleSignature' => '351,32,551,132',
            'mail' => 'jean.dupont@hostname.com'
        ),
        array (
            'visibleSignaturePage' => '2',
            'isVisibleSignature' => true,
            'visibleRectangleSignature' => '48,32,248,132',
            'mail' => 'hmartin@hostname.com'
        )
    ),
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
    echo 'Une erreur est survenue :';
    var_dump($client->getErrors());
}
else
{
    // Récupération des informations de la signature et ajout d'un texte obligatoire pour l'ensemble des signataires
    // au niveau du document "document1.pdf"

    $requiredText =
        "Mention obligatoire à renseigner dans le cadre de ce test.\n" .
        "Veuillez rédiger ce texte pour signer le document.";

    // S'il n'y a qu'un seul token, on met la variable sous forme de tableau
    if(isset($result['tokens']['token']))
        $result['tokens'] = array($result['tokens']);

    // Récupération de l'idFile du document1
    $idFile = null;
    foreach($result['fileInfos'] as $value) {
        if($value['fileName'] === 'document1.pdf') {
            $idFile = $value['idFile'];
        }
    }

    // On ajoute le texte pour chacun des signataires
    foreach($result['tokens'] as $value) {
        $isTextAdded = $client->addTextToWrite($value['token'], $idFile, $requiredText);
        if($isTextAdded === false) {
            echo 'Echec d\'ajout du texte obligatoire :';
            var_dump($client->getErrors());
        }
    }

    // Remarque : 
    // ----------
    // L'url d'accès aux documents ci-dessous est https://demo.yousign.fr/public/ext/cosignature/<token>
    // En production, l'url d'accès est https://yousign.fr/public/ext/cosignature/<token>
    // 
    $links = array();
    foreach ($result['tokens'] as $value)
    {
        $url = $client->getIframeUrl($value['token']);
        $links[] =  '<li>' .
            'Lien pour le signataire "'.$value['mail'].'" : ' .
            '<a href="'.$url.'" target="_blank">'.$url.'</a>' .
            '</li>';
    }

    // Affichage des liens
    echo '<p>Cosignature créée avec succès</p>';
    echo '<h2>Liens d\'accès aux documents à signer</h2>';
    echo '<ul>'.implode($links, '').'</ul>';

    // Détail du retour de l'api
    echo '<h2>Détails du retour de l\'api</h2>';
    var_dump($result);
}

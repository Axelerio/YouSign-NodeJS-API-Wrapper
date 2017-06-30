<?php

/**
 * Cet exemple permet de télécharger des documents signés.
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

$resultDetails = array();
$downloadLinks = array();

$idDemand  = $result[0]['cosignatureEvent'];
$listFiles = $result[0]['fileInfos'];

// S'il n'y a qu'un fichier, il faut mettre le résultat sous forme de array
// pour homogénéiser
if(isset($listFiles['idFile'])) {
    $listFiles = array($listFiles);
}

foreach ($listFiles as $file)
{
    $fileResult = $client->getCosignedFileFromIdDemand($idDemand, $file['idFile']);
    if($fileResult === false) {
        echo 'Une erreur est survenue : ';
        var_dump($client->getErrors());
        exit;
    }

    // On récupère détail du résultat
    $resultDetails[] = $fileResult;

    // On crée le fichier temporaire
    $pathFile = './'.$fileResult['fileName'];
    if(!is_writable(dirname($pathFile)))
        throw new \RuntimeException(sprintf('The file "%s" is not writable', $pathFile));

    $handle = fopen($pathFile, 'w+');
    fwrite($handle, base64_decode($fileResult['file']));
    fclose($handle);

    // On affiche un lien de téléchargement du fichier 
    $downloadLinks[] = '<li>Téléchargement du fichier <a href="'.$pathFile.'" target="_blank">'.$fileResult['fileName'].'</a></li>';
}

echo '<h2>Liens de téléchargement des fichiers : </h2>';
echo '<ul>'.implode($downloadLinks, '').'</ul>';

echo '<h2>Détail des retours de l\'API</h2>';
foreach ($resultDetails as $details) {
    var_dump($details);
}

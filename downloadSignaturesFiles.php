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
$search = isset($argv[1]) ? base64_decode($argv[1]) : false;
$outputFolder = isset($argv[2]) ? base64_decode($argv[2]) : false;

if(!$search || !$outputFolder){
     $output = array(
        "success" => false, 
        "errors" => "Missing parameters. You must send : searchstring\n, outputFolder Example : php initSignature.php jean@dubois.org Users/John/data/output"
    );
} else {

    // Inclusion du loader
    $loader = require_once dirname(__FILE__).'/vendor/autoload.php';

    // Définition du chemin de configuration
    $configFile = dirname(__FILE__).'/../../ysApiParameters.ini';

    // Création du client en passant les identifiants en paramètres
    $client = new \YousignAPI\YsApi($configFile);

    // Options de recherche
    $options = array (
        'search'        => $search,  // On recherche tout
        'firstResult'   => 0,   // A partir du premier résultat
        'count'         => 100,  // Nombre de résultat retourné
        'status'        => ''  // Peu importe le statut
    );

    // Appel du client pour obtenir la liste des signatures
    $result = $client->getListCosign($options);

    if($result === false) {

        // Affichage du/des résultats
        $output = array("success" => false, "errors" => $client->getErrors());
    } else if(count($result) === 0) {

        // On peut s'arrêter ici si pas de resultats
        $output = array("success" => false, "errors" => "no results");
    } else {

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
                $output = array("success" => false, "errors" => $client->getErrors());
                echo (json_encode($output, JSON_PRETTY_PRINT));
                exit;
            }

            // On récupère détail du résultat
            $resultDetails[] = $fileResult;

            // On crée le fichier temporaire
            $pathFile = $outputFolder."/".$fileResult['fileName'];
            if(!is_writable(dirname($pathFile))){
                $output = array("success" => false, "errors" => "The file is not writable ".$pathFile);
                echo (json_encode($output, JSON_PRETTY_PRINT));
                exit;
            }
            
            // On écrit le fichier
            $handle = fopen($pathFile, 'w+');
            fwrite($handle, base64_decode($fileResult['file']));
            fclose($handle);
            
            // On affiche un lien de téléchargement du fichier 
            $downloadLinks[] = $pathFile;
        }

        // Affichage du/des résultats
        $output = array("success" => true, "results" => $downloadLinks);
    }
}
echo (json_encode($output, JSON_PRETTY_PRINT));
?>
<?php

require_once 'database.php';

// Enable all warnings and errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$db = dbConnect();

// Check request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$request =substr($_SERVER['PATH_INFO'], 1);
$request = explode('/', $request);
$requestResource = array_shift($request);

if ($requestResource == "cover") {
    
    $data = false;

    if ($requestMethod == "GET") {

        $id_cover = $_GET['id_cover'];

        // Sélectionnez l'image à partir de la base de données
        $requete = $db->prepare("SELECT image FROM Covers WHERE id = ?");
        $requete->bind_param('i', $id_cover);
        $requete->execute();

        // Récupérez les résultats
        $resultat = $requete->get_result();
        $donnees = $resultat->fetch_assoc();

        // Convertissez les données binaires en format base64
        $imageBase64 = base64_encode($donnees['image']);

        // Retournez l'image encodée en base64
        echo $imageBase64;
    }
    
}

?>

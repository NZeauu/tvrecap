<?php

/**
 * Filename: adusers.php
 * Author: Enzo Peigné
 * Description: Get the users, delete a user and update a user
 *              Send an email to the user when his account is deleted
 */

require_once 'addatabase.php';

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

if($requestResource == "getUsers"){

    $data = false;

    if($requestMethod == 'GET'){
        $data = getUsers($db);
    }
}

if($requestResource == "deleteUser"){

    $data = false;

    if($requestMethod == 'POST'){

        $email = $_POST['email'];
        $data = deleteUser($db, $email);

        $to = $email;
        $subject = "Votre compte TVRecap a été supprimé";
        $from = "tvrecap@epeigne.fr";

        $message .= "Bonjour,\n\n";
        $message .= "Votre compte a été supprimé par un administrateur. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter notre équipe.\n\n";
        $message .= "Vous pouvez nous contacter à l'adresse suivante : tvrecap@epeigne.fr ou directement sur le site dans l'onglet Contact de la page de connexion.\n\n";
        $message .= "Cordialement,\n\n";
        $message .= "L'équipe TVRecap";

        $headers = "From: " . $from . "\r\n";
        
        mail($to, $subject, $message, $headers);

    }
}

if($requestResource == 'updateUser'){
    $data = false;

    if($requestMethod == 'POST'){
        $userId = $_POST['userId'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $data = updateUser($db, $userId, $email, $username);
    }

}

header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
if($requestMethod == 'POST'){
    header('HTTP/1.1 200 Created');
}else{
    header('HTTP/1.1 200 OK');
}
echo json_encode($data);
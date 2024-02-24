<?php

/**
 * Filename: register.php
 * Author: Enzo Peigné
 * Description: Register a new account and check if the email and the username already exist in the database
 
 */

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

if($requestResource == "register"){

    $data = false;

    // Check if the request is a POST request
    if($requestMethod == "POST"){

        // Get the data from the request
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $data = registerAccount($db, $username, $email, $password_hash);

        // Generate a token for the account verification
        $token = bin2hex(random_bytes(16));

        // Hash the token
        $token_hash = hash('sha256', $token);

        // Insert the token in the database
        $result = insertVerifToken($db, $email, $token_hash);

        // Send a mail to the user
        if($result){
            $to = $email;
            $from = 'tvrecap.noreply@epeigne.fr';


            $subject = 'Vérification de votre compte TVRecap';
            $message = "Bonjour,\n\n";
            $message .= "Merci de vous être inscrit sur TVRecap. Pour valider votre compte, veuillez cliquer sur le lien suivant : \n";
            $message .= "https://epeigne.fr/tvrecap/html/verifaccount.html?token=" . $token . "\n\n";
            $message .= "Une fois votre compte validé, vous pourrez vous connecter à votre compte TVRecap.\n\n";
            $message .= "Merci de votre confiance.\n\n";
            $message .= "L'équipe TVRecap";
            $message .= "\n\nCeci est un mail automatique, merci de ne pas y répondre.";

            $headers = "From: " . $from;

            mail($to, $subject, $message, $headers);

        }
    }
}

if($requestResource == "mail"){
    $data = false;

    // Check if the request is a POST request
    if($requestMethod == "GET"){

        // Get the data from the request
        $email = $_GET['email'];

        $data = checkMail($db, $email);
    }

}

if($requestResource == "username"){
    $data = false;

    // Check if the request is a POST request
    if($requestMethod == "GET"){

        // Get the data from the request
        $username = $_GET['username'];

        $data = checkUsername($db, $username);
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
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

if($requestResource == "mail"){
    $data = false;

    // Check if the request is a POST request
    if($requestMethod == "GET"){

        // Get the data from the request
        $email = $_GET['email'];

        $data = checkMail($db, $email);
    }

}

if($requestResource == "login"){
    $data = false;

    // Check if the request is a POST request
    if($requestMethod == "POST"){

        // Get the data from the request
        $email = $_POST['email'];
        $password = $_POST['password'];

        $data = connectionAccount($db, $email, $password);

        // Create a cookie session with the user id
        if($data){
            setcookie('user_mail', $email, time() + 3600, '/');
        }

    }

}

if($requestResource == 'resetPass'){
    $data = false;

    // Check if the request is a POST request
    if($requestMethod == "POST"){

        $email = $_POST['email'];

        // Generate a token
        $token = bin2hex(random_bytes(16));

        // Hash the token
        $token_hash = hash('sha256', $token);

        // Create an expiration date for the token (15 minutes)
        $expiration_date = date('Y-m-d H:i:s', time() + 60 * 15);

        // Insert the token in the database
        $data = insertToken($db, $email, $token_hash, $expiration_date);

        // Send an email to the user with the token
        if($data){
            $to = $email;
            $from = 'tvrecap.noreply@epeigne.fr';
            $subject = 'Reset your password';
            
            // Create the link to reset the password
            $link = 'https://epeigne.fr/tvrecap/html/reset-password.html?token=' . $token;

            // Create the email content
            $message = "Bonjour,\n\n";
            $message .= "Vous avez demandé à réinitialiser votre mot de passe. Veuillez cliquer sur le lien ci-dessous pour le réinitialiser.\n\n";
            $message .= "Link: " . $link . "\n\n";
            $message .= "Une fois le mot de passe réinitialisé, vous pourrez vous connecter à votre compte avec le nouveau mot de passe.\n\n";

            // Add a message footer for no reply
            $message .= "Ceci est un message automatique, merci de ne pas y répondre.\n\n";

            // Create the email headers
            $headers = "From:" . $from;

            // Send the email
            mail($to, $subject, $message, $headers);

            $data = array("success" => true, "msg" => "Un email vous a été envoyé pour réinitialiser votre mot de passe. Veuillez vérifier votre boîte de réception ou votre dossier de courrier indésirable (spam).");
        }        
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
<?php

/**
 * Filename: connect.php
 * Author: Enzo Peigné
 * Description: Connect the user to his account and send an email to reset the password if asked by the user. 
 *              The file also checks if the email already exists in the database before connecting the user to display an error message.
 */

require_once 'database.php';

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    if($requestMethod == "GET"){

        // Get the data from the request
        $email = $_GET['email'];

        $data = checkMail($db, $email);
    }

}

if($requestResource == "login"){
    $data = false;

    if($requestMethod == "POST"){

        // Get the data from the request
        $email = $_POST['email'];
        $password = $_POST['password'];
        $rememberme = $_POST['rememberme'];

        $data = connectionAccount($db, $email, $password);

        // Create a cookie session with the user token and save the token in the database
        if($data){

            // Generate a token
            $token = bin2hex(random_bytes(16));

            // Hash the token
            $token_hash = hash('sha256', $token);

            // If the user wants to stay connected keep the cookie for 30 days else keep it for 1 hour
            if($rememberme == "true"){

                // Insert the token in the database
                insertSessionToken($db, $email, $token_hash, date('Y-m-d H:i:s', time() + 3600 * 24 * 30));
                
                // Create a cookie session with the token with secure and httpOnly attributes and a strict policy
                setcookie('USERSESSION', $token_hash, time() + 3600 * 24 * 30, '/', "epeigne.fr", true, true);
            }else{

                // Insert the token in the database
                insertSessionToken($db, $email, $token_hash, date('Y-m-d H:i:s', time() + 3600));

                // Create a cookie session with the token with secure and httpOnly attributes and a strict policy
                setcookie('USERSESSION', $token_hash, time() + 3600, '/', "epeigne.fr", true, true);
            }
            
        }

    }

}

if($requestResource == "cookieCheck"){
    $data = false;

    if($requestMethod == "GET"){

        // Get the data from the request
        if(isset($_COOKIE['USERSESSION'])){
            $token = $_COOKIE['USERSESSION'];

            $page = $_GET['page'];
    
            $data = checkSessionToken($db, $token, $page);
        }

    }

}


if($requestResource == 'resetPass'){
    $data = false;

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

            $mail = new PHPMailer(true);

            try{
                // PHPMailer configuration
                require_once 'phpMailerConf.php';

                $to = $email;
                $from = 'tvrecap.noreply@epeigne.fr';

                $mail->setFrom($from, 'TVRecap');
                $mail->addAddress($to);

                // Caracters encoding
                $mail->CharSet = 'UTF-8';

                // HTML content of the email
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe TVRecap';
                $mail->Body = '
                    <html>
                        <head>
                            <title>Réinitialisation de votre mot de passe TVRecap</title>
                            <style>
                                body {
                                    display: flex;
                                    width: 100%;
                                    height: 100%;
                                    flex-direction: column;
                                    padding: 20px;
                                }
                            </style>
                        </head>
                        <body>
                            <div style="width: 95%; display: flex; justify-content: center; background-color: black; border-radius: 10px; margin: 2%; font-style: italic; align-items: center;">
                                <h1 style="color: white; width: 100%; text-align:center"><span style="color: red;">TV</span>Recap</h1>
                            </div>
                            <div>
                                <h3>Réinitialisation de votre mot de passe TVRecap</h3>
                                <p>Bonjour,</p>
                                <p>Vous avez demandé à réinitialiser votre mot de passe. Veuillez cliquer sur le lien ci-dessous pour le réinitialiser.</p>
                                <p>Link: <a href="https://epeigne.fr/tvrecap/html/reset-password.html?token=' . $token . '">Réinitialiser mon mot de passe</a></p>
                                <p>Une fois le mot de passe réinitialisé, vous pourrez vous connecter à votre compte avec le nouveau mot de passe.</p>
                                <p>Merci de votre confiance.</p>
                                <p>L\'équipe TVRecap</p>
                                <br><hr><br>
                                <p>Ceci est un mail automatique, merci de ne pas y répondre.</p>
                            </div>
                        </body>
                    </html>
                ';

                // Send the email
                $mail->send();

                $data = array("success" => true, "msg" => "Un email vous a été envoyé pour réinitialiser votre mot de passe. Veuillez vérifier votre boîte de réception ou votre dossier de courrier indésirable (spam).");

            }catch(Exception $e){
                $data = array("success" => false, "msg" => "Votre demande de réinitialisation de mot de passe n'a pas pu être prise en compte.");
            }
        }        
    }

}

if($requestResource == 'checkVerified'){
    $data = false;

    if($requestMethod == "GET"){

        // Get the data from the request
        $email = $_GET['email'];

        $data = checkVerified($db, $email);
    }

}

if($requestResource == 'resendMail'){
    $data = false;

    if($requestMethod == "POST"){

        // Get the data from the request
        $email = $_POST['email'];

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

            $data = array("success" => true, "msg" => "Un email vous a été envoyé pour valider votre compte. Veuillez vérifier votre boîte de réception ou votre dossier de courrier indésirable (spam).");

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
<?php

/**
 * Filename: register.php
 * Author: Enzo Peigné
 * Description: Register a new account and check if the email and the username already exist in the database
 
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

            $mail = new PHPMailer(true);

            try{
                // PHPMailer configuration
                require_once 'phpMailerConf.php';

                $to = $email;
                $from = 'tvrecap.noreply@epeigne.fr';

                $mail->setFrom($from, 'TVRecap');
                $mail->addAddress($to, $username);

                // Caracters encoding
                $mail->CharSet = 'UTF-8';

                // HTML content of the email
                $mail->isHTML(true);
                $mail->Subject = 'Vérification de votre compte TVRecap';
                $mail->Body = '
                    <html>
                        <head>
                            <title>Vérification de votre compte TVRecap</title>
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
                                <p>Bonjour,</p>
                                <p>Merci de vous être inscrit sur TVRecap. Pour valider votre compte, veuillez cliquer sur le lien suivant :</p>
                                <a href="https://epeigne.fr/tvrecap/html/verifaccount.html?token=' . $token . '">Valider mon compte</a>
                                <p>Une fois votre compte validé, vous pourrez vous connecter à votre compte TVRecap.</p>
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

            }catch(Exception $e){
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

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
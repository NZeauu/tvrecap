<?php

/**
 * Filename: adusers.php
 * Author: Enzo Peigné
 * Description: Get the users, delete a user and update a user
 *              Send an email to the user when his account is deleted
 */

require_once 'addatabase.php';

require '../../vendor/autoload.php';

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

if($requestResource == "getUsers"){

    $data = false;

    if($requestMethod == 'GET'){
        $data = getUsers($db);
    }
}

if($requestResource == "deleteUser"){

    $data = false;

    if($requestMethod == 'POST'){

        $mail = new PHPMailer(true);

        $email = $_POST['email'];

        try{
            
            $data = deleteUser($db, $email);

            if(!$data){
                throw new Exception('Error');
            }

            // PHPMailer configuration
            require_once '../../php/phpMailerConf.php';

            $to = $email;

            $mail->setFrom('tvrecap.noreply@epeigne.fr', 'TVRecap');
            $mail->addAddress($to);

            // Caracters encoding
            $mail->CharSet = 'UTF-8';

            // HTML content of the email
            $mail->isHTML(true);
            $mail->Subject = "Votre compte TVRecap a été supprimé";
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
                        <p>Votre compte a été supprimé par un administrateur. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter notre équipe.</p>
                        <p>Vous pouvez nous contacter via le formulaire de contact sur le site ou à l\'adresse suivante :
                            <a href="mailto:tvrecap@epeigne.fr">tvrecap@epeigne.fr</a>
                        </p>
                        <p>Cordialement,</p>
                        <p>L\'équipe TVRecap</p>
                        <br><hr><br>
                        <p>Ceci est un mail automatique, merci de ne pas y répondre.</p>
                    </div>
                </body>
            </html>
            ';

            // Send the email
            $mail->send();

            $data = true;

        }catch(Exception $e){
            $data = false;
        }
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
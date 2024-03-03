<?php

/**
 * Filename: add.php
 * Author: Enzo Peigné
 * Description: Send an email to the administrator to ask for adding a new content
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

if($requestResource == "sendmail"){

    $data = false;

    if($requestMethod == "POST"){

        $mail = new PHPMailer(true);

        $type = $_POST['type'];

        if($type == "movie"){
            $title = $_POST['title'];
            $year = $_POST['year'];
            $duration = $_POST['duration'];
            $synopsis = $_POST['synopsis'];

            $message = "<h4>~~CONTENT ADDING ASK~~</h4>
                        <p>Title: $title</p>
                        <p>Year: $year</p>
                        <p>Duration: $duration</p>
                        <p>Synopsis: $synopsis</p>";

        }else if($type == "serie"){
            $title = $_POST['title'];
            $year = $_POST['year'];
            $seasons = $_POST['seasons'];
            $synopsis = $_POST['synopsis'];

            $message = "<h4>~~CONTENT ADDING ASK~~</h4>
                        <p>Title: $title</p>
                        <p>Year: $year</p>
                        <p>Seasons: $seasons</p>
                        <p>Synopsis: $synopsis</p>";
        }


        try{
            // PHPMailer configuration
            require_once 'phpMailerConf.php';

            $to = "tvrecap@epeigne.fr";
            
            $mail->setFrom('system.tvrecap@epeigne.fr', 'SYSTEM TVRecap');
            $mail->addAddress($to, 'HOST Mail TVRecap');

            // Caracters encoding
            $mail->CharSet = 'UTF-8';

            // HTML content of the email
            $mail->isHTML(true);
            $mail->Subject = 'Demande d\'ajout de contenu TVRecap';
            $mail->Body = '
                <html>
                    <head>
                        <title>Demande d\'ajout de contenu TVRecap</title>
                        <style>
                            body {
                                display: flex;
                                width: 100%;
                                height: 100%;
                                flex-direction: column;
                            }
                            .message-container {
                                width: 95%;
                                margin: 2%;
                            }
                            .message {
                                word-wrap: break-word; 
                                text-align: center;
                            }
                        </style>
                    </head>
                    <body>
                        <div style="width: 95%; display: flex; justify-content: center; background-color: black; border-radius: 10px; margin: 2%;">
                            <img src="https://epeigne.fr/tvrecap/img/darkHeader.png" alt="Header Image" style="width: 150px; height: auto;">
                        </div>
                        <div class="message-container">
                            <h1>Demande d\'ajout de contenu TVRecap</h1>
                            <p>Un utilisateur a envoyé une demande d\'ajout de contenu.</p>
                            <p>Type de contenu: <strong>' . $type . '</strong></p>
                            <p>Message:</p>
                            <div class="message">' . $message . '</div>
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

header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
if($requestMethod == 'POST'){
    header('HTTP/1.1 200 Created');
}else{
    header('HTTP/1.1 200 OK');
}
echo json_encode($data);
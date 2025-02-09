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

        if ($type === "movie") {
            $title = htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
            $duration = htmlspecialchars($_POST['duration'] ?? '', ENT_QUOTES, 'UTF-8');
            $synopsis = htmlspecialchars($_POST['synopsis'] ?? '', ENT_QUOTES, 'UTF-8');
    
            $message = "<h4>~~CONTENT ADDING ASK~~</h4>
                        <p><strong>Title:</strong> " . nl2br($title) . "</p>
                        <p><strong>Year:</strong> " . ($year ?: 'Invalid Year') . "</p>
                        <p><strong>Duration:</strong> " . nl2br($duration) . "</p>
                        <p><strong>Synopsis:</strong> " . nl2br($synopsis) . "</p>";
    
        } elseif ($type === "serie") {
            $title = htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
            $seasons = filter_var($_POST['seasons'] ?? '', FILTER_VALIDATE_INT);
            $synopsis = htmlspecialchars($_POST['synopsis'] ?? '', ENT_QUOTES, 'UTF-8');
    
            $message = "<h4>~~CONTENT ADDING ASK~~</h4>
                        <p><strong>Title:</strong> " . nl2br($title) . "</p>
                        <p><strong>Year:</strong> " . ($year ?: 'Invalid Year') . "</p>
                        <p><strong>Seasons:</strong> " . ($seasons ?: 'Invalid Seasons') . "</p>
                        <p><strong>Synopsis:</strong> " . nl2br($synopsis) . "</p>";
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
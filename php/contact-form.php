<?php

    /**
     * Filename: contact-form.php
     * Author: Enzo Peigné
     * Description: Send an email to the administrator for a contact request from the user on the website (every contact form on the website)
     */

    // Enable all warnings and errors
    ini_set('display_errors', 1);
    error_reporting(E_ALL);


    require '../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true);


    try {
        // PHPMailer configuration
        require_once 'phpMailerConf.php';

        $userMail = $_POST['userMail'];
        $username = $_POST['username'];
        $contactSubject = $_POST['subject'];
        $content = $_POST['message'];

        $to = "tvrecap@epeigne.fr";

        $mail->setFrom($userMail, $username);
        $mail->addAddress($to, 'TVRecap');

        // Caracters encoding
        $mail->CharSet = 'UTF-8';

        // HTML content of the email
        $mail->isHTML(true);
        $mail->Subject = 'Nouvelle demande de contact de la part de ' . $username;
        $mail->Body = '
            <html>
                <head>
                    <title>Nouvelle demande de contact</title>
                    <style>
                        body {
                            display: flex;
                            width: 100%;
                            height: 100%;
                            flex-direction: column;
                            background-color: #E5E5E5;
                        }
                    </style>
                </head>
                <body>
                    <div style="width: 95%; display: flex; justify-content: center; background-color: black; border-radius: 10px; margin: 2%;">
                        <img src="https://epeigne.fr/tvrecap/img/darkHeader.png" alt="Header Image" style="width: 150px; height: auto;">
                    </div>
                    <div>
                        <h1>Nouvelle demande de contact</h1>
                        <p>Un utilisateur a envoyé une nouvelle demande de contact.</p>
                        <p>Adresse de contact: ' . $userMail . '</p>
                        <p>Objet de la demande: ' . $contactSubject . '</p>
                        <p>Message: ' . $content . '</p>
                    </div>
                </body>
            </html>
        ';

        // Send the email
        $mail->send();

        if($contactSubject == 'Contact Login Page' || $contactSubject == 'Contact Register Page'){
            $data = array("success" => true, "msg" => "Votre message a bien été envoyé !");
        }
        else{
            $data = array("success" => true, "msg" => "Votre message a bien été envoyé, vous allez être redirigé vers la page d'accueil.");
        }

    } catch (Exception $e) {
        $data = array("success" => false, "msg" => "Votre message n'a pas pu être envoyé.");
    }



    header('Content-Type: application/json; charset=utf-8');
    header('Cache-control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('HTTP/1.1 200 Sent');
    echo json_encode($data);
?>
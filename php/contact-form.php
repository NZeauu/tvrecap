<?php

    /**
     * Filename: contact-form.php
     * Author: Enzo Peigné
     * Description: Send an email to the administrator for a contact request from the user on the website (every contact form on the website)
     */

    // Enable all warnings and errors
    ini_set('display_errors', 1);
    error_reporting(E_ALL);


    // Change the email address to the administrator's email address
    $to = "tvrecap@epeigne.fr";


    $from = $_POST['userMail'];
    $username = $_POST['username'];
    $contactSubject = $_POST['subject'];
    $content = $_POST['message'];
    
    $subject = "Formulaire de contact - " . $contactSubject;
    
    $message = "Nouvelle demande de contact de la part de $username\n\n";
    $message .= "Adresse de contact: $from \n\n";
    $message .= "Objet de la demande: $contactSubject\n\n";
    $message .= "Message: $content";
    
    $headers = "From:" . $from;
    
    mail($to,$subject,$message,$headers);

    if($contactSubject == 'Contact Login Page' || $contactSubject == 'Contact Register Page'){
        $data = array("success" => true, "msg" => "Votre message a bien été envoyé !");
    }
    else{
        $data = array("success" => true, "msg" => "Votre message a bien été envoyé, vous allez être redirigé vers la page d'accueil.");
    }



    header('Content-Type: application/json; charset=utf-8');
    header('Cache-control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('HTTP/1.1 200 Sent');
    echo json_encode($data);
?>
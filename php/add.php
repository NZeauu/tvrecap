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

if($requestResource == "sendmail"){

    $data = false;

    if($requestMethod == "POST"){
        $type = $_POST['type'];

        if($type == "movie"){
            $title = $_POST['title'];
            $year = $_POST['year'];
            $duration = $_POST['duration'];
            $synopsis = $_POST['synopsis'];

            $message = "~~CONTENT ADDING ASK~~\n\n
                        Title: $title\n
                        Year: $year\n
                        Duration: $duration\n
                        Synopsis: $synopsis";

        }else if($type == "serie"){
            $title = $_POST['title'];
            $year = $_POST['year'];
            $seasons = $_POST['seasons'];
            $synopsis = $_POST['synopsis'];

            $message = "~~CONTENT ADDING ASK~~\n\n
                        Title: $title\n
                        Year: $year\n
                        Seasons: $seasons\n
                        Synopsis: $synopsis";
        }

        $to = "tvrecap.noreply@epeigne.frstud.fr";
        $subject = "TVRecap - Add new $type";

        $header = "From: tvrecap.informations@gmail.com";

        // Send email
        mail($to, $subject, $message, $header);

        $data = true;
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
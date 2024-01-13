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
            setcookie('user_id', $email, time() + 20, '/');
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
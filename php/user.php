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

if($requestResource == "avatar"){

    $data = false;

    if($requestMethod == "GET"){
        
        $email = $_GET['email'];

        $data = getAvatar($db, $email);
    }

}

if($requestResource == "username"){

    $data = false;

    if($requestMethod == "GET"){
        
        $email = $_GET['email'];

        $data = getUsername($db, $email);

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
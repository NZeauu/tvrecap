<?php

/**
 * Filename: reset-pass.php
 * Author: Enzo Peigné
 * Description: Check if the reset token is valid and reset the password
 */

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

if($requestResource == "checkToken"){

    $data = false;

    if($requestMethod == "GET"){

        $token = $_GET['token'];

        $token_hash = hash('sha256', $token);

        $data = checkToken($db, $token_hash);
    }

}

if($requestResource == "resetPassword"){

    $data = false;

    if($requestMethod == "POST"){

        $token = $_POST['token'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        $token_hash = hash('sha256', $token);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $data = resetPass($db, $token_hash, $password_hash, $email);
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
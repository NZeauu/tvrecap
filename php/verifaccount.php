<?php

/**
 * Filename: verifaccount.php
 * Author: Enzo Peigné
 * Description: This file is used to verify the account of a user with the tlink sent by email
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

if($requestResource == 'verifToken'){
    $data = false;
    if($requestMethod == 'POST'){
        $token = $_POST['token'];

        // hash token
        $token = hash('sha256', $token);

        $data = checkVerifToken($db, $token);
    }
}

if($requestResource == 'verifAccount'){
    $data = false;
    if($requestMethod == 'POST'){
        $token = $_POST['token'];

        // hash token
        $token = hash('sha256', $token);

        $data = verifAccount($db, $token);
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
<?php

/**
 * Filename: adhome.php
 * Author: Enzo Peigné
 * Description: Get the number of users and the last user registered to the database
 */

require_once 'addatabase.php';

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

if($requestResource == "numUsers"){

    $data = false;

    if($requestMethod == "GET"){
        $data = getNumUsers($db);
    }

}

if ($requestResource == "lastUser") {

    $data = false;

    if ($requestMethod == "GET") {
        $data = getLastUser($db);
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
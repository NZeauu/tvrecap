<?php

/**
 * Filename: user.php
 * Author: Enzo Peigné
 * Description: Get the user's avatar and username
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

if($requestResource == "cookieCheck"){
    $data = false;

    if($requestMethod == "GET"){

        if(isset($_COOKIE['USERSESSION'])){
            $token = $_COOKIE['USERSESSION'];
            $res = getSessionExpiration($db, $token);

            if($res != false){
                $data = $res;
            }
        }
    }
}

if($requestResource == "avatar"){

    $data = false;

    if($requestMethod == "GET"){
        
        $token = $_COOKIE['USERSESSION'];

        $email = checkSessionToken($db, $token);

        if($email != false){
            $data = getAvatar($db, $email);
        }

    }

}

if($requestResource == "username"){

    $data = false;

    if($requestMethod == "GET"){
        
        $token = $_COOKIE['USERSESSION'];

        $email = checkSessionToken($db, $token);

        if($email != false){
            $data = getUsername($db, $email);
        }
    }
}

if($requestResource == "email"){

    $data = false;

    if($requestMethod == "GET"){
        
        $token = $_COOKIE['USERSESSION'];

        $email = checkSessionToken($db, $token);

        if($email != false){
            $data = $email;
        }
    }
}

if($requestResource == "disconnect"){
    $data = false;

    if($requestMethod == "GET"){

        $token = $_COOKIE['USERSESSION'];

        $data = removeSessionToken($db, null, $token);

        if($data){
            setcookie('USERSESSION', '', time() - 3600, '/', "tvrecap.epeigne.fr", true, true);
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
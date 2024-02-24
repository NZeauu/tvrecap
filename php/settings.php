<?php

/**
 * Filename: settings.php
 * Author: Enzo Peigné
 * Description: Display the user's information and update the user's information (username, password, birthday and avatar)
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

if($requestResource == "infos"){

    $data = false;

    if($requestMethod == "GET"){

        $userMail = $_GET['email'];

        $data = getUserInfo($db, $userMail);
    }
}

if($requestResource == "username"){
    $data = false;

    if($requestMethod == "PUT"){

        parse_str(file_get_contents('php://input'), $_PUT);

        $userMail = $_PUT['email'];
        $username = $_PUT['username'];

        $data = updateUsername($db, $userMail, $username);
    }

}

if($requestResource == "password"){
    $data = false;

    if($requestMethod == "PUT"){

        parse_str(file_get_contents('php://input'), $_PUT);

        $userMail = $_PUT['email'];
        $password = $_PUT['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $data = updatePassword($db, $userMail, $password);
    }

}

if($requestResource == "birthday"){
    $data = false;

    if($requestMethod == "PUT"){

        parse_str(file_get_contents('php://input'), $_PUT);

        $userMail = $_PUT['email'];
        $birthday = $_PUT['birthday'];

        $data = updateBirthday($db, $userMail, $birthday);
    }

}

if($requestResource == "avatar"){
    $data = false;

    if($requestMethod == "PUT"){

        parse_str(file_get_contents('php://input'), $_PUT);

        $userMail = $_PUT['email'];
        $avatar = $_PUT['avatar'];

        $data = updateAvatar($db, $userMail, $avatar);
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
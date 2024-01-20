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


if($requestResource == "moviedetails"){

    $data = false;
    
    if($requestMethod == "GET"){
        $movieId = $_GET['movieId'];

        $data = getMovieDetails($db, $movieId);

    }
}

if($requestResource == "checkwatchlist"){

    $data = false;
    
    if($requestMethod == "GET"){
        $movieId = $_GET['movieId'];
        $userMail = $_GET['userMail'];

        $data = checkMovieSeen($db, $userMail, $movieId);

    }
}

if($requestResource == "addwatchlist"){

    $data = false;
    
    if($requestMethod == "PUT"){

        parse_str(file_get_contents('php://input'), $_PUT);

        $movieId = $_PUT['movieId'];
        $userMail = $_PUT['userMail'];

        $data = addMovieSeen($db, $userMail, $movieId);

    }
}

if($requestResource == "removewatchlist"){

    $data = false;
    
    if($requestMethod == "DELETE"){

        parse_str(file_get_contents('php://input'), $_DELETE);

        $movieId = $_DELETE['movieId'];
        $userMail = $_DELETE['userMail'];

        $data = deleteMovieSeen($db, $userMail, $movieId);

    }
}



header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
if($requestMethod == 'POST'){
    header('HTTP/1.1 200 Created');
}elseif($requestMethod == 'DELETE'){
    header('HTTP/1.1 200 Deleted');
}
else{
    header('HTTP/1.1 200 OK');
}
echo json_encode($data);
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


if($requestResource == "seriedetails"){

    $data = false;
    
    if($requestMethod == "GET"){
        $serieId = $_GET['serieId'];

        $data = getSerieDetails($db, $serieId);

    }
}

if($requestResource == "checkfullywatched"){

    $data = false;
    
    if($requestMethod == "GET"){
        $serieId = $_GET['serieId'];
        $userMail = $_GET['userMail'];

        $data = checkSerieFullyWatched($db, $userMail, $serieId);

    }
}

if($requestResource == "addwatchlist"){

    $data = false;
    
    if($requestMethod == "POST"){
        $serieId = $_POST['serieId'];
        $userMail = $_POST['userMail'];

        $data = addSerieSeen($db, $userMail, $serieId);

    }
}

if($requestResource == "removewatchlist"){

    $data = false;
    
    if($requestMethod == "DELETE"){

        parse_str(file_get_contents('php://input'), $_DELETE);

        $serieId = $_DELETE['serieId'];
        $userMail = $_DELETE['userMail'];

        $data = deleteSerieSeen($db, $userMail, $serieId);

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
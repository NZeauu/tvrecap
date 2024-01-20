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

if($requestResource == "checkepwatched"){
        
        $data = false;
    
        if($requestMethod == "GET"){
            $serieId = $_GET['serieId'];
            $userMail = $_GET['userMail'];
            $episodeNumber = $_GET['episodeNumber'];
            $seasonNumber = $_GET['seasonNumber'];
    
            $data = checkEpisodeSeen($db, $userMail, $serieId, $episodeNumber, $seasonNumber);
    
        }
    
}

if($requestResource == "seasons"){

    $data = false;

    if($requestMethod == "GET"){
        $serieId = $_GET['serieId'];

        $data = getNumberOfSeasons($db, $serieId);

    }
}

if($requestResource == "seasonepisodes"){
    
    $data = false;

    if($requestMethod == "GET"){
        $serieId = $_GET['serieId'];
        $seasonNumber = $_GET['seasonNumber'];

        $data = getSeasonEpisodes($db, $serieId, $seasonNumber);

    }
}

if($requestResource == "updateepstatus"){

    $data = false;
    
    if($requestMethod == "PUT"){

        parse_str(file_get_contents('php://input'), $_PUT);

        $episodeNumber = $_PUT['episodeNumber'];
        $seasonNumber = $_PUT['seasonNumber'];
        $userMail = $_PUT['userMail'];
        $serieId = $_PUT['serieId'];
       
        $data = addEpisodeSeen($db, $userMail, $serieId, $episodeNumber, $seasonNumber);

    }

    if($requestMethod == "DELETE"){

        parse_str(file_get_contents('php://input'), $_DELETE);

        $episodeNumber = $_DELETE['episodeNumber'];
        $seasonNumber = $_DELETE['seasonNumber'];
        $userMail = $_DELETE['userMail'];
        $serieId = $_DELETE['serieId'];

        $data = deleteEpisodeSeen($db, $userMail, $serieId, $episodeNumber, $seasonNumber);

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
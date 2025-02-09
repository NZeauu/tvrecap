<?php

/**
 * Filename: serie-details.php
 * Author: Enzo Peigné
 * Description: Get the details of a serie, check if the serie is fully watched by the user, 
 *              Check if an episode is seen by the user, get the number of seasons of a serie, 
 *              Get the episodes of a season and update the status of an episode
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
    
    if($requestMethod == "POST"){

        // parse_str(file_get_contents('php://input'), $_PUT);

        $episodeNumber = $_POST['episodeNumber'];
        $seasonNumber = $_POST['seasonNumber'];
        $userMail = $_POST['userMail'];
        $serieId = $_POST['serieId'];

        if ($_POST['status'] == "not-seen") {
            $data = addEpisodeSeen($db, $userMail, $serieId, $episodeNumber, $seasonNumber);
        } elseif ($_POST['status'] == "seen") {
            $data = deleteEpisodeSeen($db, $userMail, $serieId, $episodeNumber, $seasonNumber);
        }
       
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
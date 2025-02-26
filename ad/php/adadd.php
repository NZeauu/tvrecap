<?php

/**
 * Filename: adadd.php
 * Author: Enzo Peigné
 * Description: Add a movie or a serie to the database using the Python scripts in the scripts folder
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

function sanitizeFilename($filename){
    $filename = mb_strtolower($filename);
    $filename = preg_replace('/[^a-z0-9]+/u', '-', $filename);
    $filename = trim($filename, '-');
    return $filename;
}

if($requestResource == "addmovie"){
    $data = false;

    if($requestMethod == 'POST'){
        $title = $_POST['title'];
        $year = $_POST['year'];
        $genre = $_POST['genre'];
        $synopsis = $_POST['synopsis'];
        $duration = $_POST['duration'];
        $realisator = $_POST['realisator'];
        $actors = $_POST['actors'];
        $coverURL = $_POST['coverURL'];

        $title = str_replace('"', '\"', $title);
        $synopsis = str_replace('"', '\"', $synopsis);
        $realisator = str_replace('"', '\"', $realisator);
        $actors = str_replace('"', '\"', $actors);
        
        
        $filename = $title . "-" . $year;
        $filename = sanitizeFilename($filename);
        $coverpath = "../img/Covers/movies/" . $filename . ".jpg";

        // Delete spaces in the URL
        $coverURL = str_replace(' ', '', $coverURL);

        // Save the image from the URL
        exec("python3 ../scripts/genImg.py " . escapeshellarg($coverURL) . " " . escapeshellarg($filename) . " movie");

        $data = addMovie($db, $title, $year, $genre, $synopsis, $duration, $realisator, $actors, $coverpath);
    }

}

if ($requestResource == "addserie"){
    $data = false;

    if ($requestMethod == "POST"){
        $title = $_POST['title'];
        $year = $_POST['year'];
        $genre = $_POST['genre'];
        $synopsis = $_POST['synopsis'];
        $realisator = $_POST['realisator'];
        $actors = $_POST['actors'];
        $coverURL = $_POST['coverURL'];
        $contentID = $_POST['contentID'];
        $nbSeasons = $_POST['nbSeasons'];

        $title = str_replace('"', '\"', $title);
        $synopsis = str_replace('"', '\"', $synopsis);
        $realisator = str_replace('"', '\"', $realisator);
        $actors = str_replace('"', '\"', $actors);

        $filename = $title . "-" . $year;
        $filename = sanitizeFilename($filename);
        $coverpath = "../img/Covers/series/" . $filename . ".jpg";

        // Delete spaces in the URL
        $coverURL = str_replace(' ', '', $coverURL);

        // Save the image from the URL
        exec("python3 ../scripts/genImg.py " . escapeshellarg($coverURL) . " " . escapeshellarg($filename) . " serie");

        $data = addSerie($db, $title, $year, $genre, $synopsis, $realisator, $actors, $coverpath, $nbSeasons);

        // Add all the episodes of the serie if it's not already in the database
        if ($data){

            $serieName = getLastSerie($db);

             // Add all the episodes of the serie
            exec("python3 ../scripts/getEpisodes.py " . escapeshellarg($contentID) . " " . escapeshellarg($serieName) . " 2>&1", $output, $return_var);
            // DEBUGGING
            // foreach ($output as $line) {
            //     echo $line . "\n";
            // }

            // echo "Code de retour: " . $return_var . "\n";
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
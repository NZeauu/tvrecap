<?php

/**
 * Filename: admanage.php
 * Author: Enzo Peigné
 * Description: Get the movies and series, the length of the movies and series, 
 *              Delete a movie or a serie and search a movie or a serie
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


if($requestResource == "getMoviesLength"){
    $data = false;

    if($requestMethod == 'GET'){
        $data = getMoviesLength($db);
    }
}

if($requestResource == "getSeriesLength"){
    $data = false;

    if($requestMethod == 'GET'){
        $data = getSeriesLength($db);
    }
}


if($requestResource == "getMovies"){
    $data = false;

    if($requestMethod == 'GET'){

        $maxRow = $_GET['maxRow'];
        $sorting = $_GET['sorting'];

        $data = getMovies($db, $maxRow, $sorting);
    }
}

if($requestResource == "getSeries"){
    $data = false;

    if($requestMethod == 'GET'){

        $maxRow = $_GET['maxRow'];
        $sorting = $_GET['sorting'];

        $data = getSeries($db, $maxRow, $sorting);
    }
}

if($requestResource == "deleteMovie"){
    $data = false;

    if($requestMethod == 'POST'){
        $id = $_POST['id'];

        $data = deleteMovie($db, $id);
    }
}

if($requestResource == "deleteSerie"){
    $data = false;

    if($requestMethod == 'POST'){
        $id = $_POST['id'];

        $data = deleteSerie($db, $id);
    }
}

if($requestResource == "searchMovie"){
    $data = false;

    if($requestMethod == 'GET'){
        $value = $_GET['value'];

        $data = searchMovie($db, $value);
    }
}

if($requestResource == "searchSerie"){
    $data = false;

    if($requestMethod == 'GET'){
        $value = $_GET['value'];

        $data = searchSerie($db, $value);
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
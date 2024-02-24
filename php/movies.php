<?php

/**
 * Filename: movies.php
 * Author: Enzo Peigné
 * Description: Get all the movies, the movies filtered by category, duration and year and the length of the movies
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

if($requestResource == "all"){
    
    $data = false;

    if($requestMethod == "GET"){

        $sorting = $_GET['sorting'];
        $maxrow = $_GET['maxrow'];

        $data = getAllMovies($db, $sorting, $maxrow);
    }

}

if($requestResource == "filtered"){
    
    $data = false;

    if($requestMethod == "GET"){

        $category = $_GET['category'];
        $duration = $_GET['duration'];
        $year = $_GET['year'];
        $sorting = $_GET['sorting'];
        $maxrow = $_GET['maxrow'];
        
        $data = getFilteredMovies($db, $category, $duration, $year, $sorting, $maxrow);
    }
}

if($requestResource == "length"){
    
    $data = false;

    if($requestMethod == "GET"){
        $data = getMoviesLength($db);
    }
}

if($requestResource == "filteredLen"){
        
        $data = false;
    
        if($requestMethod == "GET"){
    
            $category = $_GET['category'];
            $duration = $_GET['duration'];
            $year = $_GET['year'];
            
            $data = getFilteredMoviesLength($db, $category, $duration, $year);
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
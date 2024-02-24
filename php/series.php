<?php

/**
 * Filename: series.php
 * Author: Enzo Peigné
 * Description: Get all the series, the series filtered by category, duration, year and the length of the series (filtered or not)
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
        $maxrow = $_GET['maxRow'];
        
        $data = getAllSeries($db, $sorting, $maxrow);

    }
}

if($requestResource == "duration"){

    $data = false;
    
    if($requestMethod == "GET"){

        $id_serie = $_GET['id_serie'];
        
        $data = getAverageDuration($db, $id_serie);

    }
}

if($requestResource == "filtered"){
        
        $data = false;
        
        if($requestMethod == "GET"){
    
            $seasons = $_GET['seasons'];
            $category = $_GET['category'];
            $duration = $_GET['duration'];
            $year = $_GET['year'];
            $sorting = $_GET['sorting'];
            $maxrow = $_GET['maxRow'];

            $data = getFilteredSeries($db, $seasons, $category, $duration, $year, $sorting, $maxrow);
    
        }
    
}

if($requestResource == "length"){
        
    $data = false;
    
    if($requestMethod == "GET"){

        $data = getSeriesLength($db);

    }

}

if($requestResource == "filteredLen"){
            
        $data = false;
        
        if($requestMethod == "GET"){
    
            $seasons = $_GET['seasons'];
            $category = $_GET['category'];
            $duration = $_GET['duration'];
            $year = $_GET['year'];
    
            $data = getFilteredSeriesLength($db, $seasons, $category, $duration, $year);
    
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
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

if($requestResource == "all"){

    $data = false;
    
    if($requestMethod == "GET"){
        
        $data = getAllSeries($db);

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

            $data = getFilteredSeries($db, $seasons, $category, $duration, $year);
    
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
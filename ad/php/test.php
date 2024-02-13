<?php

// Enable all warnings and errors

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check request
$requestMethod = $_SERVER['REQUEST_METHOD'];
// $request =substr($_SERVER['PATH_INFO'], 1);
// $request = explode('/', $request);
// $requestResource = array_shift($request);

$output = false;
if($requestMethod == 'GET'){
    $title = $_GET['title'];
    $year = $_GET['year'];
    $type = $_GET['type'];

    $command = 'python3 ../scripts/searchMov.py';

    $output = exec($command . ' ' . $type . ' ' . $title . ' ' . $year);
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
if($requestMethod == 'POST'){
    header('HTTP/1.1 200 Created');
}else{
    header('HTTP/1.1 200 OK');
}
echo $output;

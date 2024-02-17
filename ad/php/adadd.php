<?php
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

// caracters to remove from filename
$caractersToRemove = array(' ', '\'', '\"', '(', ')', '?', '!', ':', ';', ',', '.', '*');

if($requestResource == "saveimg"){

    $data = false;

    if($requestMethod == 'POST' && isset($_FILES["file"]["name"])){

        $title = $_POST['title'];
        $year = $_POST['year'];

        $targetDirectory = "../../img/Covers/";
        $filename = $title . "-" . $year;
        $filename = str_replace($caractersToRemove, '', $filename);
        $filename = strtolower($filename);
        $targetFile = $targetDirectory . $filename . ".jpg";

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
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
        $filename = str_replace(' ', '', $filename);
        $filename = strtolower($filename);
        $coverpath = "../img/Covers/movies/" . $filename . ".jpg";

        // Delete spaces in the URL
        $coverURL = str_replace(' ', '', $coverURL);

        exec("python3 ../scripts/genImg.py " . escapeshellarg($coverURL) . " " . escapeshellarg($filename) . " movie");

        $data = addMovie($db, $title, $year, $genre, $synopsis, $duration, $realisator, $actors, $coverpath);
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
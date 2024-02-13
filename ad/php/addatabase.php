<?php

include '../../php/constants.php';


// Connect to the database "OK" 
function dbconnect() {
    // Create a connection to the database
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// ------------------------------------------------------
// -------------------- HOME PAGE -----------------------
// ------------------------------------------------------

// Get the number of users in the database "OK"
function getNumUsers($conn) {
    $sql = "SELECT COUNT(*) FROM Accounts WHERE administrator IS NULL";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    return $row['COUNT(*)'];
}

// Get the last user registered in the database "OK"
function getLastUser($conn) {
    $sql = "SELECT username FROM Accounts WHERE administrator IS NULL ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    return $row['username'];
}

// ------------------------------------------------------
// --------------------- ADD PAGE -----------------------
// ------------------------------------------------------

// Add a movie to the database "OK"
function addMovie($db, $title, $year, $genre, $synopsis, $duration, $realisator, $actors, $coverpath) {
    try {
        $sql = "INSERT INTO Films (nom, image, duree, date_sortie, categorie, synopsis, actors, realisator) VALUES (\"$title\", \"$coverpath\", \"$duration\", \"$year\", \"$genre\", \"$synopsis\", \"$actors\", \"$realisator\")";
        $db->query($sql);
        $db->close();
        return true;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
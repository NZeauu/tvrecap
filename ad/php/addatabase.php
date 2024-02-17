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

// Check if the content is already in the database "OK"
function checkContent($conn, $title, $year, $type) {
    if ($type == "movie") {
        $sql = "SELECT COUNT(*) FROM Films WHERE nom = ? AND date_sortie = ?";
    }
    if ($type == "serie"){
        $sql = "SELECT COUNT(*) FROM Séries WHERE nom = ? AND date_sortie = ?";
    }

    try{
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $title, $year);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    
    }

}

// Add a movie to the database "OK"
function addMovie($db, $title, $year, $genre, $synopsis, $duration, $realisator, $actors, $coverpath) {

    // Check if the movie is already in the database before adding it
    if (checkContent($db, $title, $year, "movie") > 0) {
        return "already exists";
    }

    try {
        $sql = "INSERT INTO Films (nom, image, duree, date_sortie, categorie, synopsis, actors, realisator) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssisssss", $title, $coverpath, $duration, $year, $genre, $synopsis, $actors, $realisator);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// add a serie to the database "OK"
function addSerie($db, $title, $year, $genre, $synopsis, $realisator, $actors, $coverpath, $seasons) {

    // Check if the serie is already in the database before adding it
    if (checkContent($db, $title, $year, "serie") > 0) {
        return "already exists";
    }

    try {
        $sql = "INSERT INTO Séries (nom, nb_saisons, date_sortie, categorie, synopsis, actors, realisator, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("siisssss", $title, $seasons, $year, $genre, $synopsis, $actors, $realisator, $coverpath);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// ------------------------------------------------------
// -------------------- USERS PAGE ----------------------
// ------------------------------------------------------

// Get the list of users in the database "OK"
function getUsers($conn) {
    try{
        $sql = "SELECT * FROM Accounts WHERE administrator IS NULL";
        $result = $conn->query($sql);
        $conn->close();

        if ($result->num_rows > 0) {
            $users = array();
            while($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        } else {
            return "No users found";
        }
        

    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Delete a user from the database "OK"
function deleteUser($conn, $email) {
    try{
        $sql = "DELETE FROM Accounts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Update a user in the database "OK"
function updateUser($conn, $userId, $email, $username) {
    try{
        $sql = "UPDATE Accounts SET email = ?, username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $email, $username, $userId);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
<?php

/**
 * Filename: addatabase.php
 * Author: Enzo Peigné
 * Description: Functions to interact with the database for the administator
 *              Divided into 4 parts: home page, add page, users page and content page
 */

include '../../php/constants.php';


/**
 * Function to connect to the database
 * 
 * @return mysqli The connection to the database
 */
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

/**
 * Function to get the number of users registered in the database
 * 
 * @param mysqli $conn The connection to the database
 * 
 * @return int Return the number of users registered in the database
 */
function getNumUsers($conn) {
    $sql = "SELECT COUNT(*) FROM Accounts WHERE administrator IS NULL";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    return $row['COUNT(*)'];
}

/**
 * Function to get the last user registered in the database
 * 
 * @param mysqli $conn The connection to the database
 * 
 * @return int Return the username of the last user registered in the database
 */
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

/**
 * Function to check if a movie or a serie is already in the database
 * 
 * @param mysqli $conn The connection to the database
 * @param string $title The title of the movie or serie
 * @param int $year The year of release of the movie or serie
 * @param string $type The type of content (movie or serie)
 * 
 * @return int Return the number of content found in the database
 */
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

/**
 * Function to add a movie to the database
 * 
 * @param mysqli $conn The connection to the database
 * @param string $title The title of the movie
 * @param int $year The year of release of the movie
 * @param string $genre The genre of the movie
 * @param string $synopsis The synopsis of the movie
 * @param int $duration The duration in minutes of the movie
 * @param string $realisator The realisator of the movie
 * @param string $actors The actors of the movie
 * @param string $coverpath The path to the cover of the movie
 * 
 * @return bool|string Return true if the movie has been added to the database, 
 *                     Returns "already exists" if the movie is already in the database and false if an error occured
 */
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

/**
 * Function to add a serie to the database
 * 
 * @param mysqli $conn The connection to the database
 * @param string $title The title of the serie
 * @param int $year The year of release of the serie
 * @param string $genre The genre of the serie
 * @param string $synopsis The synopsis of the serie
 * @param string $realisator The realisator of the serie
 * @param string $actors The actors of the serie
 * @param string $coverpath The path to the cover of the serie
 * @param int $seasons The number of seasons of the serie
 * 
 * @return bool|string Return true if the serie has been added to the database, 
 *                     Returns "already exists" if the serie is already in the database and false if an error occured
 */
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

/**
 * Get the list of users in the database
 * 
 * @param mysqli $conn The connection to the database
 * 
 * @return array|string Return the list of users in the database or "No users found" if no users are found
 */
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

/**
 * Delete a user from the database
 * 
 * @param mysqli $conn The connection to the database
 * @param  string $email The email of the user to delete
 * 
 * @return bool Return true if the user has been deleted from the database, false if an error occured
 */
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

/**
 * Update the information of a user in the database
 * 
 * @param mysqli $conn The connection to the database
 * @param int $userId The id of the user to update
 * @param string $email The new email of the user
 * @param string $username The new username of the user
 * 
 * @return bool Return true if the user has been updated in the database, false if an error occured
 */
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

// ------------------------------------------------------
// ------------------ CONTENT PAGE ----------------------
// ------------------------------------------------------

/**
 * Get the amount of movies in the database
 * 
 * @param mysqli $conn The connection to the database
 * 
 * @return int Return the amount of movies in the database
 */
function getMoviesLength($conn) {
    try{
        $sql = "SELECT COUNT(*) FROM Films";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        return $row['COUNT(*)'];
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Get the amount of series in the database
 * 
 * @param mysqli $conn The connection to the database
 * 
 * @return int Return the amount of series in the database
 */
function getSeriesLength($conn) {
    try{
        $sql = "SELECT COUNT(*) FROM Séries";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        return $row['COUNT(*)'];
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


/**
 * Get the list of movies in the database
 * 
 * @param mysqli $conn The connection to the database
 * @param int $maxRow The row to start from in the database
 * @param string $sorting The sorting method to use
 * 
 * @return array|string Return the list of movies in the database or "No movies found" if no movies are found
 */
function getMovies($conn, $maxRow, $sorting) {
    try{
        $sql = "SELECT * FROM Films ORDER BY $sorting LIMIT ?, 25";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maxRow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $movies = array();
            while($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }
            return $movies;
        } else {
            return "No movies found";
        }
        

    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Get the list of series in the database
 * 
 * @param mysqli $conn The connection to the database
 * @param int $maxRow The row to start from in the database
 * @param string $sorting The sorting method to use
 * 
 * @return array|string Return the list of series in the database or "No series found" if no series are found
 */
function getSeries($conn, $maxRow, $sorting) {
    try{
        $sql = "SELECT * FROM Séries ORDER BY $sorting LIMIT ?, 25";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maxRow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $series = array();
            while($row = $result->fetch_assoc()) {
                $series[] = $row;
            }
            return $series;
        } else {
            return "No series found";
        }
        

    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Delete a movie from the database
 * 
 * @param mysqli $conn The connection to the database
 * @param int $id The id of the movie to delete
 * 
 * @return bool Return true if the movie has been deleted from the database, false if an error occured
 */
function deleteMovie($conn, $id) {
    try{
        $sql = "DELETE FROM Films WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Delete a serie from the database
 * 
 * @param mysqli $conn The connection to the database
 * @param int $id The id of the serie to delete
 * 
 * @return bool Return true if the serie has been deleted from the database, false if an error occured
 */
function deleteSerie($conn, $id) {
    try{
        deleteEpisodes($conn, $id);
        $sql = "DELETE FROM Séries WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Get the list of episodes of a serie
 * 
 * @param mysqli $conn The connection to the database
 * @param int $id The id of the serie
 * 
 * @return array|string Return the list of episodes of the serie or "No episodes found" if no episodes are found
 */
function deleteEpisodes($conn, $id) {
    try{
        $sql = "DELETE FROM Episodes WHERE serie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        return true;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Search for a movie by its title in the database
 * 
 * @param mysqli $conn The connection to the database
 * @param string $value The title of the movie to search
 * 
 * @return array|string Return the list of movies found in the database or "No movies found" if no movies are found
 */
function searchMovie($conn, $value) {
    try{
        $value = "%".$value."%";
        $sql = "SELECT * FROM Films WHERE nom LIKE ? ORDER BY nom ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $movies = array();
            while($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }
            return $movies;
        } else {
            return "No movies found";
        }
        

    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

/**
 * Search for a serie by its title in the database
 * 
 * @param mysqli $conn The connection to the database
 * @param string $value The title of the serie to search
 * 
 * @return array|string Return the list of series found in the database or "No series found" if no series are found
 */
function searchSerie($conn, $value) {
    try{
        $value = "%".$value."%";
        $sql = "SELECT * FROM Séries WHERE nom LIKE ? ORDER BY nom ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $series = array();
            while($row = $result->fetch_assoc()) {
                $series[] = $row;
            }
            return $series;
        } else {
            return "No series found";
        }
        

    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
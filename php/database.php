<?php

include 'constants.php';

// Connect to the database
function dbconnect() {
    // Create a connection to the database
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// ------------------------------------ //
// -------------- USERS --------------- //
// ------------------------------------ //

// User connection
function connectionAccount($conn, $mail, $password) {

    try{
        $sql = "SELECT password FROM Accounts WHERE email = '$mail'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password"])) {
                return true;
            } else {
                return false;
            }
        }
        else {
            return "Email not found";
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    } 
}

// User registration
function registerAccount($conn, $username, $email, $password){
    try{
        $sql = "INSERT INTO Accounts (username, email, password) VALUES ('$username', '$email', '$password')";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Check if the email is already used
function checkMail($conn, $email){
    try{
        $sql = "SELECT * FROM Accounts WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Check if the username is already used
function checkUsername($conn, $username){
    try{
        $sql = "SELECT * FROM Accounts WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get the user name from the email
function getUsername($conn, $email){
    try{
        $sql = "SELECT username FROM Accounts WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["username"];
        }
        else {
            return "User not found";
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


// ------------------------------------ //
// -------------- SERIES -------------- //
// ------------------------------------ //



// ------------------------------------ //
// -------------- EPISODES ------------ //
// ------------------------------------ //




// ------------------------------------ //
// -------------- MOVIES -------------- //
// ------------------------------------ //

// Get all the movies
function getAllMovies($conn){
    try{
        $sql = "SELECT * FROM Films";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $movies = array();

            while($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }

            return $movies;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

// Get the movie cover
// We get back the blob of the cover
// We need to convert it to an image
function getMovieCover($conn, $id_cover){
    try{
        $sql = "SELECT image FROM Covers WHERE id = '$id_cover'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["image"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get filtered movies
function getFilteredMovies($conn, $category, $duration, $year){
    try{

        // If the user didn't select a category, we select all the categories
        if ($category != "all"){
            $category = "categorie LIKE '%$category%'";
        }
        else{
            $category = "1";
        }

        // If the user didn't select a duration, we select all the durations
        if ($duration != "all"){

            // Duration less than 1 hour
            if ($duration == "60"){
                $duration = "duree <= '60'";
            }

            // Duration between 1 hour and 2 hours
            else if ($duration == "120"){
                $duration = "duree > '60' AND duree <= '120'";
            }

            // Duration more than 2 hours
            else if ($duration == "999"){
                $duration = "duree > '120'";
            }
        }
        else{
            $duration = "1";
        }

        // If the user didn't select a year, we select all the years
        if ($year != "all"){

            // Get actual year
            $actual_year = date("Y");

            $min_year = $actual_year - 51;

            if ($year == $min_year){
                $year = "date_sortie <= '$min_year'";
            }
            else{
                $year = "date_sortie = '$year'";
            }        

        }
        else{
            $year = "1";
        }

        $sql = "SELECT * FROM Films WHERE $category AND $duration AND $year";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $movies = array();

            while($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }

            return $movies;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}



// ------------------------------------ //
// ------------ SEEN LIST ------------- //
// ------------------------------------ //

// Get the total time of the seen list
function getTotalTime($conn, $email){
    try{
        $sql = "SELECT `Accounts`.`email` AS `User_Email`,
                SUM(
                    CASE
                        WHEN `Seen_List`.`type` = 'movie' THEN `Films`.`duree`
                        WHEN `Seen_List`.`type` = 'serie' THEN `Episodes`.`duree`
                        ELSE 0
                    END
                ) AS `Total_Duration_Seen`
                FROM
                    `Seen_List`
                JOIN
                    `Accounts` ON `Seen_List`.`user_id` = `Accounts`.`id`
                LEFT JOIN
                    `Films` ON `Seen_List`.`movie_id` = `Films`.`id`
                LEFT JOIN
                    `Episodes` ON `Seen_List`.`episode_id` = `Episodes`.`id`
                WHERE
                    `Accounts`.`email` = '$email'
                GROUP BY
                    `Accounts`.`email`;"; 

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["Total_Duration_Seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get the total number of movies watched by the user
function getMoviesWatched($conn, $email){
    try{
        $sql = "SELECT `Accounts`.`email` AS `User_Email`,
                COUNT(
                    CASE
                        WHEN `Seen_List`.`type` = 'movie' THEN `Films`.`id`
                        ELSE NULL
                    END
                ) AS `Total_Movies_Seen`
                FROM
                    `Seen_List`
                JOIN
                    `Accounts` ON `Seen_List`.`user_id` = `Accounts`.`id`
                LEFT JOIN
                    `Films` ON `Seen_List`.`movie_id` = `Films`.`id`
                WHERE
                    `Accounts`.`email` = '$email'
                GROUP BY
                    `Accounts`.`email`;"; 

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["Total_Movies_Seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get the total number of episodes watched by the user
function getEpisodesWatched($conn, $email){
    try{
        $sql = "SELECT `Accounts`.`email` AS `User_Email`,
                COUNT(
                    CASE
                        WHEN `Seen_List`.`type` = 'serie' THEN `Episodes`.`id`
                        ELSE NULL
                    END
                ) AS `Total_Episodes_Seen`
                FROM
                    `Seen_List`
                JOIN
                    `Accounts` ON `Seen_List`.`user_id` = `Accounts`.`id`
                LEFT JOIN
                    `Episodes` ON `Seen_List`.`episode_id` = `Episodes`.`id`
                WHERE
                    `Accounts`.`email` = '$email'
                GROUP BY
                    `Accounts`.`email`;"; 

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["Total_Episodes_Seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
<?php

include 'constants.php';

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

// ------------------------------------ //
// -------------- USERS --------------- //
// ------------------------------------ //

// User connection "OK"
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

// User registration "OK"
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

// Check if the email is already used "OK"
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

// Check if the username is already used "OK"
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

// Get the user name from the email "OK"
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

// Get the user's avatar from the email "OK"
function getAvatar($conn, $email){
    try{
        $sql = "SELECT avatar FROM Accounts WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["avatar"];
        }
        else {
            return "User not found";
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get user's information "OK"
function getUserInfo($conn, $email){
    try{
        $sql = "SELECT * FROM Accounts WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
        }
        else {
            return "User not found";
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Update user's username "OK"
function updateUsername($conn, $email, $username){
    try{
        $sql = "UPDATE Accounts SET username = '$username' WHERE email = '$email'";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Update user's birthday "OK"
function updateBirthday($conn, $email, $birthday){
    try{
        $sql = "UPDATE Accounts SET birthday = '$birthday' WHERE email = '$email'";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Update user's password "OK"
function updatePassword($conn, $email, $password){
    try{
        $sql = "UPDATE Accounts SET password = '$password' WHERE email = '$email'";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Update user's avatar "OK"
function updateAvatar($conn, $email, $avatar){
    try{
        $sql = "UPDATE Accounts SET avatar = '$avatar' WHERE email = '$email'";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


// ------------------------------------ //
// -------------- SERIES -------------- //
// ------------------------------------ //

// Get all the series "OK"
function getAllSeries($conn){
    try{
        $sql = "SELECT * FROM Séries";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $series = array();

            while($row = $result->fetch_assoc()) {
                $series[] = $row;
            }

            return $series;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

// Get filtered series "OK"
function getFilteredSeries($conn, $seasons, $category, $duration, $year){
    try{

        // If the user didn't select a number of seasons, we select all the seasons
        if ($seasons != "all"){

            // Number of seasons less than 2
            if ($seasons == "2"){
                $seasons = "nb_saisons <= '2'";
            }

            // Number of seasons between 2 and 5
            else if ($seasons == "5"){
                $seasons = "nb_saisons > '2' AND nb_saisons <= '5'";
            }

            // Number of seasons more than 5
            else if ($seasons == "999"){
                $seasons = "nb_saisons > '5'";
            }
        }
        else{
            $seasons = "1";
        }

        // If the user didn't select a duration, we select all the durations
        if ($duration != "all"){

            // Duration less than 30 min
            if ($duration == "30"){
                $duration = "id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) <= 30)";
            }

            // Duration between 30 min and 1 hour
            else if ($duration == "60"){
                $duration = "id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) > 30 AND AVG(duree) <= 60)";
            }

            // Duration more than 1 hours
            else if ($duration == "999"){
                $duration = "id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) > 60)";
            }
        }
        else{
            $duration = "1";
        }

        // If the user didn't select a category, we select all the categories
        if ($category != "all"){
            $category = "categorie LIKE '%$category%'";
        }
        else{
            $category = "1";
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

        $sql = "SELECT * FROM Séries WHERE $seasons AND $category AND $duration AND $year";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $series = array();

            while($row = $result->fetch_assoc()) {
                $series[] = $row;
            }

            return $series;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

// Get average duration of the episodes of a serie "OK"
function getAverageDuration($conn, $id_serie){
    try{
        $sql = "SELECT AVG(duree) AS average_duration FROM Episodes WHERE serie_id = '$id_serie'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["average_duration"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Filter the series by average duration of the episodes "OK"
function filterSeriesByAverageDuration($conn, $average_duration){
    try{

        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $series = array();

            while($row = $result->fetch_assoc()) {
                $series[] = $row;
            }

            return $series;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get the serie details "OK"
function getSerieDetails($db, $serieId){
    try{
        $sql = "SELECT * FROM Séries WHERE id = '$serieId'";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get the number of seasons of a serie "OK"
function getNumberOfSeasons($conn, $id_serie){
    try{
        $sql = "SELECT nb_saisons FROM Séries WHERE id = '$id_serie'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["nb_saisons"];
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
// -------------- EPISODES ------------ //
// ------------------------------------ //

// Get all the episodes of a serie "OK"
function getSeasonEpisodes($conn, $id_serie, $season){
    try{
        $sql = "SELECT * FROM Episodes WHERE serie_id = '$id_serie' AND saison = '$season'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $episodes = array();

            while($row = $result->fetch_assoc()) {
                $episodes[] = $row;
            }

            return $episodes;
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
// -------------- MOVIES -------------- //
// ------------------------------------ //

// Get all the movies "OK"
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

// Get filtered movies "OK"
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

// Get the movie details "OK"
function getMovieDetails($conn, $id_movie){
    try{
        $sql = "SELECT * FROM Films WHERE id = '$id_movie'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
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

// Get the total time of the seen list "OK"
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

// Get the total number of movies watched by the user "OK"
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

// Get the total number of episodes watched by the user "OK"
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

// Check if the movie is in the seen list "OK"
function checkMovieSeen($conn, $email, $id_movie){
    try{
        $sql = "SELECT * FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND movie_id = '$id_movie'";
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

// Add the movie to the seen list "OK"
function addMovieSeen($conn, $email, $id_movie){
    try{
        $sql = "INSERT INTO Seen_List (user_id, type, movie_id, episode_id) VALUES ((SELECT id FROM Accounts WHERE email = '$email'), 'movie', '$id_movie', null)";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Delete the movie from the seen list "OK"
function deleteMovieSeen($conn, $email, $id_movie){
    try{
        $sql = "DELETE FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND movie_id = '$id_movie'";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Check if a serie is fully watched "OK"
function checkSerieFullyWatched($conn, $email, $id_serie){
    try{
        $sql = "SELECT * FROM Episodes WHERE id NOT IN (SELECT episode_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND type = 'serie') AND serie_id = '$id_serie';";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            return false;
        }
        else {
            return true;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Check if the episode is in the seen list "OK"
function checkEpisodeSeen($conn, $email, $serieId, $episodeNumber, $seasonNumber){
    try{
        $sql = "SELECT * FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND episode_id = (SELECT id FROM Episodes WHERE serie_id = '$serieId' AND num_ep = '$episodeNumber' AND saison = '$seasonNumber')";
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

// Add the episode to the seen list "OK"
function addEpisodeSeen($conn, $email, $serieId, $episodeNumber, $seasonNumber){
    try{
        $sql = "INSERT INTO Seen_List (user_id, type, movie_id, episode_id) VALUES ((SELECT id FROM Accounts WHERE email = '$email'), 'serie', null, (SELECT id FROM Episodes WHERE serie_id = '$serieId' AND num_ep = '$episodeNumber' AND saison = '$seasonNumber'))";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Delete the episode from the seen list "OK"
function deleteEpisodeSeen($conn, $email, $serieId, $episodeNumber, $seasonNumber){
    try{
        $sql = "DELETE FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND episode_id = (SELECT id FROM Episodes WHERE serie_id = '$serieId' AND num_ep = '$episodeNumber' AND saison = '$seasonNumber')";
        $conn->query($sql);

        return true;
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Get the list of movies seen by the user "OK"
function getMoviesSeen($conn, $email){
    try{
        $sql = "SELECT * FROM Films WHERE id IN (SELECT movie_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND type = 'movie')";
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

// Get the list of series seen by the user "OK"
function getSeriesSeen($conn, $email){
    try{
        $sql = "SELECT * FROM Séries WHERE id IN (SELECT serie_id FROM Episodes WHERE id IN (SELECT episode_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND type = 'serie'))";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $series = array();

            while($row = $result->fetch_assoc()) {
                $series[] = $row;
            }

            return $series;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

// Get the number of episodes of a serie seen by the user "OK"
function getNumberOfEpisodesSeen($conn, $email, $id_serie){
    try{
        $sql = "SELECT COUNT(*) AS nb_episodes_seen FROM Episodes WHERE id IN (SELECT episode_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = '$email') AND type = 'serie') AND serie_id = '$id_serie'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["nb_episodes_seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}
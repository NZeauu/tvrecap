<?php

/**
 * Filename: database.php
 * Author: Enzo Peigné
 * Description: Database functions to connect to the database and to get the data from the database
 *              The file is divided into 5 parts: USERS, SERIES, EPISODES, MOVIES, SEEN LIST
 *              Each part contains the functions to get the data from the database
 */

include 'constants.php';

/**
 * Connect to the database
 *  
 * @return mysqli Returns the database connection object
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

// ------------------------------------ //
// -------------- USERS --------------- //
// ------------------------------------ //


/**
 * Connect the user to his account
 * 
 * @param mysqli $conn Database connection object
 * @param string $mail User's email
 * @param string $password User's password
 * 
 * @return string|boolean Returns the user's informations if the connection is successful,
 *                        Returns false if the password is incorrect, 
 *                        Returns "Email not found" if the email is not in the database
 */
function connectionAccount($conn, $mail, $password) {

    try{
        $sql = "SELECT * FROM Accounts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $mail);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password"])) {
                return $row["administrator"]? "admin" : "user";
            } else {
                return false;
            }
        }
        else {
            return "Email not found";
        } 
    }
    catch(PDOException $e) {
        return false;
    } 
}

/**
 * Register a new account
 * 
 * @param mysqli $conn Database connection object
 * @param string $username User's username
 * @param string $email User's email
 * @param string $password User's password
 * 
 * @return boolean Returns true if the registration is successful, false otherwise
 */
function registerAccount($conn, $username, $email, $password){
    try{
        $sql = "INSERT INTO Accounts (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Check if the email is already used
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return boolean Returns true if the email is already used, false otherwise
 */
function checkMail($conn, $email){
    try{
        $sql = "SELECT * FROM Accounts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Check if the username is already used
 * 
 * @param mysqli $conn Database connection object
 * @param string $username User's username
 * 
 * @return boolean Returns true if the username is already used, false otherwise
 */
function checkUsername($conn, $username){
    try{
        $sql = "SELECT * FROM Accounts WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the username from the email
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return string Returns the user's username if the email is found, "User not found" otherwise
 */
function getUsername($conn, $email){
    try{
        $sql = "SELECT username FROM Accounts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["username"];
        }
        else {
            return "User not found";
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the user's avatar
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return string Returns the user's avatar filepath if the email is found, "User not found" otherwise
 */
function getAvatar($conn, $email){
    try{
        $sql = "SELECT avatar FROM Accounts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["avatar"];
        }
        else {
            return "User not found";
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the user's information
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return array|string Returns the user's information if the email is found, "User not found" otherwise
 */
function getUserInfo($conn, $email){
    try{
        $sql = "SELECT * FROM Accounts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
        }
        else {
            return "User not found";
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Update the user's username
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $username User's username
 * 
 * @return boolean Returns true if the username is updated, false otherwise
 */
function updateUsername($conn, $email, $username){
    try{
        $sql = "UPDATE Accounts SET username = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Update the user's birthday
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $birthday User's birthday
 * 
 * @return boolean Returns true if the birthday is updated, false otherwise
*/
function updateBirthday($conn, $email, $birthday){
    try{
        $sql = "UPDATE Accounts SET birthday = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $birthday, $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Update the user's password
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $password User's password
 * 
 * @return boolean Returns true if the password is updated, false otherwise
 */
function updatePassword($conn, $email, $password){
    try{
        $sql = "UPDATE Accounts SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Update the user's avatar
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $avatar User's avatar filepath
 * 
 * @return boolean Returns true if the avatar is updated, false otherwise
 */
function updateAvatar($conn, $email, $avatar){
    try{
        $sql = "UPDATE Accounts SET avatar = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $avatar, $email);
        $stmt->execute();
        
        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Insert a token and an expiration date in the database for the password reset
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $token User's token generated for the password reset
 * @param string $expiration_date Token's expiration date
 * 
 * @return boolean Returns true if the token is inserted, false otherwise
 */
function insertToken($conn, $email, $token, $expiration_date){
    try{
        $sql = "UPDATE Accounts
                SET reset_token_hash = ?, reset_token_expiration = ?
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiration_date, $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Check if the token is in the database and if the expiration date is not passed
 * 
 * @param mysqli $conn Database connection object
 * @param string $token User's token generated for the password reset
 * 
 * @return string|boolean Returns the user's email if the token is in the database and the expiration date is not passed,
 *                        Returns false otherwise and remove the token from the database
 */
function checkToken($conn, $token){
    try{
        $sql = "SELECT * FROM Accounts 
                WHERE reset_token_hash = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            if($user['reset_token_expiration'] > date("Y-m-d H:i:s", time())){
                return $user['email'];
            }
            else{
                removeToken($conn, $user['email']);
                return false;
            }
        }
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Reset the user's password
 * 
 * @param mysqli $conn Database connection object
 * @param string $token User's token generated for the password reset
 * @param string $password User's new password
 * @param string $email User's email
 * 
 * @return boolean Returns true if the password is reset, false otherwise
 */
function resetPass($conn, $token, $password, $email){
    try{
        $sql = "UPDATE Accounts
                SET password = ?
                WHERE reset_token_hash = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $password, $token);
        $stmt->execute();

        removeToken($conn, $email);

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Remove the token from the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return boolean Returns true if the token is removed, false otherwise
 */
function removeToken($conn, $email){
    try{
        $sql = "UPDATE Accounts
                SET reset_token_hash = NULL, reset_token_expiration = NULL
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Insert a token in the database for the account verification
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $token User's token generated for the account verification
 * 
 * @return boolean Returns true if the token is inserted, false otherwise
 */

function insertVerifToken($conn, $email, $token){
    try{
        $sql = "UPDATE Accounts
                SET verification_token = ?
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Check if the user's account is verified
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return boolean Returns true if the user's account is verified, false otherwise
 */
function checkVerified($conn, $email){
    try{
        $sql = "SELECT * FROM Accounts 
                WHERE email = ? AND verified = 1";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Check the user's token for the account verification
 * 
 * @param mysqli $conn Database connection object
 * @param string $token User's token generated for the account verification
 * 
 * @return boolean Returns true if the token is in the database, false otherwise
 */
function checkVerifToken($conn, $token){
    try{
        $sql = "SELECT * FROM Accounts 
                WHERE verification_token = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Verify the user's account
 * 
 * @param mysqli $conn Database connection object
 * @param string $token User's token generated for the account verification
 * 
 * @return boolean Returns true if the account is verified, false otherwise
 */
function verifAccount($conn, $token){
    try{
        $sql = "UPDATE Accounts
                SET verified = 1, verification_token = NULL
                WHERE verification_token = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Insert the session token in the database once the user is connected
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $token User's session token
 * @param string $expiration_date Token's expiration date
 * 
 * @return boolean Returns true if the session token is inserted, false otherwise
 */
function insertSessionToken($conn, $email, $token, $expiration_date){
    try{
        $sql = "UPDATE Accounts
                SET session_token = ?, session_expiration = ?
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiration_date, $email);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Check the user's session token
 * 
 * @param mysqli $conn Database connection object
 * @param string $token User's session token
 * @param string $page Page where the request is made (null if it's not a login page request)
 * 
 * @return string|boolean Returns the user's email if the token is in the database and the expiration date is not passed,
 *                        Returns false otherwise and remove the token from the database
 */
function checkSessionToken($conn, $token, $page = null){
    try{
        $sql = "SELECT * FROM Accounts 
                WHERE session_token = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            if($user['session_expiration'] > date("Y-m-d H:i:s", time())){

                if($page == "login"){
                    if($user['administrator'] == 1){
                        return "admin";
                    }
                    else{
                        return "user";
                    }
                }else{
                    return $user['email'];
                }
            }
            else{
                removeSessionToken($conn, $user['email']);
                return false;
            }
        }
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the expiration date of the user's session token
 * 
 * @param mysqli $conn Database connection object
 * @param string $token User's session token
 * 
 * @return string|boolean Returns the expiration date if the token is in the database, false otherwise
 */
function getSessionExpiration($conn, $token){
    try{
        $sql = "SELECT session_expiration FROM Accounts 
                WHERE session_token = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            return $user['session_expiration'];
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Remove the session token from the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email (null if the user wants to disconnect)
 * @param string $token User's session token (null in other cases)
 * 
 * @return boolean Returns true if the token is removed, false otherwise
 */
function removeSessionToken($conn, $email, $token = null){
    try{
        if($email != null){
            $sql = "UPDATE Accounts
                SET session_token = NULL, session_expiration = NULL
                WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
        }
        else{
            $sql = "UPDATE Accounts
                SET session_token = NULL, session_expiration = NULL
                WHERE session_token = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
        }

        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

// ------------------------------------ //
// -------------- SERIES -------------- //
// ------------------------------------ //

/**
 * Get all the series from the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $sorting Sorting method
 * @param int $maxrow The row to start from in the database
 * 
 * @return array|null Returns an array with the series if the request is successful, null otherwise
 */
function getAllSeries($conn, $sorting, $maxrow){
    try{
        $sql = "SELECT * FROM Séries ORDER BY ? LIMIT ?, 25";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $sorting, $maxrow);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }

}

/**
 * Get filtered series from the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $seasons Number of seasons selected by the user
 * @param string $category Category selected by the user
 * @param string $duration Duration selected by the user
 * @param string $year Year selected by the user
 * @param string $sorting Sorting method selected by the user
 * @param int $maxrow The row to start from in the database
 * 
 * @return array|null Returns an array with the series if the request is successful, null otherwise
 */
function getFilteredSeries($conn, $seasons, $category, $duration, $year, $sorting, $maxrow){
    try{
            
        // Prepare the SQL query
        $sql = "SELECT * FROM Séries WHERE nb_saisons <= ? AND categorie LIKE ?";

        // Prepare the parameters
        $params = array();

        // Prepare the types of parameters
        $params_type = "is";

        // Selection of seasons parameter
        if ($seasons == "2"){
            $params[] = 2;
        }
        else if ($seasons == "5"){
            $params[] = 5;
        }
        else if ($seasons == "999"){
            $params[] = 999;
        }
        else{
            $params[] = 9999;
        }

        if ($category != "all"){
            $params[] = "%$category%";
        }
        else{
            $params[] = "%";
        }

        // Selection of duration parameter
        if($duration != "all"){
            if ($duration == "30"){
                $sql .= " AND id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) <= 30)";
            }
            else if ($duration == "60"){
                $sql .= " AND id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) > 30 AND AVG(duree) <= 60)";
            }
            else if ($duration == "999"){
                $sql .= " AND id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) > 60)";
            }
        }

        // Selection of year parameter
        if($year != "all"){
            $sql .= " AND date_sortie = ?";
            $params[] = $year;
            $params_type .= "i";
        }

        switch($sorting){
            case "nom ASC":
                $sql .= " ORDER BY nom ASC";
                break;
            case "nom DESC":
                $sql .= " ORDER BY nom DESC";
                break;
            case "date ASC":
                $sql .= " ORDER BY date_sortie ASC";
                break;
            case "date DESC":
                $sql .= " ORDER BY date_sortie DESC";
                break;
            default:
                break;
        }

        $sql .= " LIMIT ?, 25";
        $params[] = $maxrow;
        $params_type .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($params_type, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }
}

/**
 * Get the average duration of the serie's episodes
 * 
 * @param mysqli $conn Database connection object
 * @param string $id_serie Serie's id
 * 
 * @return array|null Returns an array with the serie's id and the average duration if the request is successful, null otherwise
 */
function getAverageDuration($conn, $id_serie){
    try{
        $sql = "SELECT serie_id, AVG(duree) AS average_duration FROM Episodes WHERE serie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_serie);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the serie's details
 * 
 * @param mysqli $conn Database connection object
 * @param string $serieId Serie's id
 * 
 * @return array|null Returns an array with the serie's details if the request is successful, null otherwise
 */
function getSerieDetails($db, $serieId){
    try{
        $sql = "SELECT * FROM Séries WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $serieId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the serie's number of seasons
 * 
 * @param mysqli $conn Database connection object
 * @param string $id_serie Serie's id
 * 
 * @return array|null Returns an array with the serie's number of seasons if the request is successful, null otherwise
 */
function getNumberOfSeasons($conn, $id_serie){
    try{
        $sql = "SELECT nb_saisons FROM Séries WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_serie);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["nb_saisons"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the amount of series in the database
 * 
 * @param mysqli $conn Database connection object
 * 
 * @return int Returns the amount of series in the database
 */
function getSeriesLength($conn){
    try{
        $sql = "SELECT COUNT(*) FROM Séries";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        return $row['COUNT(*)'];
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the amount of filtered series in the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $seasons Number of seasons selected by the user
 * @param string $category Category selected by the user
 * @param string $duration Duration selected by the user
 * @param string $year Year selected by the user
 * 
 * @return int Returns the amount of filtered series in the database
*/
function getFilteredSeriesLength($conn, $seasons, $category, $duration, $year){
    try{
        // Prepare the SQL query
        $sql = "SELECT COUNT(*) FROM Séries WHERE nb_saisons <= ? AND categorie LIKE ?";

        // Prepare the parameters
        $params = array();

        // Prepare the types of parameters
        $params_type = "is";

        // Selection of seasons parameter
        if ($seasons == "2"){
            $params[] = 2;
        }
        else if ($seasons == "5"){
            $params[] = 5;
        }
        else if ($seasons == "999"){
            $params[] = 999;
        }
        else{
            $params[] = 9999;
        }

        if ($category != "all"){
            $params[] = "%$category%";
        }
        else{
            $params[] = "%";
        }

        // Selection of duration parameter
        if($duration != "all"){
            if ($duration == "30"){
                $sql .= " AND id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) <= 30)";
            }
            else if ($duration == "60"){
                $sql .= " AND id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) > 30 AND AVG(duree) <= 60)";
            }
            else if ($duration == "999"){
                $sql .= " AND id IN (SELECT serie_id FROM Episodes GROUP BY serie_id HAVING AVG(duree) > 60)";
            }
        }

        // Selection of year parameter
        if($year != "all"){
            $sql .= " AND date_sortie = ?";
            $params[] = $year;
            $params_type .= "i";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($params_type, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['COUNT(*)'];
        }
        else {
            return null;
        }

    }
    catch(PDOException $e) {
        return false;
    }
}

// ------------------------------------ //
// -------------- EPISODES ------------ //
// ------------------------------------ //

/**
 * Get the serie's episodes for a specific season
 * 
 * @param mysqli $conn Database connection object
 * @param string $id_serie Serie's id
 * @param string $season Serie's season
 * 
 * @return array|null Returns an array with the serie's episodes if the request is successful, null otherwise
 */
function getSeasonEpisodes($conn, $id_serie, $season){
    try{
        $sql = "SELECT * FROM Episodes WHERE serie_id = ? AND saison = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $id_serie, $season);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }

}


// ------------------------------------ //
// -------------- MOVIES -------------- //
// ------------------------------------ //

/**
 * Get all the movies from the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $sorting Sorting method
 * @param int $maxrow The row to start from in the database
 * 
 * @return array|null Returns an array with the movies if the request is successful, null otherwise
 */
function getAllMovies($conn, $sorting, $maxrow){
    try{
        $sql = "SELECT * FROM Films";

        switch($sorting){
            case "nom ASC":
                $sql .= " ORDER BY nom ASC";
                break;
            case "nom DESC":
                $sql .= " ORDER BY nom DESC";
                break;
            case "date_sortie ASC":
                $sql .= " ORDER BY date_sortie ASC";
                break;
            case "date_sortie DESC":
                $sql .= " ORDER BY date_sortie DESC";
                break;
            default:
                break;
        }

        $sql .= " LIMIT ?, 25";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maxrow);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }

}

/**
 * Get filtered movies from the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $category Category selected by the user
 * @param string $duration Duration selected by the user
 * @param string $year Year selected by the user
 * @param string $sorting Sorting method selected by the user
 * @param int $maxrow The row to start from in the database
 * 
 * @return array|null Returns an array with the movies if the request is successful, null otherwise
 */
function getFilteredMovies($conn, $category, $duration, $year, $sorting, $maxrow){
    try{
        // Prepare the SQL query
        $sql = "SELECT * FROM Films WHERE categorie LIKE ?";

        // Prepare the parameters
        $params = array();

        // Prepare the types of parameters
        $params_type = "s";

        // Selection of category parameter
        if ($category != "all"){
            $params[] = "%$category%";
        }
        else{
            $params[] = "%";
        }

        // Selection of duration parameter
        if($duration != "all"){
            if ($duration == "60"){
                $params[] = 60;
                $sql .= " AND duree <= ?";
                $params_type .= "i";
            }
            else if ($duration == "120"){
                $params[] = 120;
                $sql .= " AND duree > 60 AND duree <= ?";
                $params_type .= "i";
            }
            else if ($duration == "999"){
                $params[] = 120;
                $sql .= " AND duree > ?";
                $params_type .= "i";
            }
        }

        // Selection of year parameter
        if($year != "all"){
            $params[] = $year;
            $sql .= " AND date_sortie = ?";
            $params_type .= "i";
        }

        switch($sorting){
            case "nom ASC":
                $sql .= " ORDER BY nom ASC";
                break;
            case "nom DESC":
                $sql .= " ORDER BY nom DESC";
                break;
            case "date ASC":
                $sql .= " ORDER BY date_sortie ASC";
                break;
            case "date DESC":
                $sql .= " ORDER BY date_sortie DESC";
                break;
            default:
                break;
        }

        $sql .= " LIMIT ?, 25";
        $params[] = $maxrow;
        $params_type .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($params_type, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }
}

/**
 * Get the movie's details
 * 
 * @param mysqli $conn Database connection object
 * @param string $id_movie Movie's id
 * 
 * @return array|null Returns an array with the movie's details if the request is successful, null otherwise
 */
function getMovieDetails($conn, $id_movie){
    try{
        $sql = "SELECT * FROM Films WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_movie);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row;
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the amount of movies in the database
 * 
 * @param mysqli $conn Database connection object
 * 
 * @return int Returns the amount of movies in the database
 */
function getMoviesLength($conn){
    try{
        $sql = "SELECT COUNT(*) FROM Films";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        return $row['COUNT(*)'];
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the amount of filtered movies in the database
 * 
 * @param mysqli $conn Database connection object
 * @param string $category Category selected by the user
 * @param string $duration Duration selected by the user
 * @param string $year Year selected by the user
 * 
 * @return int Returns the amount of filtered movies in the database
 */
function getFilteredMoviesLength($conn, $category, $duration, $year){
    try{
        // Prepare the SQL query
        $sql = "SELECT COUNT(*) FROM Films WHERE categorie LIKE ?";

        // Prepare the parameters
        $params = array();

        // Prepare the types of parameters
        $params_type = "s";

        // Selection of category parameter
        if ($category != "all"){
            $params[] = "%$category%";
        }
        else{
            $params[] = "%";
        }

        // Selection of duration parameter
        if($duration != "all"){
            if ($duration == "60"){
                $params[] = 60;
                $sql .= " AND duree <= ?";
                $params_type .= "i";
            }
            else if ($duration == "120"){
                $params[] = 120;
                $sql .= " AND duree > 60 AND duree <= ?";
                $params_type .= "i";
            }
            else if ($duration == "999"){
                $params[] = 120;
                $sql .= " AND duree > ?";
                $params_type .= "i";
            }
        }

        // Selection of year parameter
        if($year != "all"){
            $params[] = $year;
            $sql .= " AND date_sortie = ?";
            $params_type .= "i";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($params_type, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['COUNT(*)'];
        }
        else {
            return null;
        }
    }
    catch(PDOException $e) {
        return false;
    }
}


// ------------------------------------ //
// ------------ SEEN LIST ------------- //
// ------------------------------------ //

/**
 * Get the total time spent watching movies and series by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return int|null Returns the total time spent watching movies and series by the user if the request is successful, null otherwise
 */
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
                    `Accounts`.`email` = ?
                GROUP BY
                    `Accounts`.`email`;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["Total_Duration_Seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the total number of movies watched by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return int|null Returns the total number of movies watched by the user if the request is successful, null otherwise
 */
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
                    `Accounts`.`email` = ?
                GROUP BY
                    `Accounts`.`email`;"; 

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["Total_Movies_Seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the total number of episodes watched by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return int|null Returns the total number of episodes watched by the user if the request is successful, null otherwise
 */
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
                    `Accounts`.`email` = ?
                GROUP BY
                    `Accounts`.`email`;"; 

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["Total_Episodes_Seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Get the list of movies seen by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return array|null Returns an array with the movies seen by the user if the request is successful, null otherwise
 */
function checkMovieSeen($conn, $email, $id_movie){
    try{
        $sql = "SELECT * FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND movie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id_movie);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Add the movie to the seen list of the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $id_movie Movie's id
 * 
 * @return boolean Returns true if the movie is added to the seen list, false otherwise
 */
function addMovieSeen($conn, $email, $id_movie){
    try{
        $sql = "INSERT INTO Seen_List (user_id, type, movie_id, episode_id) VALUES ((SELECT id FROM Accounts WHERE email = ?), 'movie', ?, null)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id_movie);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Delete the movie from the seen list of the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $id_movie Movie's id
 * 
 * @return boolean Returns true if the movie is deleted from the seen list, false otherwise
 */
function deleteMovieSeen($conn, $email, $id_movie){
    try{
        $sql = "DELETE FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND movie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id_movie);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Check if the serie is fully watched by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $id_serie Serie's id
 * 
 * @return boolean Returns true if the serie is fully watched by the user, false otherwise
 */
function checkSerieFullyWatched($conn, $email, $id_serie){
    try{
        $sql = "SELECT * FROM Episodes WHERE id NOT IN (SELECT episode_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND type = 'serie') AND serie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id_serie);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false;
        }
        else {
            return true;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Check if the episode is seen by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $serieId Serie's id
 * @param string $episodeNumber Episode's number
 * @param string $seasonNumber Season's number
 * 
 * @return boolean Returns true if the episode is seen by the user, false otherwise
 */
function checkEpisodeSeen($conn, $email, $serieId, $episodeNumber, $seasonNumber){
    try{
        $sql = "SELECT * FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND episode_id = (SELECT id FROM Episodes WHERE serie_id = ? AND num_ep = ? AND saison = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $serieId, $episodeNumber, $seasonNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
    catch(PDOException $e) {
        return false;
    }
}

/**
 * Add the episode to the seen list of the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $serieId Serie's id
 * @param string $episodeNumber Episode's number
 * @param string $seasonNumber Season's number
 * 
 * @return boolean Returns true if the episode is added to the seen list, false otherwise
 */
function addEpisodeSeen($conn, $email, $serieId, $episodeNumber, $seasonNumber){
    try{
        $sql = "INSERT INTO Seen_List (user_id, type, movie_id, episode_id) VALUES ((SELECT id FROM Accounts WHERE email = ?), 'serie', null, (SELECT id FROM Episodes WHERE serie_id = ? AND num_ep = ? AND saison = ?))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $serieId, $episodeNumber, $seasonNumber);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Delete the episode from the seen list of the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $serieId Serie's id
 * @param string $episodeNumber Episode's number
 * @param string $seasonNumber Season's number
 * 
 * @return boolean Returns true if the episode is deleted from the seen list, false otherwise
 */
function deleteEpisodeSeen($conn, $email, $serieId, $episodeNumber, $seasonNumber){
    try{
        $sql = "DELETE FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND episode_id = (SELECT id FROM Episodes WHERE serie_id = ? AND num_ep = ? AND saison = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $serieId, $episodeNumber, $seasonNumber);
        $stmt->execute();

        return true;
    }
    catch(PDOException $e) {
        
        return false;
    }
}

/**
 * Get the list of movies seen by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return array|null Returns an array with the movies seen by the user if the request is successful, null otherwise
 */
function getMoviesSeen($conn, $email){
    try{
        $sql = "SELECT * FROM Films WHERE id IN (SELECT movie_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND type = 'movie')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }

}

/**
 * Get the list of series seen by the user
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * 
 * @return array|null Returns an array with the series seen by the user if the request is successful, null otherwise
 */
function getSeriesSeen($conn, $email){
    try{
        $sql = "SELECT * FROM Séries WHERE id IN (SELECT serie_id FROM Episodes WHERE id IN (SELECT episode_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND type = 'serie'))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

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
        return false;
    }

}

/**
 * Get the number of episodes seen by the user for a specific serie
 * 
 * @param mysqli $conn Database connection object
 * @param string $email User's email
 * @param string $id_serie Serie's id
 * 
 * @return array|null Returns an array with the number of episodes seen by the user for a specific serie if the request is successful
 *                    Returns null otherwise
 */
function getNumberOfEpisodesSeen($conn, $email, $id_serie){
    try{
        $sql = "SELECT COUNT(*) AS nb_episodes_seen FROM Episodes WHERE id IN (SELECT episode_id FROM Seen_List WHERE user_id = (SELECT id FROM Accounts WHERE email = ?) AND type = 'serie') AND serie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id_serie);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return $row["nb_episodes_seen"];
        }
        else {
            return null;
        } 
    }
    catch(PDOException $e) {
        return false;
    }

}
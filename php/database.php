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


// ------------------------------------ //
// -------------- SERIES -------------- //
// ------------------------------------ //



// ------------------------------------ //
// -------------- EPISODES ------------ //
// ------------------------------------ //




// ------------------------------------ //
// -------------- MOVIES -------------- //
// ------------------------------------ //



// ------------------------------------ //
// ------------ SEEN LIST ------------- //
// ------------------------------------ //
import { cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the user is connected
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ---------------------- FUNCTIONS ----------------------
// -------------------------------------------------------

// Get the number of users registered on the site
function getNumberOfUsers() {
    $.ajax({
        url: "../php/adhome.php/numUsers",
        type: "GET",
        success: function (data) {
            $("#num-users").html(data);
        },
        error: function (error) {
            console.log("Error: " + error);
        }
    });
}

// Get the last user registered on the site
function getLastUser() {
    $.ajax({
        url: "../php/adhome.php/lastUser",
        type: "GET",
        success: function (data) {
            $("#last-user").html(data);
        },
        error: function (error) {
            console.log("Error: " + error);
        }
    });
}

$(document).ready(function () {
    // Get the user's name
    setUserName();

    getAvatar("#avatar-header");

    getNumberOfUsers();

    getLastUser();

});

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user when he clicks on the logout button
$("#logout").click(function () {
    alert("Vous êtes déconnecté"); // Alert the user that he is disconnected
    disconnect();
});

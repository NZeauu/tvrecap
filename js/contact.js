import { cookieCheck, setUserName, getAvatar, getEmail, disconnect } from "./mainContent.js";

// Check if user is connected
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// -------------------- CONTACT PAGE ---------------------
// -------------------------------------------------------

// Fill the username input with the username of the connected user
function fillUsername() {
    var email = window.user_email;

    $.ajax("../php/contact.php/username", {
        type: "GET",
        data: {
            userMail: email
        },
        success: function (data) {
            $("#username-form").val(data);
        }
    });
}

$("#contact-submit").click(function () {
    $("#error-checkbox").css("display", "none");
    var email = $("#email").val();
    var username = $("#username-form").val();
    var subject = $("#subject").val();
    var message = $("#message").val();

    console.log(email + " " + username + " " + subject + " " + message)

    if($("#accept").is(":checked") == true && email != "" && username != "" && subject != "" && message != "") {
        $.ajax("../php/contact-form.php/contact-form", {
            type: "POST",
            data: {
                userMail: email,
                username: username,
                subject: subject,
                message: message
            },
            success: function (data) {
                alert(data.msg);
                window.location.href = "../html/home.html";
            }
        });
    }
    else{
        if($("#accept").is(":checked") == false && (email == "" || username == "" || subject == "" || message == "")) {
            alert("Veuillez accepter les conditions d'utilisation et remplir tous les champs");
        }
        else if($("#accept").is(":checked") == false) {
            alert("Veuillez accepter les conditions d'utilisation");
        }
        else {
            alert("Veuillez remplir tous les champs");
        }
    }
});


// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user when he clicks on the logout button
$("#logout").click(function () {
    alert("Vous êtes déconnecté"); // Alert the user that he is disconnected
    disconnect();
});


// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------

// When the page is loaded
$(document).ready(function () {

    // When the promise is resolved, get the informations about the user
    getEmail().then(function() {
        setUserName();

        fillUsername();

        $("#email").val(window.user_email);

        getAvatar("#avatar-header");
    });
});
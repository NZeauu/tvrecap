import { getCookie, cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the cookie is set every second
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ------------------ INFOS DISPLAY ----------------------
// -------------------------------------------------------

// Display the user's infos
function displayInfos() {
    var email = getCookie("user_mail");

    $.ajax('../php/settings.php/infos', {
        method: 'GET',
        data: {
            email: email
        },
    }).done(function (data) {
        
        // Personnal data section
        $('#username-input').val(data.username);
        getAvatar("#avatar-setting");

        if(data.birthday != null) {
            $("#birthday-input").val(data.birthday);
        }

        // Login data section
        $('#mail-input').val(data.email);
    });
}

// -------------------------------------------------------
// ------------------ INFOS UPDATE -----------------------
// -------------------------------------------------------

// Update the user's infos
$(".edit-ico").click(function () {

    switch(this.id) {
        case "username-but":
            $("#username-input").prop("disabled", false);
            updateUsername();
            break;
        case "birthday-but":
            $("#birthday-input").prop("disabled", false);
            updateBirthday();
            break;
        case "password-but":
            $("#password-input").prop("disabled", false);
            updatePassword();
            break;
        case "avatar-but":
            updateAvatar();
            break;
        default:
            break;
    }
});


// Update the user's username
function updateUsername() {
    var email = getCookie("user_mail");

    $("#username-but").attr("style", "display: none;");
    $("#username-save").attr("style", "display: auto;");

    $("#username-save").click(function () {

        if($("#username-input").val() == "") {
            alert("Veuillez entrer un nom d'utilisateur");
            return;
        }

        var username = $("#username-input").val();

        $.ajax('../php/settings.php/username', {
            method: 'PUT',
            data: {
                email: email,
                username: username
            },
        }).done(function (data) {
            $("#username-but").attr("style", "display: auto;");
            $("#username-save").attr("style", "display: none;");
            $("#username-input").prop("disabled", true);
            window.location.reload();
        });

    });
   
}

// Update the user's birthday
function updateBirthday() {
    var email = getCookie("user_mail");

    $("#birthday-but").attr("style", "display: none;");
    $("#birthday-save").attr("style", "display: auto;");

    $("#birthday-save").click(function () {

        var birthday = $("#birthday-input").val();

        if($("#birthday-input").val() == "") {
            birthday = null;
        }

    
        $.ajax('../php/settings.php/birthday', {
            method: 'PUT',
            data: {
                email: email,
                birthday: birthday
            },
        }).done(function (data) {
            $("#birthday-but").attr("style", "display: auto;");
            $("#birthday-save").attr("style", "display: none;");
            $("#birthday-input").prop("disabled", true);
            window.location.reload();
        });

    });
   
}

// Update the user's password
function updatePassword() {
    var email = getCookie("user_mail");

    $("#password-but").attr("style", "display: none;");
    $("#password-save").attr("style", "display: auto;");

    $("#password-save").click(function () {

        if($("#password-input").val() == "") {
            alert("Veuillez entrer un mot de passe");
            return;
        }

        var password = $("#password-input").val();

        $.ajax('../php/settings.php/password', {
            method: 'PUT',
            data: {
                email: email,
                password: password
            },
        }).done(function (data) {
            $("#password-but").attr("style", "display: auto;");
            $("#password-save").attr("style", "display: none;");
            $("#password-input").prop("disabled", true);
            window.location.reload();
        });

    });
   
}

// Update the user's avatar
function updateAvatar() {
    var email = getCookie("user_mail");

    openPopup();


    $(".but-choice").click(function () {

        if(this.id != "default") {
            var avatar = "../img/avatars/" + this.id + ".png";
        }
        else {
            var avatar = "../img/avatars/default.svg";
        }

        $("#avatar-save").click(function () {
            $.ajax('../php/settings.php/avatar', {
                method: 'PUT',
                data: {
                    email: email,
                    avatar: avatar
                },
            }).done(function (data) {
                window.location.reload();
            });
        });
    });
}

function openPopup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'block';
}

function closePopup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'none';
}

$("#avatar-cancel").click(function () {
    closePopup();
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
    setUserName();

    getAvatar("#avatar-header");

    displayInfos();

});
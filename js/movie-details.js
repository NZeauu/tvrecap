// -------------------------------------------------------
// --------------------- COOKIES -------------------------
// -------------------------------------------------------

// Cookie check
function cookieCheck() {

    if(document.cookie.indexOf("user_id") === -1) {

        // Redirect to the login page
        window.location.replace("login.html");
    }
}

// Check if the cookie is set and redirect to the login page if not
// cookieCheck();

// Check if the cookie is set every second
setInterval(cookieCheck, 1000);

// Get the user's name from the cookie
function getCookie(cookieName) {
    const name = cookieName + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookieArray = decodedCookie.split(';');

    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i].trim();
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
}


// -------------------------------------------------------
// --------------------- NAVBAR -------------------------
// -------------------------------------------------------

// Set the user's name in the navbar
function setUserName() {
    var email = getCookie("user_id");

    if (email === null) {
        $("#username").text("User not found");
    }

    $.ajax('../php/home.php/username', {
        method: 'GET',
        data: {
            email: email
        },
    }).done(function (data) {
        $("#username").text(data);
    });
}


// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------

// When the page is loaded
$(document).ready(function () {
    setUserName();

});

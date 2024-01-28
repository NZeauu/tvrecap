// -------------------------------------------------------
// --------------------- COOKIES -------------------------
// -------------------------------------------------------

// Cookie check
export function cookieCheck() {

    if(document.cookie.indexOf("user_mail") === -1) {

        // Redirect to the login page
        window.location.replace("login.html");
    }
}

// Check if the cookie is set and redirect to the login page if not
// cookieCheck();



// Get the user's name from the cookie
export function getCookie(cookieName) {
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
export function setUserName() {
    var email = getCookie("user_mail");

    if (email === null) {
        $("#username").text("User not found");
    }

    $.ajax('../php/user.php/username', {
        method: 'GET',
        data: {
            email: email
        },
    }).done(function (data) {
        $("#username").text(data);
    });
}

// Get the user's avatar
export function getAvatar(idImage) {
    var email = getCookie("user_mail");

    $.ajax('../php/user.php/avatar', {
        method: 'GET',
        data: {
            email: email
        },
    }).done(function (data) {
        $(idImage).attr("src", data);
    });
}

// Disconnect the user
export function disconnect() {
    // Delete the cookie
    document.cookie = "user_mail=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    // Redirect to the login page
    window.location.replace("login.html");
}
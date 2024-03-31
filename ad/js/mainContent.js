// -------------------------------------------------------
// --------------------- COOKIES -------------------------
// -------------------------------------------------------

window.expireSession = "";

// Get the expiration date of the cookie
function getExpirationDate() {

    $.ajax('../../php/user.php/cookieCheck', {
        method: 'GET',
    }).done(function (data) {
        if (data !== "false") {
            window.expireSession = data;
        }
    });

}

getExpirationDate();


// Cookie check
export function cookieCheck() {

    var today = new Date();
    var expire = new Date(window.expireSession);

    if (today > expire) {
        disconnect();
    }
}


// -------------------------------------------------------
// ----------------------- USER --------------------------
// -------------------------------------------------------
export function getEmail() {
    // Get the user's email from the token
    // Create a promise to wait for the email to be retrieved before doing anything else
    return new Promise((resolve, reject) => {

        $.ajax('../../php/user.php/email', {
            method: 'GET',
        }).done(function (data) {
            window.user_email = data;
            resolve(data);
        }).fail(function (error) {
            reject(error);
        });
    });
 
}

// -------------------------------------------------------
// --------------------- NAVBAR -------------------------
// -------------------------------------------------------

// Set the user's name in the navbar
export function setUserName() {

    $.ajax('../../php/user.php/username', {
        method: 'GET',
    }).done(function (data) {
        $("#username").text(data);
    });
}

// Get the user's avatar
export function getAvatar(idImage) {

    $.ajax('../../php/user.php/avatar', {
        method: 'GET',
    }).done(function (data) {
        $(idImage).attr("src", data);
    });
}

// Disconnect the user
export function disconnect() {
    // Delete the cookie
    $.ajax('../../php/user.php/disconnect', {
        method: 'GET',
    }).done(function (data) {
        // Redirect to the login page
        window.location.replace("https://tvrecap.epeigne.fr");  
    });
}
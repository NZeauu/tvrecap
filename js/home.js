// -------------------------------------------------------
// --------------------- COOKIES -------------------------
// -------------------------------------------------------

// Cookie check
function cookieCheck() {
    if(document.cookie.indexOf("user_id") === -1) {
        window.location.replace("login.html");
    }
}

// Check if the cookie is set and redirect to the login page if not
// cookieCheck();


// -------------------------------------------------------

// -------------------------------------------------------
// --------------------- COOKIES -------------------------
// -------------------------------------------------------

// Cookie check
function cookieCheck() {

    if(document.cookie.indexOf("user_mail") === -1) {

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
    var email = getCookie("user_mail");

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
// --------------------- ADD -----------------------------
// -------------------------------------------------------

// Header select choice display
$(".series-content").on("click", function () {

    // Empty the form
    $("#serie-title").val("");
    $("#serie-year").val("");
    $("#serie-seasons").val("1");
    $("#serie-synopsis").val("");
    
    
    seriesChoice(this);

});

$(".movies-content").on("click", function () {

    // Empty the form
    $("#movie-title").val("");
    $("#movie-year").val("");
    $("#movie-duration").val("");
    $("#movie-synopsis").val("");
    
    moviesChoice(this);
});

function moviesChoice(element) {

    $("#movies-content-border").attr("style", "")
    $("#series-content-border").attr("style", "")
    $(".series-content").attr("style", "")
    $(element).attr("style", "")

    $(element).css({
                    "background-color" : "rgba(255, 0, 0, 0.70)",
                    "padding-right" : "10%",
                    "z-index" : "3"
                })
    $(".series-content").css({"z-index": "5", "background-color": "transparent", "margin-left": "-10%"});
    $("#movies-content-border").css({
                    "color": "black",
                    "font-weight": "bold",
                    "position": "absolute",
                    "justify-content": "center",
                    "align-items": "center",
                    "display": "flex",
                    "height": "100%",
                    "width": "100%",
                    "transform": "translate(-50%, -50%)",
                    "background-color": "white",
                    "transform": "skew(-30deg)",
                    "z-index": "4",
                    "left": "50%"
                })
    
    // Get the movies form
    getMovieForm();
}

function seriesChoice(element) {

    $("#movies-content-border").attr("style", "")
    $("#series-content-border").attr("style", "")
    $(".movies-content").attr("style", "")
    $(element).attr("style", "")

    $(element).css({
        "background-color" : "rgba(255, 0, 0, 0.70)",
        "padding-left" : "10%",
        // "padding-right" : "1%",
        "z-index" : "3"
    })
    $(".movies-content").css({"z-index": "5", "background-color": "transparent", "margin-right": "-10%"});
    $("#series-content-border").css({
            "color": "black",
            "font-weight": "bold",
            "position": "absolute",
            "justify-content": "center",
            "align-items": "center",
            "display": "flex",
            "height": "100%",
            "width": "20%",
            "transform": "translate(-50%, -50%)",
            "background-color": "white",
            "transform": "skew(-30deg)",
            "z-index": "4",
            "left": "30%"
        })

    // Get the series form
    getSerieForm();
}

// Get the movies form
function getMovieForm() {
    $("#serie-form").attr("style", "display: none;");
    $("#movie-form").attr("style", "display: auto;");
}

// Get the series form
function getSerieForm() {
    $("#movie-form").attr("style", "display: none;");
    $("#serie-form").attr("style", "display: auto;");
}

// Ask a movie add
$("#submit-movie").click(function () {
    var title = $("#movie-title").val();
    var year = $("#movie-year").val();
    var duration = $("#movie-duration").val();
    var synopsis = $("#movie-synopsis").val();


    if(title === "" || year === "" || duration === "" || synopsis === "") {
        alert("Veuillez remplir tous les champs");
        return;
    }

    $.ajax('../php/add.php/sendmail', {
        method: 'POST',
        data: {
            title: title,
            year: year,
            duration: duration,
            synopsis: synopsis,
            type: "movie"
        },
    }).done(function (data) {
        alert("Demande d'ajout de film envoyée");
        window.location.replace("home.html");
    });
});

// Ask a serie add
$("#submit-serie").click(function () {
    var title = $("#serie-title").val();
    var year = $("#serie-year").val();
    var seasons = $("#serie-seasons").val();
    var synopsis = $("#serie-synopsis").val();

    if(title === "" || year === "" || seasons === "" || synopsis === "") {
        alert("Veuillez remplir tous les champs");
        return;
    }

    $.ajax('../php/add.php/sendmail', {
        method: 'POST',
        data: {
            title: title,
            year: year,
            seasons: seasons,
            synopsis: synopsis,
            type: "serie"
        },
    }).done(function (data) {
        alert("Demande d'ajout de série envoyée");
        window.location.replace("home.html");
    });
});


// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user
function disconnect() {
    // Delete the cookie
    document.cookie = "user_mail=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    // Redirect to the login page
    window.location.replace("login.html");
}

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

    // Select "Films" by default
    moviesChoice($(".movies-content"));
});
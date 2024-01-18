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
// ---------------------- MOVIE --------------------------
// -------------------------------------------------------

// Get the serie's details
function getSerieDetails(){

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/seriedetails', {
        method: 'GET',
        data: {
            serieId: serieId
        },
    }).done(function (data) {

        // Get the average duration of an episode
        $.ajax('../php/series.php/duration', {
            method: 'GET',
            data: {
                id_serie: serieId
            },
        }).done(function (data) {
            // console.log(data);
            var duration = data;

            if(duration > 60){
                var hours = Math.floor(duration / 60);
                var minutes = duration % 60;

                // Add a 0 if the minutes are less than 10
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }

                // If the minutes are a float, round them
                if (minutes % 1 !== 0) {
                    minutes = Math.round(minutes);
                }
                console.log(hours + "h" + minutes);

                $("#duration-value").text(hours + "h" + minutes);
            }
            else{

                // Add a 0 if the minutes are less than 10
                if (duration < 10) {
                    minutes = '0' + duration;
                }

                // If the minutes are a float, round them
                if (duration % 1 !== 0) {
                    minutes = Math.round(duration);
                }

                // console.log(duration + "min");

                $("#duration-value").text(minutes + "min");
            }
        });

        // Set the serie's details
        $("#title-text").text(data.nom);
        $("#year-text").text(data.date_sortie);
        $("#category-value").text(data.categorie);
        $("#actors-value").text(data.actors);
        $("#real-value").text(data.realisator);
        $("#synopsis").text(data.synopsis);

        // Get the serie's picture
        getImage(data.image_id);

        // Check if the serie is in the user's watchlist
        checkWatchlist();
    });

}

// Get the serie's picture
function getImage(id) {

    var imageId = id;

    // Faites une requête AJAX pour récupérer l'image
    $.ajax({
        url: '../php/cover.php/cover',
        type: 'GET',
        data: { id_cover: imageId },
        success: function (data) {
            // Mettez à jour le contenu de l'élément avec l'image récupérée
            $('#picture').html('<img src="data:image/jpeg;base64,' + data + '" alt="Image" id="picture-img">');

        },
        error: function () {
            console.error('Erreur lors de la récupération de l\'image');
        }
    });

}

// Display the good check button depending if the serie is fully watched or not by the user
function checkWatchlist() {

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/checkfullywatched', {
        method: 'GET',
        data: {
            serieId: serieId,
            userMail: userMail
        },
    }).done(function (data) {

        if (data == true) {
            $("#seen-button").attr("style", "display: auto;");
            $("#not-seen-button").attr("style", "display: none;");
        } else {
            $("#seen-button").attr("style", "display: none;");
            $("#not-seen-button").attr("style", "display: auto;");
        }
    });
}

// Add the episode to the user's watchlist and display the good check button
function addToWatchlist() {

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/addwatchlist', {
        method: 'POST',
        data: {
            serieId: serieId,
            userMail: userMail
        },
    }).done(function (data) {
        checkWatchlist();
    });
}

// Remove the serie from the user's watchlist and display the good check button
function removeFromWatchlist() {

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/removewatchlist', {
        method: 'DELETE',
        data: {
            serieId: serieId,
            userMail: userMail
        },
    }).done(function (data) {
        checkWatchlist();
    });
}

// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------

// When the page is loaded
$(document).ready(function () {
    setUserName();

    // Get the movie's details
    getSerieDetails();

    // When the user clicks on the "seen" button
    $("#not-seen-button").click(function () {
        addToWatchlist();
    });

    // When the user clicks on the "seen" button
    $("#seen-button").click(function () {
        removeFromWatchlist();
    });
});

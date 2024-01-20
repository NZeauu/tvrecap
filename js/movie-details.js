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

// Get the movie's details
function getMovieDetails(){

    // Get the movie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const movieId = urlParams.get('id');

    // Get the movie's details from the database
    $.ajax('../php/movie-details.php/moviedetails', {
        method: 'GET',
        data: {
            movieId: movieId
        },
    }).done(function (data) {

        var duration = data.duree;
        var hours = Math.floor(duration / 60);
        var minutes = duration % 60;

        if (minutes < 10) {
            minutes = "0" + minutes;
        }

        // Set the movie's details
        $("#title-text").text(data.nom);
        $("#year-text").text(data.date_sortie);
        $("#category-value").text(data.categorie);
        $("#duration-value").text(hours + "h" + minutes);
        $("#actors-value").text(data.actors);
        $("#real-value").text(data.realisator);
        $("#synopsis").text(data.synopsis);

        // Get the movie's picture
        getImage(data.image_id);

        // Check if the movie is in the user's watchlist
        checkWatchlist();
    });

}

// Get the movie's picture
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

// Display the good check button depending if the movie is in the user's watchlist or not
function checkWatchlist() {

    // Get the movie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const movieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the movie's details from the database
    $.ajax('../php/movie-details.php/checkwatchlist', {
        method: 'GET',
        data: {
            movieId: movieId,
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

// Add the movie to the user's watchlist and display the good check button
function addToWatchlist() {

    // Get the movie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const movieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the movie's details from the database
    $.ajax('../php/movie-details.php/addwatchlist', {
        method: 'PUT',
        data: {
            movieId: movieId,
            userMail: userMail
        },
    }).done(function (data) {
        checkWatchlist();
    });
}

// Remove the movie from the user's watchlist and display the good check button
function removeFromWatchlist() {

    // Get the movie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const movieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the movie's details from the database
    $.ajax('../php/movie-details.php/removewatchlist', {
        method: 'DELETE',
        data: {
            movieId: movieId,
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
    getMovieDetails();

    // When the user clicks on the "seen" button
    $("#not-seen-button").click(function () {
        addToWatchlist();
    });

    // When the user clicks on the "seen" button
    $("#seen-button").click(function () {
        removeFromWatchlist();
    });
});




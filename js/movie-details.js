import { cookieCheck, setUserName, getAvatar, getEmail } from "./mainContent.js";

// Check if the cookie is set every second
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ---------------------- MOVIE --------------------------
// -------------------------------------------------------

// Return back button
$("#return").on("click", function () {
    window.history.back();
});

// Get the movie's details
function getMovieDetails(){

    // Display the loading animation
    $('#details-frame').attr('style', 'display: none;');
    $('#loading').attr('style', 'display: flex; text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter; justify-content: center; align-items: center; width: 100%; height: 100%;');

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
        $('#picture').empty();
        $('<img>', {
            src: data.image,
            alt: "cover",
            id: "picture-img"
        }).appendTo('#picture');

        // Check if the movie is in the user's watchlist
        checkWatchlist();

        // Wait 1 second and display the movie's details
        setTimeout(function () {
            $('#details-frame').fadeIn(1000);
            $('#loading').attr('style', 'display: none;');
        }, 1000);

    });

}

// Display the good check button depending if the movie is in the user's watchlist or not
function checkWatchlist() {

    // Get the movie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const movieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = window.user_email;

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
    var userMail = window.user_email;

    // Get the movie's details from the database
    $.ajax('../php/movie-details.php/addwatchlist', {
        method: 'POST',
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
    var userMail = window.user_email;

    // Get the movie's details from the database
    $.ajax('../php/movie-details.php/removewatchlist', {
        method: 'POST',
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

    // Get the user's email
    // When the promise is resolved, get the informations about the user
    getEmail().then(function () {
        setUserName();

        // Get the movie's details
        getMovieDetails();

        getAvatar("#avatar-header");

        // When the user clicks on the "seen" button
        $("#not-seen-button").click(function () {
            addToWatchlist();
        });

        // When the user clicks on the "seen" button
        $("#seen-button").click(function () {
            removeFromWatchlist();
        });
    });

});




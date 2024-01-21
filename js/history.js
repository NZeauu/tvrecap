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
// --------------------- CHOICE --------------------------
// -------------------------------------------------------

$(".series-content").on("click", function () {

    seriesChoice(this);

});

$(".movies-content").on("click", function () {
    
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
    
    // Get the user's movies history
    getMoviesHistory();
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

    // Get the user's series history
    getSeriesHistory();
}


// -------------------------------------------------------
// --------------------- HISTORY -------------------------
// -------------------------------------------------------

// Get the user's movies history
function getMoviesHistory() {
    var email = getCookie("user_mail");

    $.ajax('../php/history.php/movies', {
        method: 'GET',
        data: {
            userMail: email
        },
    }).done(function (data) {
        $("#history-content").empty();
        createMovieCard(data);
    });
}

// Get the user's series history
function getSeriesHistory() {
    var email = getCookie("user_mail");

    $.ajax('../php/history.php/series', {
        method: 'GET',
        data: {
            userMail: email
        },
    }).done(function (data) {
        $("#history-content").empty();
        createSeriesCard(data);        
    });
}



// -------------------------------------------------------
// ---------------------- CARDS --------------------------
// -------------------------------------------------------

// Create card for a movie
function createMovieCard(data){

    $("#history-content").html('<h1 id="loading" style="font-size: 50px"> Loading... </h1>');
    $('#loading').attr('style', 'text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter;');

    // console.log(data);

    // If the data is empty or null print a message
    if (data === null || data === "") {
        const message = $('<p>').text('Aucun film vu !');
        $('#history-content').append(message);

        // Create style for the message
        const style = $('<style>').text('#history-content p {text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter;}');
        $('head').append(style);

        $('#loading').remove();

        return;
    }

    // Convert the duration to hours and minutes
    // The base value is in minutes

    for(let i = 0; i < data.length; i++){

        const duration = data[i].duree;
        const hours = Math.floor(duration / 60);
        var minutes = duration % 60;

        // Add a 0 if the minutes are less than 10
        if (minutes < 10) {
            minutes = '0' + minutes;
        }

        // Create the movie card
        const movieCard = $('<div>').attr('id', 'movie-card');
        const cardContent = $('<div>').attr('id', 'card-content');

        // Get the movie image
        const movieCardImg = $('<div>').attr('id', 'movie-card-img' + i);
        movieCardImg.attr('class', 'movie-card-img');

        $.ajax({
            url: '../php/cover.php/cover',
            type: 'GET',
            data: { id_cover: data[i].image_id },
            success: function (data) {
                // Create the image tag with the base64 data    
                const img = $('<img>').attr('src', 'data:image/jpeg;base64,' + data);
                movieCardImg.append(img);
            },
            error: function () {
                console.error('Erreur lors de la récupération de l\'image');
            }
        });

        const movieCardInfo = $('<div>').attr('id', 'movie-card-info');

        const movieCardTitle = $('<div>').attr('id', 'movie-card-title');

        const movieTitle = $('<div>').attr('id', 'movie-title');
        const titleSection = $('<section>').text(data[i].nom);
        movieTitle.append(titleSection);

        const movieYear = $('<div>').attr('id', 'movie-year');
        const yearSection = $('<section>').text(data[i].date_sortie);
        movieYear.append(yearSection);

        movieCardTitle.append(movieTitle);
        movieCardTitle.append(movieYear);

        // const separatorLine = $('<div>').attr('id', 'separator-line');

        const bottomCard = $('<div>').attr('id', 'bottom-card');

        const movieCardDetails = $('<div>').attr('id', 'movie-card-details');

        // const movieCardCategory = $('<div>').attr('id', 'movie-card-category');
        // const categoryTitleSection = $('<section>').attr('id', 'category-title').text('Catégorie(s)');
        // const movieCategorySection = $('<section>').attr('id', 'movie-category').text(data[i].categorie);
        // movieCardCategory.append(categoryTitleSection);
        // movieCardCategory.append(movieCategorySection);

        const movieCardDuration = $('<div>').attr('id', 'movie-card-duration');
        const durationTitleSection = $('<section>').attr('id', 'duration-title').text('Durée');
        const movieDurationSection = $('<section>').attr('id', 'movie-duration').text(hours + 'h' + minutes);
        movieCardDuration.append(durationTitleSection);
        movieCardDuration.append(movieDurationSection);

        // movieCardDetails.append(movieCardCategory);
        movieCardDetails.append(movieCardDuration);

        const movieDetailsButton = $('<div>').attr('id', 'movie-details-but');
        const detailsButton = $('<button>').attr('id', 'movie-details-button').text('Détails');
        detailsButton.attr('onclick', 'window.location.href = "movie-details.html?id=' + data[i].id + '";');
        detailsButton.attr('value', data[i].id);
        movieDetailsButton.append(detailsButton);

        bottomCard.append(movieCardDetails);
        bottomCard.append(movieDetailsButton);

        movieCardInfo.append(movieCardTitle);
        // movieCardInfo.append(separatorLine);
        movieCardInfo.append(bottomCard);

        cardContent.append(movieCardImg);
        cardContent.append(movieCardInfo);

        movieCard.append(cardContent);

        movieCard.attr('style', 'display: none;');

        // Append the movie card to the body of the document
        $('#history-content').append(movieCard);

        // Wait for the image to be loaded
        // $('#movie-card-img' + i).ready(function () {
        //     $('#loading').remove();
        //     movieCard.fadeIn(500);
        // });

        setTimeout(function () {
            $('#loading').remove();
            movieCard.fadeIn(500);
        }, 1000);
    }



}

// Create card for a series
function createSeriesCard(data){

    $('#history-content').html('<h1 id="loading" style="font-size: 50px"> Loading... </h1>');
    $('#loading').attr('style', 'text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter;');

     // If the data is empty or null print a message
     if (data === null || data === "") {
        const message = $('<p>').text('Aucune série vue !');
        $('#history-content').append(message);

        // Create style for the message
        const style = $('<style>').text('#history-content p {text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter}');
        $('head').append(style);

        $('#loading').remove();

        return;
    }

    // Convert the duration to hours and minutes
    // The base value is in minutes

    for(let i = 0; i < data.length; i++){

        // Create the serie card
        const serieCard = $('<div>').attr('id', 'serie-card');
        const cardContent = $('<div>').attr('id', 'card-content');

        // Get the serie image
        const serieCardImg = $('<div>').attr('id', 'serie-card-img' + i);
        serieCardImg.attr('class', 'serie-card-img');

        $.ajax({
            url: '../php/cover.php/cover',
            type: 'GET',
            data: { id_cover: data[i].image_id },
            success: function (data) {
                // Create the image tag with the base64 data    
                const img = $('<img>').attr('src', 'data:image/jpeg;base64,' + data);
                serieCardImg.append(img);
            },
            error: function () {
                console.error('Erreur lors de la récupération de l\'image');
            }
        });

        const serieCardInfo = $('<div>').attr('id', 'serie-card-info');

        const serieCardTitle = $('<div>').attr('id', 'serie-card-title');

        const serieTitle = $('<div>').attr('id', 'serie-title');
        const titleSection = $('<section>').text(data[i].nom);
        serieTitle.append(titleSection);

        const serieYear = $('<div>').attr('id', 'serie-year');
        const yearSection = $('<section>').text(data[i].date_sortie);
        serieYear.append(yearSection);

        serieCardTitle.append(serieTitle);
        serieCardTitle.append(serieYear);

        // const separatorLine = $('<div>').attr('id', 'separator-line');

        const bottomCard = $('<div>').attr('id', 'bottom-card');

        const serieCardDetails1 = $('<div>').attr('id', 'serie-card-details');

        // const serieCardCategory = $('<div>').attr('id', 'serie-card-category');
        // const categoryTitleSection = $('<section>').attr('id', 'category-title').text('Catégorie(s)');
        // const serieCategorySection = $('<section>').attr('id', 'serie-category').text(data[i].categorie);
        // serieCardCategory.append(categoryTitleSection);
        // serieCardCategory.append(serieCategorySection);

        const serieCardDuration = $('<div>').attr('id', 'serie-card-duration');
        const durationTitleSection = $('<section>').attr('id', 'duration-title').text('Durée moyenne d\'un épisode');
        const serieDurationSection = $('<section>').attr('id', 'serie-duration');

        // Get the average duration of an episode
        $.ajax('../php/series.php/duration', {
            method: 'GET',
            data: {
                id_serie: data[i].id
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

                $('#serie-duration').text(hours + 'h' + minutes);
            }
            else{

                // Add a 0 if the minutes are less than 10
                if (duration < 10) {
                    duration = '0' + duration;
                }

                // If the minutes are a float, round them
                if (duration % 1 !== 0) {
                    duration = Math.round(duration);
                }

                $('#serie-duration').text(duration + 'min');
            }
        });
        serieCardDuration.append(durationTitleSection);
        serieCardDuration.append(serieDurationSection);

        // serieCardDetails1.append(serieCardCategory);
        serieCardDetails1.append(serieCardDuration);

        const serieCardDetails2 = $('<div>').attr('id', 'serie-card-details');

        const serieCardWatched = $('<div>').attr('id', 'serie-card-watched');
        const watchedTitleSection = $('<section>').attr('id', 'watched-title').text('Nombre d\'épisodes vus');
        const serieWatchedSection = $('<section>').attr('id', 'serie-category');

        // Get the number of watched episodes
        $.ajax('../php/history.php/watched', {
            method: 'GET',
            data: {
                idserie: data[i].id,
                userMail: getCookie("user_mail")
            },
        }).done(function (data) {
            // console.log(data);
            $('#serie-category').text(data);
        });


        serieCardWatched.append(watchedTitleSection);
        serieCardWatched.append(serieWatchedSection);

        serieCardDetails2.append(serieCardWatched);

        const serieDetailsButton = $('<div>').attr('id', 'serie-details-but');
        const detailsButton = $('<button>').attr('id', 'serie-details-button').text('Détails');
        detailsButton.attr('onclick', 'window.location.href = "serie-details.html?id=' + data[i].id + '";');
        detailsButton.attr('value', data[i].id);
        serieDetailsButton.append(detailsButton);

        bottomCard.append(serieCardDetails1);
        bottomCard.append(serieCardDetails2);
        bottomCard.append(serieDetailsButton);

        serieCardInfo.append(serieCardTitle);
        // serieCardInfo.append(separatorLine);
        serieCardInfo.append(bottomCard);

        cardContent.append(serieCardImg);
        cardContent.append(serieCardInfo);

        serieCard.append(cardContent);

        serieCard.attr('style', 'display: none;');

        $('#history-content').append(serieCard);


        // Wait for the image to be loaded
        // $('#serie-card-img' + i).ready(function () {
        //     $('#loading').remove();
        //     serieCard.fadeIn(500);
        // });

        setTimeout(function () {
            $('#loading').remove();
            serieCard.fadeIn(500);
        }, 1000);
        
    }
}


// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------

// When the page is loaded
$(document).ready(function () {
    setUserName();

    
    // Select "Films" by default
    moviesChoice($(".movies-content"));

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

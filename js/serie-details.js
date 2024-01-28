import { getCookie, cookieCheck, setUserName, getAvatar } from "./mainContent.js";

// Check if the cookie is set every second
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ---------------------- SERIE --------------------------
// -------------------------------------------------------

// Return back button
$("#return").on("click", function () {

    // Get the previous page's url
    var previousPage = document.referrer;
    console.log(previousPage);

    // If the previous page is the history page redirect to it else go back to the previous page
    if(previousPage == "https://epeigne.frstud.fr/tvrecap/html/history.html"){
        window.location.replace("history.html");
    }
    else{
        window.history.back();
    }

    // window.history.back();
});

// Get the serie's details
function getSerieDetails(){

    // Display the loading animation
    $('#details-frame').attr('style', 'display: none;');
    $('#loading').attr('style', 'display: flex; text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter; justify-content: center; align-items: center; width: 100%; height: 100%;');


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

        // Wait 1 second and display the movie's details
        setTimeout(function () {
            $('#details-frame').fadeIn(1000);
            $('#loading').attr('style', 'display: none;');
        }, 1000);
        
    });

}

// Get the serie's picture
function getImage(id) {

    var imageId = id;

    // AJAX request to get the image 
    $.ajax({
        url: '../php/cover.php/cover',
        type: 'GET',
        data: { id_cover: imageId },
        success: function (data) {
            // Create the image tag with the base64 data
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

// Check if an episode is watched or not by the user
function checkEpisodeWatched(episodeNumber, seasonNumber) {
    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/checkepwatched', {
        method: 'GET',
        data: {
            serieId: serieId,
            userMail: userMail,
            episodeNumber: episodeNumber,
            seasonNumber: seasonNumber
        },
    }).done(function (data) {

        var buttonSeen = "#E" + episodeNumber + "Seen";
        var buttonNotSeen = "#E" + episodeNumber + "NotSeen";

        if(data == true){
            $(buttonSeen).attr("style", "display: auto;");
            $(buttonNotSeen).attr("style", "display: none;");          
        }
        else{
            $(buttonSeen).attr("style", "display: none;");
            $(buttonNotSeen).attr("style", "display: auto;");
        }
    });
}

// Update the episode's status to watched if seen button is clicked
$("#episodes").on("click", ".ep-seen", function () {

    // Get the episode's number from the button's id (ex: E1Seen => 1)
    var episodeNumber = this.id.substring(1, this.id.length - 4);

    // Get the season's number from the season's button value
    var seasonNumber = $(".season-button[value='selected']").attr("id").substring(1);

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/updateepstatus', {
        method: 'DELETE',
        data: {
            episodeNumber: episodeNumber,
            seasonNumber: seasonNumber,
            userMail: userMail,
            serieId: serieId
        },
    }).done(function (data) {
        checkEpisodeWatched(episodeNumber, seasonNumber);
    });
});


// Update the episode's status to not watched if not-seen button is clicked
$("#episodes").on("click", ".ep-not-seen", function () {

    // Get the episode's number from the button's id (ex: E1NotSeen => 1)
    var episodeNumber = this.id.substring(1, this.id.length - 7);

    // Get the season's number from the season's button value
    var seasonNumber = $(".season-button[value='selected']").attr("id").substring(1);

    // Get the user's id from the cookie
    var userMail = getCookie("user_mail");

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/updateepstatus', {
        method: 'PUT',
        data: {
            episodeNumber: episodeNumber,
            seasonNumber: seasonNumber,
            userMail: userMail,
            serieId: serieId
        },
    }).done(function (data) {
        checkEpisodeWatched(episodeNumber, seasonNumber);
    });
});


// -------------------------------------------------------
// ---------------------- CARDS --------------------------
// -------------------------------------------------------

// Create the cards for each episode
function createSerieCards(data) {

    // Clear the previous cards
    $("#episodes").empty();

    // For each episode
    for (var i = 0; i < data.length; i++) {

        // Card base
        const serieCard = $('<div>').attr('class', 'episode-card');

        // Card main blocks
        const episodeNumber = $('<div>').attr('class', 'episode-number');
        const episodeSeen = $('<div>').attr('class', 'episode-seen');

        // Card episodeNumber content
        const episodeTitle = $('<h3>').text('Episode ' + data[i].num_ep + ' : "' + data[i].nom + '"');
        const episodeDuration = $('<div>').attr('class', 'episode-duration');
        const episodeDurationValue = $('<p>');

        // Duration of the episode
        var duration = data[i].duree;

        if(duration > 60){
            var hours = Math.floor(duration / 60);
            var minutes = duration % 60;

            // Add a 0 if the minutes are less than 10
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            episodeDurationValue.text(hours + "h" + minutes);
        }
        else{
            var minutes = duration;

            // Add a 0 if the minutes are less than 10
            if (minutes < 10) {
                var minutes = '0' + minutes;
            }
            episodeDurationValue.text(minutes + "min");
        }

        // Card episodeSeen content
        const episodeSeenButton = $('<button>').attr('class', 'ep-seen');
        episodeSeenButton.attr('id', 'E' + data[i].num_ep + 'Seen');
        episodeSeenButton.attr('style', 'display: none;');
        episodeSeenButton.text('Vu');

        const episodeNotSeenButton = $('<button>').attr('class', 'ep-not-seen');
        episodeNotSeenButton.attr('id', 'E' + data[i].num_ep + 'NotSeen');
        episodeNotSeenButton.attr('style', 'display: none;');
        episodeNotSeenButton.text('Non vu');

        // Check if the episodes are watched or not
        checkEpisodeWatched(data[i].num_ep, data[i].saison);

        // Append the content to the card
        episodeNumber.append(episodeTitle);
        episodeDuration.append(episodeDurationValue);
        episodeNumber.append(episodeDuration);
        
        episodeSeen.append(episodeSeenButton);
        episodeSeen.append(episodeNotSeenButton);

        serieCard.append(episodeNumber);
        serieCard.append(episodeSeen);

        // Append the card to the page
        $("#episodes").append(serieCard);
    }

}

// Create the buttons for each season
function createSeasonButtons() {

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');


   $.ajax('../php/serie-details.php/seasons', {
        method: 'GET',
        data: {
            serieId: serieId
        },
    }).done(function (data) {

        // For each season
        for (var i = 1; i <= data; i++) {

            // Create the button
            const seasonButton = $('<button>').attr('class', 'season-button');
            seasonButton.attr('id', 'S' + i);
            seasonButton.text('Saison ' + i);
            seasonButton.attr('value', 'not-selected');

            // Append the button to the page
            $("#seasons").append(seasonButton);
        }

        // Set the first season's episodes as default
        $("#S1").attr("style", "background-color: #F20000; color: white;");
        $("#S1").attr("value", "selected");
        getSeasonEpisodes(1);
    });

}

// Get the season's episodes of the serie
function getSeasonEpisodes(seasonNumber) {

    // Get the serie's id from the url
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const serieId = urlParams.get('id');

    // Get the serie's details from the database
    $.ajax('../php/serie-details.php/seasonepisodes', {
        method: 'GET',
        data: {
            seasonNumber: seasonNumber,
            serieId: serieId
        },
    }).done(function (data) {

        // Create the cards for each episode
        createSerieCards(data);
        
    });
}


// When the user clicks on a season button it displays the episodes of the season
$("#seasons").on("click", ".season-button", function () {

    // Get the season number from the button's id (ex: S1 => 1)
    var seasonNumber = this.id.substring(1);

    // Change button's color and set the other buttons to the default color
    $(".season-button").attr("style", "background-color: rgba(255, 0, 0, 0.3)");
    $(this).attr("style", "background-color: #F20000; color: white;");
    $(".season-button").attr("value", "not-selected");
    $(this).attr("value", "selected");

    // Get the season's episodes
    getSeasonEpisodes(seasonNumber);

});


// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------

// When the page is loaded
$(document).ready(function () {
    setUserName();
    getAvatar("#avatar-header");

    // Get the movie's details
    getSerieDetails();

    // Set the seasons buttons
    createSeasonButtons();

    // When the user clicks on the "seen" button
    $(".not-seen-button").click(function () {
        addToWatchlist();
    });

    // When the user clicks on the "seen" button
    $(".seen-button").click(function () {
        removeFromWatchlist();
    });
});

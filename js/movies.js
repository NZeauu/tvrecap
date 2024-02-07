import { getCookie, cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the cookie is set every second
setInterval(cookieCheck, 1000);


// -------------------------------------------------------
// -------------------- FILTERS --------------------------
// -------------------------------------------------------

// Fill select with years
function fillYears() {
    var currentYear = new Date().getFullYear();

    let i = currentYear;

    let min_year = currentYear - 50;

    for (i; i >= currentYear - 50; i--) {
        $('#year').append($('<option>', {
            value: i,
            text: i
        }));
        if (i == currentYear - 50){
            $('#year').append($('<option>', {
                value: currentYear - 51,
                text: currentYear - 51 + " et moins"
            }));
        }
    }

    
}

// Fill select with categories
function fillCategories() {
    var list = [
        "Action",
        "Aventure",
        "Animation",
        "Comédie",
        "Crime",
        "Documentaire",
        "Drame",
        "Famille",
        "Fantaisie",
        "Horreur",
        "Mystère",
        "Romance",
        "Science-fiction",
        "Thriller",
        "Guerre",
        "Western",
        "Biopic",
        "Historique",
        "Musical",
        "Sport"
    ];

    for (let i = 0; i < list.length; i++) {
        $('#category').append($('<option>', {
            value: list[i],
            text: list[i]
        }));
    }
}



// -------------------------------------------------------
// --------------------- MOVIES --------------------------
// -------------------------------------------------------

// Get the movies from the database
function getAllMovies() {
    $.ajax('../php/movies.php/all', {
        method: 'GET',
    }).done(function (data) {
        // console.log(data);
        createCard(data);
    });
}

// Get movies filtered
function getFilteredMovies() {

    var category = $("#category").val();
    var duration = $("#duration").val();
    var year = $("#year").val();

    if (category == "all" && duration == "all" && year == "all") {
        getAllMovies();
        return;
    }

    $.ajax('../php/movies.php/filtered', {
        method: 'GET',
        data: {
            category: category,
            duration: duration,
            year: year,
        },
    }).done(function (data) {
        // console.log(data);
        createCard(data);
    });
}

// Get movies sorted
$("#filter-button").click(function () {
    getFilteredMovies();
});

// Reset the filters
$("#reset-button").click(function () {
    $("#category").val("all");
    $("#duration").val("all");
    $("#year").val("all");
    getAllMovies();
});

// -------------------------------------------------------
// ------------------ CARD CREATION ----------------------
// -------------------------------------------------------

function createCard(data){

    // console.log(data);

    // Empty the list
    $('#list').empty();

    $('#list').html('<h1 id="loading" > Loading... </h1>');
    $('#loading').attr('style', 'text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter;');

    // If the data is empty or null print a message
    if (data === null || data === "") {
        const message = $('<p>').text('Aucun film à afficher !');
        $('#list').append(message);

        // Create style for the message
        const style = $('<style>').text('#list p {text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter}');
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
        const movieCard = $('<div>').attr('class', 'movie-card');
        const cardContent = $('<div>').attr('class', 'card-content');

        // Get the movie image
        const movieCardImg = $('<div>').attr('id', 'movie-card-img' + i);
        movieCardImg.attr('class', 'movie-card-img');

        console.log(data[i].image);

        const image = $('<img>').attr('src', data[i].image);
        movieCardImg.append(image);

        const movieCardInfo = $('<div>').attr('class', 'movie-card-info');

        const movieCardTitle = $('<div>').attr('class', 'movie-card-title');

        const movieTitle = $('<div>').attr('class', 'movie-title');
        const titleSection = $('<section>').text(data[i].nom);
        movieTitle.append(titleSection);

        const movieYear = $('<div>').attr('class', 'movie-year');
        const yearSection = $('<section>').text(data[i].date_sortie);
        movieYear.append(yearSection);

        movieCardTitle.append(movieTitle);
        movieCardTitle.append(movieYear);

        const separatorLine = $('<div>').attr('class', 'separator-line');

        const bottomCard = $('<div>').attr('class', 'bottom-card');

        const movieCardDetails = $('<div>').attr('class', 'movie-card-details');

        const movieCardCategory = $('<div>').attr('class', 'movie-card-category');
        const categoryTitleSection = $('<section>').attr('class', 'category-title').text('Catégorie(s)');
        const movieCategorySection = $('<section>').attr('class', 'movie-category').text(data[i].categorie);
        movieCardCategory.append(categoryTitleSection);
        movieCardCategory.append(movieCategorySection);

        const movieCardDuration = $('<div>').attr('class', 'movie-card-duration');
        const durationTitleSection = $('<section>').attr('class', 'duration-title').text('Durée');
        const movieDurationSection = $('<section>').attr('class', 'movie-duration').text(hours + 'h' + minutes);
        movieCardDuration.append(durationTitleSection);
        movieCardDuration.append(movieDurationSection);

        movieCardDetails.append(movieCardCategory);
        movieCardDetails.append(movieCardDuration);

        const movieDetailsButton = $('<div>').attr('class', 'movie-details-but');
        const detailsButton = $('<button>').attr('class', 'movie-details-button').text('Détails');
        detailsButton.attr('onclick', 'window.location.href = "movie-details.html?id=' + data[i].id + '";');
        detailsButton.attr('value', data[i].id);
        movieDetailsButton.append(detailsButton);

        bottomCard.append(movieCardDetails);
        bottomCard.append(movieDetailsButton);

        movieCardInfo.append(movieCardTitle);
        movieCardInfo.append(separatorLine);
        movieCardInfo.append(bottomCard);

        cardContent.append(movieCardImg);
        cardContent.append(movieCardInfo);

        movieCard.append(cardContent);

        movieCard.attr('style', 'display: none;');

        // Append the movie card to the body of the document
        $('#list').append(movieCard);

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

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

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
    getAvatar("#avatar-header");

    // Get all the movies from the database
    getAllMovies();

    // Fill the select with years
    fillYears();

    // Fill the select with categories
    fillCategories();
});
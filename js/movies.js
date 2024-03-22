import { cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

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

    window.filtered = false;

    $("#pagination").css("display", "none");

    var maxRow = (window.page - 1) * 25;

    // Scroll to the top of the page
    window.scrollTo(0, 0);

    var sorting = $("#sorting-select").val();

    switch (sorting) {
        case "title_asc":
            sorting = "nom ASC";
            break;
        case "title_desc":
            sorting = "nom DESC";
            break;
        case "release_date_asc":
            sorting = "date_sortie ASC";
            break;
        case "release_date_desc":
            sorting = "date_sortie DESC";
            break;
        default:
            break;
    }


    $.ajax('../php/movies.php/all', {
        method: 'GET',
        data: {
            sorting: sorting,
            maxrow: maxRow
        }
    }).done(function (data) {
        createCard(data);
    });
}

// Get movies filtered
function getFilteredMovies() {

    window.filtered = true;

    $("#pagination").css("display", "none");

    var category = $("#category").val();
    var duration = $("#duration").val();
    var year = $("#year").val();
    var sorting = $("#sorting-select").val();

    switch (sorting) {
        case "title_asc":
            sorting = "nom ASC";
            break;
        case "title_desc":
            sorting = "nom DESC";
            break;
        case "release_date_asc":
            sorting = "date_sortie ASC";
            break;
        case "release_date_desc":
            sorting = "date_sortie DESC";
            break;
        default:
            break;
    }

    if (category == "all" && duration == "all" && year == "all") {
        getAllMovies();
        return;
    }

    $.ajax('../php/movies.php/filteredLen', {
        method: 'GET',
        data: {
            category: category,
            duration: duration,
            year: year,
        },
    }).done(function (data) {
        getMoviesFilterLength(data);
        var maxRow = (window.page - 1) * 25;
        $.ajax('../php/movies.php/filtered', {
            method: 'GET',
            data: {
                category: category,
                duration: duration,
                year: year,
                sorting: sorting,
                maxrow: maxRow
            },
        }).done(function (data) {
            createCard(data);
        });
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
    $("#sorting-select").val("title_asc");
    window.page = 1;
    window.nbpages = window.allpages;
    pagination();
    getAllMovies();
});

// Sort the movies
$("#sorting-select").change(function () {
    window.page = 1;
    pagination();

    if (window.filtered) {
        getFilteredMovies();
    }
    else {
        getAllMovies();
    }
});

// -------------------------------------------------------
// ------------------ CARD CREATION ----------------------
// -------------------------------------------------------

function createCard(data){


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
        detailsButton.attr('onclick', 'window.location.href = "https://tvrecap.epeigne.fr/movieDetails?id=' + data[i].id + '";');
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
        setTimeout(function () {
            $('#loading').remove();
            movieCard.fadeIn(500);
            $("#pagination").css("display", "flex");
        }, 1000);
    }
}

// -------------------------------------------------------
// -------------------- PAGINATION -----------------------
// -------------------------------------------------------

// Create a global variable to store the actual page
window.page = 1;

function getMoviesLength() {
    $.ajax({
        url: "../php/movies.php/length",
        method: "GET",
    }).done(function (data) {
        var nbpages = Math.ceil(data / 25);
        // Create a global variable to store the number of pages
        window.nbpages = nbpages;
        window.allpages = nbpages;
        pagination();
    });
}

function getMoviesFilterLength(data){

    var data_length = data;

    var nbpages = Math.ceil(data_length / 25);

    window.nbpages = nbpages;

    pagination();
}

// Create pagination
function pagination() {

    // Empty the pagination
    $("#pagination").empty();

    if (window.nbpages == 1) {
        return;
    }

    // Create the pagination
    if (window.page > 1 && window.page < window.nbpages) {
        $("#pagination").append("<button id='previous-page'>Précédent</button>");

        var previous = window.page - 1;
        var next = window.page + 1;

        $("#pagination").append("<section id='page-number'><span>" + previous + "</span> - <span style='text-decoration:underline; color:red'>" + window.page + "</span> - <span>" + next + "</span></section>");
        $("#pagination").append("<button id='next-page'>Suivant</button>");
    }
    else{
        if (window.page == 1) {
        
            var next = window.page + 1;

            $("#pagination").append("<section id='page-number'><span style='text-decoration:underline; color:red'>" + window.page + "</span> - <span>" + next + "</span></section>");
            $("#pagination").append("<button id='next-page'>Suivant</button>");
        }
        else{
            $("#pagination").append("<button id='previous-page'>Précédent</button>");

            var previous = window.page - 1;

            $("#pagination").append("<section id='page-number'><span>" + previous + "</span> - <span style='text-decoration:underline; color:red'>" + window.page + "</span></section>");
        }
    }

}

// Next page
$("#pagination").on("click", "#next-page", function () {
    window.page++;
    pagination();

    if (window.filtered) {
        getFilteredMovies();
    }
    else{
        getAllMovies();
    }

});

// Previous page
$("#pagination").on("click", "#previous-page", function () {
    window.page--;
    pagination();

    if (window.filtered) {
        getFilteredMovies();
    }
    else{
        getAllMovies();
    }
});

// -------------------------------------------------------
// ---------------------- GO UP --------------------------
// -------------------------------------------------------

// Go up button
$(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
        $('#go-top').fadeIn();
    } else {
        $('#go-top').fadeOut();
    }
});

// When the user clicks on the go up button
$('#go-top').click(function () {
    $('html, body').animate({ scrollTop: 0 }, 800);
    return false;
});

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

    // Get the number of pages
    getMoviesLength();
});
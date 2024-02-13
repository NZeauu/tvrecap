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
        "Drame",
        "Horreur",
        "Mystère",
        "Science-fiction",
        "Fantastique",
        "Policier",
        "Thriller",
        "Romance",
        "Historique",
        "Biographique",
        "Documentaire",
        "Fantasy",
        "Superhéros",
        "Famille",
        "Espionnage",
        "Western"
    ];

    for (let i = 0; i < list.length; i++) {
        $('#category').append($('<option>', {
            value: list[i],
            text: list[i]
        }));
    }
}

// -------------------------------------------------------
// --------------------- SERIES --------------------------
// -------------------------------------------------------

// Get the series from the database
function getAllSeries() {
    $.ajax('../php/series.php/all', {
        method: 'GET',
    }).done(function (data) {
        // console.log(data);
        createCard(data);
    });
}

// Get series filtered
function getFilteredSeries() {

    var seasons = $("#seasons").val();
    var category = $("#category").val();
    var duration = $("#duration").val();
    var year = $("#year").val();

    if (seasons == "all" && category == "all" && duration == "all" && year == "all") {
        getAllSeries();
        return;
    }

    $.ajax('../php/series.php/filtered', {
        method: 'GET',
        data: {
            seasons: seasons,
            category: category,
            duration: duration,
            year: year,
        },
    }).done(function (data) {
        // console.log(data);
        createCard(data);
    });
}

// Get series sorted
$("#filter-button").click(function () {
    getFilteredSeries();
});

// Reset the filters
$("#reset-button").click(function () {
    $("#category").val("all");
    $("#duration").val("all");
    $("#year").val("all");
    $("#seasons").val("all");
    getAllSeries();
});


// -------------------------------------------------------
// ------------------ CARD CREATION ----------------------
// -------------------------------------------------------

function createCard(data){

    // Empty the list
    $('#list').empty();

    $('#list').html('<h1 id="loading" style="font-size: 50px"> Loading... </h1>');
    $('#loading').attr('style', 'text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter;');

    // If the data is empty or null print a message
    if (data === null || data === "") {
        const message = $('<p>').text('Aucune série à afficher !');
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

        // Create the serie card
        const serieCard = $('<div>').attr('class', 'serie-card');
        const cardContent = $('<div>').attr('class', 'card-content');

        // Get the serie image
        const serieCardImg = $('<div>').attr('id', 'serie-card-img' + i);
        serieCardImg.attr('class', 'serie-card-img');

        // Get the image from the database
        const image = $('<img>').attr('src', data[i].image);
        serieCardImg.append(image);

        const serieCardInfo = $('<div>').attr('class', 'serie-card-info');

        const serieCardTitle = $('<div>').attr('class', 'serie-card-title');

        const serieTitle = $('<div>').attr('class', 'serie-title');
        const titleSection = $('<section>').text(data[i].nom);
        serieTitle.append(titleSection);

        const serieYear = $('<div>').attr('class', 'serie-year');
        const yearSection = $('<section>').text(data[i].date_sortie);
        serieYear.append(yearSection);

        serieCardTitle.append(serieTitle);
        serieCardTitle.append(serieYear);

        const separatorLine = $('<div>').attr('class', 'separator-line');

        const bottomCard = $('<div>').attr('class', 'bottom-card');

        const serieCardDetails1 = $('<div>').attr('class', 'serie-card-details');

        const serieCardCategory = $('<div>').attr('class', 'serie-card-category');
        const categoryTitleSection = $('<section>').attr('class', 'category-title').text('Catégorie(s)');
        const serieCategorySection = $('<section>').attr('class', 'serie-category').text(data[i].categorie);
        serieCardCategory.append(categoryTitleSection);
        serieCardCategory.append(serieCategorySection);

        const serieCardDuration = $('<div>').attr('class', 'serie-card-duration');
        const durationTitleSection = $('<section>').attr('class', 'duration-title').text('Durée moyenne d\'un épisode');
        const serieDurationSection = $('<section>').attr('class', 'serie-duration');
        serieDurationSection.attr('id', 'serie-duration' + data[i].id);

        // Get the average duration of an episode
        $.ajax('../php/series.php/duration', {
            method: 'GET',
            data: {
                id_serie: data[i].id
            },
        }).done(function (data) {
            // console.log(data);
            var duration = data['average_duration'];

            if(duration > 60){
                var hours = Math.floor(duration / 60);
                var minutes = duration % 60;

                // If the minutes are a float, round them
                if (minutes % 1 !== 0) {
                    minutes = Math.round(minutes);
                }

                // Add a 0 if the minutes are less than 10
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }

                $('#serie-duration' + data.serie_id).text(hours + 'h' + minutes);
            }
            else{

                // If the minutes are a float, round them
                if (duration % 1 !== 0) {
                    duration = Math.round(duration);
                }

                // Add a 0 if the minutes are less than 10
                if (duration < 10) {
                    duration = '0' + duration;
                }

                $('#serie-duration' + data.serie_id).text(duration + 'min');
            }
        });
        serieCardDuration.append(durationTitleSection);
        serieCardDuration.append(serieDurationSection);

        serieCardDetails1.append(serieCardCategory);
        serieCardDetails1.append(serieCardDuration);

        const serieCardDetails2 = $('<div>').attr('class', 'serie-card-details');

        const serieCardSeasons = $('<div>').attr('class', 'serie-card-seasons');
        const seasonsTitleSection = $('<section>').attr('class', 'seasons-title').text('Nombre de saison(s)');
        const serieSeasonsSection = $('<section>').attr('class', 'serie-category').text(data[i].nb_saisons);
        serieCardSeasons.append(seasonsTitleSection);
        serieCardSeasons.append(serieSeasonsSection);

        serieCardDetails2.append(serieCardSeasons);

        const serieDetailsButton = $('<div>').attr('class', 'serie-details-but');
        const detailsButton = $('<button>').attr('class', 'serie-details-button').text('Détails');
        detailsButton.attr('onclick', 'window.location.href = "serie-details.html?id=' + data[i].id + '";');
        detailsButton.attr('value', data[i].id);
        serieDetailsButton.append(detailsButton);

        bottomCard.append(serieCardDetails1);
        bottomCard.append(serieCardDetails2);
        bottomCard.append(serieDetailsButton);

        serieCardInfo.append(serieCardTitle);
        serieCardInfo.append(separatorLine);
        serieCardInfo.append(bottomCard);

        cardContent.append(serieCardImg);
        cardContent.append(serieCardInfo);

        serieCard.append(cardContent);

        serieCard.attr('style', 'display: none;');

        // Append the serie card to the body of the document
        $('#list').append(serieCard);

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

    // Get all the series from the database
    getAllSeries();

    // Fill the select with years
    fillYears();

    // Fill the select with categories
    fillCategories();
});
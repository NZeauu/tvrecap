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



// Get the image of the serie
function getImage(id, imgNumber) {

    var imageId = id;

    // AJAX request to get the image
    $.ajax({
        url: '../php/cover.php/cover',
        type: 'GET',
        data: { id_cover: imageId },
        success: function (data) {
            // Create the image tag with the base64 data
            $('#serie-card-img' + imgNumber).html('<img src="data:image/jpeg;base64,' + data + '" alt="Image">');

        },
        error: function () {
            console.error('Erreur lors de la récupération de l\'image');
        }
    });

}



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
        const serieCard = $('<div>').attr('id', 'serie-card');
        const cardContent = $('<div>').attr('id', 'card-content');

        // Get the serie image
        const serieCardImg = $('<div>').attr('id', 'serie-card-img' + i);
        serieCardImg.attr('class', 'serie-card-img');

        // getImage(data[i].image_id, i);

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

        const separatorLine = $('<div>').attr('id', 'separator-line');

        const bottomCard = $('<div>').attr('id', 'bottom-card');

        const serieCardDetails1 = $('<div>').attr('id', 'serie-card-details');

        const serieCardCategory = $('<div>').attr('id', 'serie-card-category');
        const categoryTitleSection = $('<section>').attr('id', 'category-title').text('Catégorie(s)');
        const serieCategorySection = $('<section>').attr('id', 'serie-category').text(data[i].categorie);
        serieCardCategory.append(categoryTitleSection);
        serieCardCategory.append(serieCategorySection);

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

        serieCardDetails1.append(serieCardCategory);
        serieCardDetails1.append(serieCardDuration);

        const serieCardDetails2 = $('<div>').attr('id', 'serie-card-details');

        const serieCardSeasons = $('<div>').attr('id', 'serie-card-seasons');
        const seasonsTitleSection = $('<section>').attr('id', 'seasons-title').text('Nombre de saison(s)');
        const serieSeasonsSection = $('<section>').attr('id', 'serie-category').text(data[i].nb_saisons);
        serieCardSeasons.append(seasonsTitleSection);
        serieCardSeasons.append(serieSeasonsSection);

        serieCardDetails2.append(serieCardSeasons);

        const serieDetailsButton = $('<div>').attr('id', 'serie-details-but');
        const detailsButton = $('<button>').attr('id', 'serie-details-button').text('Détails');
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

    // Get all the series from the database
    getAllSeries();

    // Fill the select with years
    fillYears();

    // Fill the select with categories
    fillCategories();
});
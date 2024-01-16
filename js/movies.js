// -------------------------------------------------------
// --------------------- COOKIES -------------------------
// -------------------------------------------------------

// Cookie check
function cookieCheck() {

    if(document.cookie.indexOf("user_id") === -1) {

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
    var email = getCookie("user_id");

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
            category: $("#category").val(),
            duration: $("#duration").val(),
            year: $("#year").val(),
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





// Get the image of the movie
function getImage(id, imgNumber) {

    var imageId = id;

    // Faites une requête AJAX pour récupérer l'image
    $.ajax({
        url: '../php/cover.php/cover',
        type: 'GET',
        data: { id_cover: imageId },
        success: function (data) {
            // Mettez à jour le contenu de l'élément avec l'image récupérée
            // $('#list').html('<img src="data:image/jpeg;base64,' + data + '" alt="Image">');
            $('#movie-card-img' + imgNumber).html('<img src="data:image/jpeg;base64,' + data + '" alt="Image">');

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

    // console.log(data);

    // Empty the list
    $('#list').empty();

    // If the data is empty or null print a message
    if (data === null || data === "") {
        const message = $('<p>').text('Aucun film à afficher !');
        $('#list').append(message);

        // Create style for the message
        const style = $('<style>').text('#list p {text-align: center; font-size: 2em; margin-top: 50px; font-family: Inter}');
        $('head').append(style);

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

        getImage(data[i].id, i);
        movieCardImg.append(movieCardImg);

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

        const separatorLine = $('<div>').attr('id', 'separator-line');

        const bottomCard = $('<div>').attr('id', 'bottom-card');

        const movieCardDetails = $('<div>').attr('id', 'movie-card-details');

        const movieCardCategory = $('<div>').attr('id', 'movie-card-category');
        const categoryTitleSection = $('<section>').attr('id', 'category-title').text('Catégorie');
        const movieCategorySection = $('<section>').attr('id', 'movie-category').text(data[i].categorie);
        movieCardCategory.append(categoryTitleSection);
        movieCardCategory.append(movieCategorySection);

        const movieCardDuration = $('<div>').attr('id', 'movie-card-duration');
        const durationTitleSection = $('<section>').attr('id', 'duration-title').text('Durée');
        const movieDurationSection = $('<section>').attr('id', 'movie-duration').text(hours + 'h' + minutes);
        movieCardDuration.append(durationTitleSection);
        movieCardDuration.append(movieDurationSection);

        movieCardDetails.append(movieCardCategory);
        movieCardDetails.append(movieCardDuration);

        const movieDetailsButton = $('<div>').attr('id', 'movie-details-but');
        const detailsButton = $('<button>').attr('id', 'movie-details-button').text('Détails');
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

        // Append the movie card to the body of the document
        $('#list').append(movieCard);
    }
}

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user
function disconnect() {
    // Delete the cookie
    document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
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

    // Get all the movies from the database
    getAllMovies();

    // Fill the select with years
    fillYears();

    // Fill the select with categories
    fillCategories();
});
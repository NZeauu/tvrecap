import { cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the user is connected
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ---------------------- CHOICE -------------------------
// -------------------------------------------------------

// Header select choice display
$(".series-content").on("click", function () {
    // Empty the form
    $("#serie-title").val("");
    $("#serie-year").val("2024");
    $("#results").empty();
    $("#no-results").css("display", "none");
    $("#loading").css("display", "none");
   
    seriesChoice();

});

$(".movies-content").on("click", function () {
    // Empty the form
    $("#movie-title").val("");
    $("#movie-year").val("2024");
    $("#results").empty();
    $("#no-results").css("display", "none");
    $("#loading").css("display", "none");
    
    moviesChoice();
});

function moviesChoice() {

    $("#movies-content-border").attr("style", "")
    $("#series-content-border").attr("style", "")
    $(".series-content").attr("style", "")
    $(".movies-content").attr("style", "")

    $(".movies-content").css({
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
    
    // Get the movies form
    getMovieForm();
}

function seriesChoice() {

    $("#movies-content-border").attr("style", "")
    $("#series-content-border").attr("style", "")
    $(".movies-content").attr("style", "")
    $(".series-content").attr("style", "")

    $(".series-content").css({
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

    // Get the series form
    getSerieForm();
}

// Get the movies form
function getMovieForm() {
    $("#serie-form").attr("style", "display: none;");
    $("#movie-form").attr("style", "display: auto;");
}

// Get the series form
function getSerieForm() {
    $("#movie-form").attr("style", "display: none;");
    $("#serie-form").attr("style", "display: auto;");
}

// -------------------------------------------------------
// ---------------------- ADD ----------------------------
// -------------------------------------------------------


$("#search-movie").click(function () {
    var title = $("#movie-title").val();
    var year = $("#movie-year").val();

    $("#results").css("display", "none");
    $("#no-results").css("display", "none");
    $("#loading").css("display", "block");

    $.ajax('https://tvrecap.epeigne.fr/ad/php/adsearchcontent.php', {
        method: 'GET',
        data: {
            title: title,
            year: year,
            type: "movie"
        },
    }).done(function (data) {

        if (data == "No results") {
            $("#loading").fadeOut(500);
            setTimeout(function () {
                $("#no-results").css("display", "block");
            }, 500);
            return;
        }

        const resultsDiv = $("#results");
        resultsDiv.empty();

        for (let i = 0; i < data.length; i++) {
            const result = data[i];

            const resultTitle = result.title;
            const resultYear = new Date(result.release_date).getFullYear();
            const resultDuration = result.duration;
            const resultSynopsis = result.overview;
            const coverURL = result.poster_path;
            const resultGenre = result.genres;

            let movie = $("<div></div>");
            movie.attr("class", "movie");
            movie.attr("id", "movie" + i);

            let title = $("<section>" + resultTitle + " (" + resultYear + ")</section>");
            movie.append(title);

            let content = $("<div></div>");
            content.attr("class", "content");

            if (coverURL == '../img/noimg.png') {
                let img = $("<img>");
                img.attr("src", "../img/noimg.png");
                img.attr("alt", "cover");
                img.attr("class", "cover");
                content.append(img);
            }
            else {
                let img = $("<img>");
                img.attr("src", "https://" + coverURL);
                img.attr("alt", "cover");
                img.attr("class", "cover");
                content.append(img);                
            }

            const rightContent = $("<div></div>");
            rightContent.attr("class", "right-content");
            
            let durationDiv = $("<div></div>");
            durationDiv.attr("class", "duration");
            durationDiv.append("<section>Durée: " + resultDuration + " min</section>");
            rightContent.append(durationDiv);

            let actorsDiv = $("<div></div>");
            actorsDiv.attr("class", "actors");

            let actors = "";

            for (let j = 0; j < result.actors.length; j++) {
                actors += result.actors[j];

                if (j < result.actors.length - 1) {
                    actors += ", ";
                }
            }

            actorsDiv.append("<section>Acteurs: " + actors + "</section>");
            rightContent.append(actorsDiv);

            let genreDiv = $("<div></div>");
            genreDiv.attr("class", "genre");

            let genres = "";

            for (let j = 0; j < resultGenre.length; j++) {
                genres += resultGenre[j];

                if (j < resultGenre.length - 1) {
                    genres += ", ";
                }
            }

            genreDiv.append("<section>Genre: " + genres + "</section>");
            rightContent.append(genreDiv);

            let synopsisDiv = $("<div></div>");
            synopsisDiv.attr("class", "synopsis");
            synopsisDiv.append("<section>" + resultSynopsis + "</section>");
            rightContent.append(synopsisDiv);
            content.append(rightContent);

            let button = $("<button>Ajouter</button>");
            button.attr("class", "valid-button");
            button.attr("id", "valid-button" + data[i].id);

            button.click(function () {
                $("#results").empty();
                createValidForm(resultTitle, resultYear, resultDuration, resultSynopsis, coverURL, actors, genres, "movie");
            });

            content.append(button);

            movie.append(content);

            resultsDiv.append(movie);
        }

        // Wait for the results to be displayed before hiding the loading icon
        // Do a fade in effect to make the results appear smoothly
        $("#loading").fadeOut(500);
        setTimeout(function () {
            $("#results").fadeIn(500);
        }, 500);

    });
});

$("#search-serie").click(function () {
    var title = $("#serie-title").val();
    var year = $("#serie-year").val();

    $("#results").css("display", "none");
    $("#no-results").css("display", "none");
    $("#loading").css("display", "block");

    $.ajax('https://tvrecap.epeigne.fr/ad/php/adsearchcontent.php', {
        method: 'GET',
        data: {
            title: title,
            year: year,
            type: "serie"
        },
    }).done(function (data) {

        if (data == "No results") {
            $("#loading").fadeOut(500);
            setTimeout(function () {
                $("#no-results").css("display", "block");
            }, 500);
            return;           
        }

        for (let i = 0; i < data.length; i++) {
            const result = data[i];

            const resultsDiv = $("#results");
            resultsDiv.empty();

            const resultTitle = result.name;
            const resultYear = new Date(result.first_air_date).getFullYear();
            const resultSynopsis = result.overview;
            const coverURL = result.poster_path;
            const resultGenre = result.genres;

            let serie = $("<div></div>");
            serie.attr("class", "serie");
            serie.attr("id", "serie" + i);

            let title = $("<section>" + resultTitle + " (" + resultYear + ")</section>");
            serie.append(title);

            let content = $("<div></div>");
            content.attr("class", "content");

            if (coverURL == 'https://tvrecap.epeigne.fr/ad/img/noimg.png') {
                let img = $("<img>");
                img.attr("src", "https://tvrecap.epeigne.fr/ad/img/noimg.png");
                img.attr("alt", "cover");
                img.attr("class", "cover");
                content.append(img);
            }
            else {
                let img = $("<img>");
                img.attr("src", "https://" + coverURL);
                img.attr("alt", "cover");
                img.attr("class", "cover");
                content.append(img);                
            }

            const rightContent = $("<div></div>");
            rightContent.attr("class", "right-content");
            

            let actorsDiv = $("<div></div>");
            actorsDiv.attr("class", "actors");

            let actors = "";

            for (let j = 0; j < result.actors.length; j++) {
                actors += result.actors[j];

                if (j < result.actors.length - 1) {
                    actors += ", ";
                }
            }

            actorsDiv.append("<section>Acteurs: " + actors + "</section>");
            rightContent.append(actorsDiv);

            let genreDiv = $("<div></div>");
            genreDiv.attr("class", "genre");

            let genres = "";

            for (let j = 0; j < resultGenre.length; j++) {
                genres += resultGenre[j];

                if (j < resultGenre.length - 1) {
                    genres += ", ";
                }
            }

            genreDiv.append("<section>Genre: " + genres + "</section>");
            rightContent.append(genreDiv);

            let synopsisDiv = $("<div></div>");
            synopsisDiv.attr("class", "synopsis");
            synopsisDiv.append("<section>" + resultSynopsis + "</section>");
            rightContent.append(synopsisDiv);
            content.append(rightContent);

            let button = $("<button>Ajouter</button>");
            button.attr("class", "valid-button");
            button.attr("id", "valid-button" + data[i].id);

            button.click(function () {
                $("#results").empty();
                createValidForm(resultTitle, resultYear, null, resultSynopsis, coverURL, actors, genres, "serie", result.id, result.nb_seasons);
            });

            content.append(button);
            serie.append(content);
            resultsDiv.append(serie);

        }

        // Wait for the results to be displayed before hiding the loading icon
        // Do a fade in effect to make the results appear smoothly
        $("#loading").fadeOut(500);
        setTimeout(function () {
            $("#results").fadeIn(500);
        }, 500);


    });

});

// Take informations from the movie card and create a validation form
// Use to add a movie to the database
function createValidForm(title, year, duration, synopsis, coverURL, actors, genres, type, contentID = null, nbSeasons = null) {

    $("#results").empty();
    let dataContent = $("<div></div>");
    dataContent.attr("class", "valid-form");

    let titleDiv = $("<div></div>");
    titleDiv.attr("class", "valid-title");
    titleDiv.append("<section><span id='mov-title'>" + title + "</span> (<span id='mov-year'>" + year + "</span>)</section>");
    dataContent.append(titleDiv);

    if (duration != null) {
        let durationDiv = $("<div></div>");
        durationDiv.attr("class", "valid-duration");
        durationDiv.append("<section>Durée: <span id='mov-duration'>" + duration + "</span> min</section>");
        dataContent.append(durationDiv);
    }

    let synopsisDiv = $("<div></div>");
    synopsisDiv.attr("class", "valid-synopsis");
    synopsisDiv.append("<section id='mov-synopsis'>" + synopsis + "</section>");
    dataContent.append(synopsisDiv);

    let addingDiv = $("<div></div>");
    addingDiv.attr("class", "adding");
    
    if (coverURL == '../img/noimg.png') {
        let imgDiv = $("<div></div>");
        imgDiv.attr("class", "valid-cover");
        imgDiv.append("<label for='mov-cover'>URL de la couverture: </label>");
        imgDiv.append("<input type='text' id='mov-cover' name='mov-cover' required>");
        addingDiv.append(imgDiv);
    }
    else {
        let img = $("<img>");
        img.attr("src", "https://" + coverURL);
        img.attr("alt", "cover");
        img.attr("class", "cover");
        addingDiv.append(img);
    }

    let rightContent = $("<div></div>");
    rightContent.attr("class", "right-content");

    let genreDiv = $("<div></div>");
    genreDiv.attr("class", "valid-genre");

    if (genres == "") {
        genreDiv.append("<label for='mov-genre'>Genre: </label>");
        genreDiv.append("<input type='text' id='mov-genre' name='mov-genre' required>");
        rightContent.append(genreDiv);
    }
    else {
        genreDiv.append("<label for='mov-genre'>Genre: </label>");
        genreDiv.append("<input type='text' id='mov-genre' name='mov-genre' value='" + genres + "' required disabled>");
        rightContent.append(genreDiv);
    }

    let actorsDiv = $("<div></div>");

    if (actors == "") {
        actorsDiv.attr("class", "valid-actors");
        actorsDiv.append("<label for='mov-actors'>Acteurs: </label>");
        actorsDiv.append("<input type='text' id='mov-actors' name='mov-actors' required>");
        rightContent.append(actorsDiv);
    }
    else {
        actorsDiv.attr("class", "valid-actors");
        actorsDiv.append("<label for='mov-actors'>Acteurs: </label>");
        actorsDiv.append("<input type='text' id='mov-actors' name='mov-actors' value='" + actors + "' required disabled>");
        rightContent.append(actorsDiv);
    }

    let directorDiv = $("<div></div>");
    directorDiv.attr("class", "valid-director");
    directorDiv.append("<label for='mov-director'>Réalisateur: </label>");
    directorDiv.append("<input type='text' id='mov-director' name='mov-director' required>");
    rightContent.append(directorDiv);

    addingDiv.append(rightContent);

    dataContent.append(addingDiv);

    let buttonDiv = $("<div></div>");
    buttonDiv.attr("class", "button-div");
    
    let button = $("<button>Ajouter</button>");
    button.attr("class", "add-button");
    button.on("click", function () {

        if ($("#mov-director").val() === "" || ($("#mov-cover").val() === "" && coverURL == 'https://tvrecap.epeigne.fr/ad/img/noimg.png')) {
            alert("Veuillez remplir tous les champs");
            return;
        }

        // Get the movie's informations
        var movieTitle = $("#mov-title").text();
        var movieYear = $("#mov-year").text();
        var movieDuration = $("#mov-duration").text();
        var movieSynopsis = $("#mov-synopsis").text();

        var movieCover = $(".cover").attr("src") || $("#mov-cover").val();
        // Add the https:// to the cover URL if it's not already there
        if (movieCover.substring(0, 8) !== "https://"){
            movieCover = "https://" + movieCover;
        }

        var movieRealisator = $("#mov-director").val();
        var movieGenre = $("#mov-genre").val();
        var movieActors = $("#mov-actors").val();

        if (type == "movie"){
            $.ajax('https://tvrecap.epeigne.fr/ad/php/adadd.php/addmovie', {
                method: 'POST',
                data: {
                    title: movieTitle,
                    year:  movieYear,
                    duration: movieDuration,
                    synopsis: movieSynopsis,
                    realisator: movieRealisator,
                    coverURL: movieCover,
                    genre: movieGenre,
                    actors: movieActors
                },
            }).done(function (data) {
    
                if (data == "already exists") {
                    alert("Le film est déjà dans la base de données");
                    location.reload();
                    return;
                }
                alert("Film ajouté");
                location.reload();
            });
        }
        else if (type == "serie"){
            $.ajax('https://tvrecap.epeigne.fr/ad/php/adadd.php/addserie', {
                method: 'POST',
                data: {
                    title: movieTitle,
                    year:  movieYear,
                    synopsis: movieSynopsis,
                    realisator: movieRealisator,
                    coverURL: movieCover,
                    genre: movieGenre,
                    actors: movieActors,
                    contentID: contentID,
                    nbSeasons: nbSeasons
                },
            }).done(function (data) {
    
                if (data == "already exists") {
                    alert("La série est déjà dans la base de données");
                    location.reload();
                    return;
                }
                alert("Série ajoutée");
                location.reload();
            });
        }
        
    });

    buttonDiv.append(button);

    dataContent.append(buttonDiv);

    $("#results").append(dataContent);
}

// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------

$(document).ready(function () {
    // Get the user's name
    setUserName();

    getAvatar("#avatar-header");

    // Get the movies form
    moviesChoice();

    // Set parution date to this year by default
    var today = new Date();
    var year = today.getFullYear();
    $("#movie-year").val(year);
    $("#serie-year").val(year);

});

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user when he clicks on the logout button
$("#logout").click(function () {
    alert("Vous êtes déconnecté"); // Alert the user that he is disconnected
    disconnect();
});

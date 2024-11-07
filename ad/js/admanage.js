import { cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the user is connected
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ---------------------- CHOICE -------------------------
// -------------------------------------------------------

let choice = "";

// Header select choice display
$(".series-content").on("click", function () {
    // Empty the form
    $("#serie-title").val("");
    $("#serie-year").val("2024");
    $("#results").empty();
    $("#no-results").css("display", "none");
    $("#search-content-bar").val("");
    $("#pagination").css("display", "flex");
    $("#sorting-section").css("display", "flex");
    window.page = 1;
   
    choice = "serie";
    getSeriesLength();
    seriesChoice();

});

$(".movies-content").on("click", function () {
    // Empty the form
    $("#movie-title").val("");
    $("#movie-year").val("2024");
    $("#results").empty();
    $("#no-results").css("display", "none");
    $("#search-content-bar").val("");
    $("#pagination").css("display", "flex");
    $("#sorting-section").css("display", "flex");
    window.page = 1;
    
    choice = "movie";
    getMoviesLength();
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


    choice = "movie";

    // Get the movies
    getMovies();

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

    // Get the series
    getSeries();
}

// -------------------------------------------------------
// --------------------- CONTENT -------------------------
// -------------------------------------------------------

// Create a global variable to store the actual page
window.page = 1;

// Create pagination
function pagination() {

    // Empty the pagination
    $("#pagination").empty();

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
    if (choice == "serie") {
        getSeries();
    } else {
        getMovies();
    }
});

// Previous page
$("#pagination").on("click", "#previous-page", function () {
    window.page--;
    pagination();
    if (choice == "serie") {
        getSeries();
    } else {
        getMovies();
    }
});



function getMoviesLength() {
    $.ajax({
        url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/getMoviesLength",
        method: "GET",
    }).done(function (data) {
        var nbpages = Math.ceil(data / 25);
        // Create a global variable to store the number of pages
        window.nbpages = nbpages;
        pagination();
    });
}

function getSeriesLength() {
    $.ajax({
        url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/getSeriesLength",
        method: "GET",
    }).done(function (data) {
        var nbpages = Math.ceil(data / 25);
        // Create a global variable to store the number of pages
        window.nbpages = nbpages;
        pagination();
    });
}


// Get the movies on the database
function getMovies() {

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

    $.ajax({
        url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/getMovies",
        method: "GET",
        data: { 
            maxRow: maxRow,
            sorting: sorting
        },
        success: function (data) {

            displayContent(data, "movie");
        },
        error: function (error) {
            console.log(error);
        }
    });
    
}

// Get the series on the database
function getSeries() {

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

    $.ajax({
        url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/getSeries",
        method: "GET",
        data: {
            maxRow: maxRow,
            sorting: sorting
        },
        success: function (data) {
            displayContent(data, "serie");
        },
        error: function (error) {
            console.log(error);
        }
    });
}


// Display the content
function displayContent(data, type) {

    // Empty the results
    $("#results").empty();
    $("#no-results").css("display", "none");

    // If there are no content
    if (data.length == 0) {
        $("#no-results").css("display", "block");
    } else {
        // Display the content
        for (var i = 0; i < data.length; i++) {

            // If the type is a serie
            if (type == "serie") {
                $("#results").append(
                    "<div class='db-data'>" +
                    "<img src='../" + data[i].image + "' alt='poster' style='width:100px;height:150px;border-radius:10px'>" +
                    "<div class='serie-content'>" +
                    "<section style='font-weight:bold'>" + data[i].nom + " (<span>" + data[i].date_sortie + "</span>)" + "</section>" +
                    "<section>" + data[i].categorie + "</section>" +
                    "<section>" + data[i].synopsis + "</section>" +
                    "<button id='" + data[i].id + "' class='delete-serie'>Supprimer</button>" +
                    "</div>" +
                    "</div>"
                );
            } else {
                $("#results").append(
                    "<div class='db-data'>" +
                    "<img src='../" + data[i].image + "' alt='poster' style='width:100px;height:150px;border-radius:10px'>" +
                    "<div class='movie-content'>" +
                    "<section style='font-weight:bold'>" + data[i].nom + " (<span>" + data[i].date_sortie + "</span>)" + "</section>" +
                    "<section>" + data[i].categorie + "</section>" +
                    "<section>" + data[i].synopsis + "</section>" +
                    "<button id='" + data[i].id + "' class='delete-movie'>Supprimer</button>" +
                    "</div>" +
                    "</div>"
                );
            }
        }
    }

}

$("#results").on("click", ".delete-serie", function () {
    var id = $(this).attr("id");

    var r = confirm("Voulez-vous vraiment supprimer cette série ?");
    if (!r) return;
    
    $.ajax({
        url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/deleteSerie",
        method: "POST",
        data: { id: id },
        success: function (data) {
            getSeries();
        },
        error: function (error) {
            console.log(error);
        }
    });
});

$("#results").on("click", ".delete-movie", function () {
    var id = $(this).attr("id");

    var r = confirm("Voulez-vous vraiment supprimer ce film ?");
    if (!r) return;
    
    $.ajax({
        url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/deleteMovie",
        method: "POST",
        data: { id: id },
        success: function (data) {
            getMovies();
        },
        error: function (error) {
            console.log(error);
        }
    });
});

$("#search-content-bar").on("change", function () {
    var value = this.value;

    console.log(value);

    if (value != "") {
        $("#sorting-section").css("display", "none");
    } else {
        $("#sorting-section").css("display", "flex");
        if(choice == "serie"){
            getSeries();
            return;
        }else{
            getMovies();
            return;
        }
    }

    if (choice == "serie") {

        $.ajax({
            url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/searchSerie",
            method: "GET",
            data: { value: value },
            success: function (data) {

                if (data == "No series found") {
                    $("#results").empty();
                    $("#no-results").css("display", "block");
                    return;
                }

                displayContent(data, "serie");
            },
            error: function (error) {
                console.log(error);
            }
        });

    }
    if (choice == "movie") {

        $.ajax({
            url: "https://tvrecap.epeigne.fr/ad/php/admanage.php/searchMovie",
            method: "GET",
            data: { value: value },
            success: function (data) {

                if (data == "No movies found") {
                    $("#results").empty();
                    $("#pagination").css("display", "none");
                    $("#no-results").css("display", "block");
                    return;
                }

                displayContent(data, "movie");
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
  
});

$("#sorting-select").on("change", function () {
    if (choice == "serie") {
        window.page = 1;
        getSeries();
    } else {
        window.page = 1;
        getMovies();
    }
});


// -------------------------------------------------------
// ---------------------- ONLOAD -------------------------
// -------------------------------------------------------


$(document).ready(function () {
    // Get the user's name
    setUserName();

    getAvatar("#avatar-header");

    // Get the movies form
    moviesChoice();
    choice = "movie";
    getMoviesLength();

});

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user when he clicks on the logout button
$("#logout").click(function () {
    alert("Vous êtes déconnecté"); // Alert the user that he is disconnected
    disconnect();
});


document.getElementById('sorting-select').addEventListener('click', function() {
    var select = this;
    var arrow = document.createElement('div');
    arrow.className = 'select-arrow';
    select.parentNode.insertBefore(arrow, select.nextSibling);
  
    select.addEventListener('blur', function() {
      arrow.classList.remove('select-open');
    });
  
    select.addEventListener('focus', function() {
      arrow.classList.add('select-open');
    });
  });
  
import { getCookie, cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the user is connected
setInterval(cookieCheck, 1000);

// -------------------------------------------------------
// ---------------------- CHOICE -------------------------
// -------------------------------------------------------

// Header select choice display
$(".series-content").on("click", function () {

    // Empty the form
    // $("#serie-title").val("");
    // $("#serie-year").val("");
    // $("#serie-seasons").val("1");
    // $("#serie-synopsis").val("");
    
    
    seriesChoice();

});

$(".movies-content").on("click", function () {

    // Empty the form
    // $("#movie-title").val("");
    // $("#movie-year").val("");
    // $("#movie-duration").val("");
    // $("#movie-synopsis").val("");
    
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



// Ask a movie add
$("#submit-movie").click(function () {
    var title = $("#movie-title").val();
    var year = $("#movie-year").val();
    var duration = $("#movie-duration").val();
    var synopsis = $("#movie-synopsis").val();
    var realisator = $("#movie-director").val();
    var actors = $("#movie-actors").val();
    var genre = $("#movie-genre").val();

    // const image = $("#movie-image").files[0];
    const fileInput = document.getElementById('movie-image');
    const file = fileInput.files[0];
    var formData = new FormData();
    formData.append('file', file);
    formData.append('title', title);
    formData.append('year', year);

    if(file === undefined || title === "" || year === "" || duration === "" || synopsis === "" || realisator === "" || actors === "" || genre === "") {
        alert("Veuillez remplir tous les champs");
        return;
    }

    $.ajax({
        url: '../php/adadd.php/saveimg',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#uploadStatus').html(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            $('#uploadStatus').html('Error occurred while uploading the file.');
        }
    });

    $.ajax('../php/adadd.php/addmovie', {
        method: 'POST',
        data: {
            title: title,
            year: year,
            duration: duration,
            synopsis: synopsis,
            realisator: realisator,
            actors: actors,
            genre: genre
        },
    }).done(function (data) {
        alert("Film ajouté");
        location.reload();
    });
});

// Ask a serie add
$("#submit-serie").click(function () {
    var title = $("#serie-title").val();
    var year = $("#serie-year").val();

    if(title === "" || year === "") {
        alert("Veuillez remplir tous les champs");
        return;
    }

    $.ajax('../php/add.php/sendmail', {
        method: 'POST',
        data: {
            title: title,
            year: year,
            type: "serie"
        },
    }).done(function (data) {
        alert("Demande d'ajout de série envoyée");
        window.location.replace("home.html");
    });
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

    // Set parution date to this year by default
    var today = new Date();
    var year = today.getFullYear();
    $("#movie-year").val(year);
});

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user when he clicks on the logout button
$("#logout").click(function () {
    alert("Vous êtes déconnecté"); // Alert the user that he is disconnected
    disconnect();
});

import { cookieCheck, setUserName, getAvatar, disconnect } from "./mainContent.js";

// Check if the user is connected
setInterval(cookieCheck, 1000);


function addRow(){
    var tableBody = $("#users")

    $.ajax('../php/adusers.php/getUsers', {
        type: 'GET',
        success: function (data) {
            console.log(data);
            for (var i = 0; i < data.length; i++) {
                var row = $("<tr></tr>");

                var avatar = $("<td></td>");
                var img = $("<img>");
                img.attr("src", data[i].avatar);
                img.attr("alt", "Avatar");
                img.attr("id", "avatar" + data[i].id);
                img.css({"width": "100px", "height": "100px"});
                avatar.append(img);
                row.append(avatar);
                
                var username = $("<td></td>").text(data[i].username);
                username.attr("id", "username" + data[i].id);
                row.append(username);

                var email = $("<td></td>").text(data[i].email);
                email.attr("id", "email" + data[i].id);
                row.append(email);

                var birthday = $("<td></td>");
                if(data[i].birthday != null){
                    birthday.text(data[i].birthday);
                }
                else{
                    birthday.text("Non renseigné");
                }
                birthday.attr("id", "birthday" + data[i].id);
                row.append(birthday);

                var updateButton = $("<td></td>");
                var update = $("<button></button>").text("Modifier");
                update.attr("id", "update" + data[i].id);
                update.attr("class", "update");
                updateButton.append(update);
                row.append(updateButton);

                var deleteButton = $("<td></td>");
                var del = $("<button></button>").text("Supprimer");
                del.attr("id", "delete" + data[i].id);
                del.attr("class", "delete");
                deleteButton.append(del);
                row.append(deleteButton);

                tableBody.append(row);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
}

$(document).on("click", ".delete", function () {
    var id = $(this).attr("id").substring(6);
    var email = $("#email" + id).text();

    // Demande de confirmation
    var r = confirm("Voulez-vous vraiment supprimer cet utilisateur ?");
    if (r == false) {
        return;
    }
    
    $.ajax('../php/adusers.php/deleteUser', {
        type: 'POST',
        data: {
            email: email
        },
        success: function (data) {
            console.log(data);
            $("#users").empty();
            addRow();
        },
        error: function (data) {
            console.log(data);
        }
    });
    
});

$(document).on("click", ".update", function () {
    var id = $(this).attr("id").substring(6);

    $(".update").attr("style", "display: none;");
    // Add a button to validate the changes
    var validate = $("<button></button>").text("Valider");
    validate.attr("id", "validate" + id);
    validate.attr("class", "validate");
    $("#update" + id).parent().append(validate);  
    
    // Add a button to cancel the changes
    var cancel = $("<button></button>").text("Annuler");
    cancel.attr("id", "cancel" + id);
    cancel.attr("class", "cancel");
    cancel.on("click", function(){
        $("#users").empty();
        addRow();
    });
    cancel.attr("style", "margin-left: 5px;");
    $("#validate" + id).parent().append(cancel);

    var oldUsername = $("#username" + id).text();
    $("#username" + id).empty();
    var input = $("<input>");
    input.attr("type", "text");
    input.attr("id", "newUsername" + id);
    input.val(oldUsername);
    input.css({
        "width": "fit-content",
        "height": "20px",
        "border-radius": "5px",
        "border": "1px solid #000",
        "text-align": "center"
    })
    $("#username" + id).append(input);

    var oldEmail = $("#email" + id).text();
    $("#email" + id).empty();
    var input = $("<input>");
    input.attr("type", "text");
    input.attr("id", "newEmail" + id);
    input.val(oldEmail);
    input.css({
        "width": "fit-content",
        "height": "20px",
        "border-radius": "5px",
        "border": "1px solid #000",
        "text-align": "center"
    })
    $("#email" + id).append(input);

});

$(document).on("click", ".validate", function () {
    var id = $(this).attr("id").substring(8);

    var newUsername = $("#newUsername" + id).val();
    var newEmail = $("#newEmail" + id).val();

    $.ajax('../php/adusers.php/updateUser', {
        type: 'POST',
        data: {
            userId: id,
            username: newUsername,
            email: newEmail
        },
        success: function (data) {
            console.log(data);
            $("#users").empty();
            addRow();
        },
        error: function (data) {
            console.log(data);
        }
    });
});


$(document).ready(function () {
    // Get the user's name
    setUserName();

    getAvatar("#avatar-header");

    // Add the users to the table
    addRow();

});

// -------------------------------------------------------
// ---------------------- LOGOUT -------------------------
// -------------------------------------------------------

// Disconnect the user when he clicks on the logout button
$("#logout").click(function () {
    alert("Vous êtes déconnecté"); // Alert the user that he is disconnected
    disconnect();
});

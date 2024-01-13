$("#registerbutton").click(function() {

    //reset error messages
    $("#usernameinput").css("border-color", "#ccc");
    $("#userError").css("display", "none");
    $("#userUsed").css("display", "none");

    $("#passwordinput").css("border-color", "#ccc");
    $("#passwordError").css("display", "none");
    $("#passwordInvalid").css("display", "none");

    $("#mailinput").css("border-color", "#ccc");
    $("#mailError").css("display", "none");
    $("#mailInvalid").css("display", "none");
    $("#mailUsed").css("display", "none");


    //register function here
    var username = $("#usernameinput").val();
    var password = $("#passwordinput").val();
    var email = $("#mailinput").val();

    var allFilled = true;
    var allValid = true;


    if (username == "") {
        $("#usernameinput").css("border-color", "red");
        $("#userError").css("display", "block");
        allFilled = false;
    }
    if (password == "") {
        $("#passwordinput").css("border-color", "red");
        $("#passwordError").css("display", "block");
        allFilled = false;
    }
    if (email == "") {
        $("#mailinput").css("border-color", "red");
        $("#mailError").css("display", "block");
        allFilled = false;
    }

    //check if mail format is correct
    var mailFormat = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!mailFormat.test(email) && email != "") {
        $("#mailinput").css("border-color", "red");
        $("#mailInvalid").css("display", "block");
        allFilled = false;
    }

    //check if email is already used
    $.ajax('../php/register.php/mail', {
        method : 'GET', data : {
            email : email
        }
    }).done(function(data) {
        if (data) {
            $("#mailinput").css("border-color", "red");
            $("#mailUsed").css("display", "block");
            allValid = false;
        }

        //check if username is already used
        $.ajax('../php/register.php/username', {
            method : 'GET', data : {
                username : username
            }
        }).done(function(data) {
            if (data) {
                $("#usernameinput").css("border-color", "red");
                $("#userUsed").css("display", "block");
                allValid = false;
            }

            if (allFilled && allValid) {
                //send data to server with ajax request
                $.ajax('../php/register.php/register', {
                    method : 'POST', data : {
                        username : username,
                        password : password,
                        email : email
                    }
                }).done(function(data) {
                    if (data) {
                        //if register successful alert and redirect to login page
                        alert("Inscription réussie. Vous pouvez maintenant vous connecter.");
                        // redirect to login page
                        window.location.href = "../html/login.html";
                    } else {
                        alert("Erreur lors de l'inscription. Veuillez réessayer.");
                    }
                });
            }
        });
    });
    
});
// Check if the token exists in the URL
var url = window.location.href;
var token = url.split("?")[1];
if(!token){
    // window.location.href = "../html/login.html";
}

var userToken = token.split("=")[1];

// Check if the token is valid
$.ajax('../php/reset-pass.php/checkToken', {
    method : 'GET', data : {
        token : userToken
    }
}).done(function(data){
    if(!data){
        alert("Votre lien de réinitialisation de mot de passe est invalide ou a expiré.");
        window.location.href = "../html/login.html";
    }
    else{
        $("#tokenValid").css("display", "block");

        // Get the email
        var email = data;
        $("#mailinput").val(email);
    }
});

// Reset the password
$("#resetbutton").on("click", function(){

    // Reset the error message
    $("#passwordInvalid").css("display", "none");
    $("#passwordEmpty").css("display", "none");

    // Get the password and password confirmation
    var password = $("#passwordinput").val();
    var confirmpassword = $("#passwordinput2").val();

    // Get the email
    var email = $("#mailinput").val();

    // Check if the password is empty
    if(password == "" || confirmpassword == ""){
        $("#passwordEmpty").css("display", "block");
    }
    else{
        // Check if the password and the confirmation are the same
        if(password != confirmpassword){
            $("#passwordInvalid").css("display", "block");
        }
        else{
            // Reset the password
            $.ajax('../php/reset-pass.php/resetPassword', {
                method : 'POST', data : {
                    token : userToken, 
                    password : password,
                    email: email
                }
            }).done(function(data){
                if(data){
                    alert("Votre mot de passe a été réinitialisé avec succès.");
                    window.location.href = "../html/login.html";
                }
                else{
                    alert("Erreur lors de la réinitialisation du mot de passe.");
                }
            });
        }
    }
});

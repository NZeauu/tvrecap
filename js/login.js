// If the user clicks on the login button, the login function is called
$("#loginbutton").click(function(){
    login();  
});

// If the user presses enter in the email or password input field, the login function is called
$("#mailinput").keypress(function(e){
    if(e.which == 13){
        login();
    }
});

$("#passwordinput").keypress(function(e){
    if(e.which == 13){
        login();
    }
});


// Login function
function login(){

    //reset error messages
    $("#mailinput").css("border-color", "#ccc");
    $("#mailInvalid").css("display", "none");
    $("#mailEmpty").css("display", "none");

    $("#passwordinput").css("border-color", "#ccc");
    $("#passwordInvalid").css("display", "none");
    $("#passwordEmpty").css("display", "none");


    var email = $("#mailinput").val();
    var password = $("#passwordinput").val();

    var allFilled = true;
    var allValid = true;

    if($("#mailinput").val() == ""){
        $("#mailinput").css("border-color", "#ff0000");
        $("#mailEmpty").css("display", "block");
        allFilled = false;
    }

    if($("#passwordinput").val() == ""){
        $("#passwordinput").css("border-color", "#ff0000");
        $("#passwordEmpty").css("display", "block");
        allFilled = false;
    }


    if(allFilled){
        //check if email is valid
        $.ajax('../php/connect.php/mail', {
            method : 'GET', data : {
                email : email
            }
        }).done(function(data){
            if(!data){
                $("#mailinput").css("border-color", "#ff0000");
                $("#mailInvalid").css("display", "block");
                $("#passwordinput").css("border-color", "#ff0000");
                $("#passwordinput").val("");
                allValid = false;
            }

            if(allValid){
                $.ajax('../php/connect.php/login', {
                    method : 'POST', data : {
                        email : email,
                        password : password
                    }
                }).done(function(data){
                    if(data){
                        window.location.href = "../html/home.html";
                    }else{
                        $("#passwordinput").css("border-color", "#ff0000");
                        $("#passwordInvalid").css("display", "block");
                    }
                });
            }
        });
    }

}
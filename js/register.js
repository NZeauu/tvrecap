// If the user clicks on the register button, the register function is called
$("#registerbutton").click(function() {
    register();    
});


// If the user presses enter in one of the input fields, the register function is called
$("#usernameinput").keypress(function(e) {
    if (e.which == 13) {
        register();
    }
});

$("#passwordinput").keypress(function(e) {
    if (e.which == 13) {
        register();
    }
});

$("#mailinput").keypress(function(e) {
    if (e.which == 13) {
        register();
    }
});

// Register function

function register(){
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
                        alert("Inscription réussie. Merci de valider votre adresse mail pour activer votre compte avant de vous connecter. Un mail de confirmation vous a été envoyé, veuillez vérifier votre boîte mail ainsi que vos spams");
                        // redirect to login page
                        window.location.href = "../html/login.html";
                    } else {
                        alert("Erreur lors de l'inscription. Veuillez réessayer.");
                    }
                });
            }
        });
    });
}



function openPopup(type) {
    var popup = document.getElementById('popup');
    popup.style.display = 'block';

    $("#popupTitle").empty();
    $("#popupText").empty();
    

    if(type == 'about'){
        document.getElementById('popupTitle').innerText = 'A propos';
        document.getElementById('popupText').innerText = 'TVRecap est un site web qui permet de garder une trace de tous les films et séries que vous avez regardés. Vous pouvez également proposer des contenus à ajouter au site.';
    }

    if(type == 'contact'){
        document.getElementById('popupTitle').innerText = 'Contact';
        // Création du formulaire de contact
        var form = document.createElement('div');
        form.setAttribute('id', 'contactForm');

        var name = document.createElement('input');
        name.setAttribute('type', 'text');
        name.setAttribute('name', 'name');
        name.setAttribute('placeholder', 'Nom');

        var email = document.createElement('input');
        email.setAttribute('type', 'email');
        email.setAttribute('name', 'email');
        email.setAttribute('placeholder', 'Email');


        var message = document.createElement('textarea');
        message.setAttribute('name', 'message');
        message.setAttribute('placeholder', 'Message');
    

        var submit = document.createElement('button');
        submit.setAttribute('type', 'submit');
        submit.setAttribute('value', 'Envoyer');
        submit.setAttribute('id', 'submit');
        submit.setAttribute('onclick', 'submit()');
        submit.innerText = 'Envoyer';

        form.appendChild(name);
        form.appendChild(email);
        form.appendChild(message);
        form.appendChild(submit);

        document.getElementById('popupText').appendChild(form);
    }


}

function closePopup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'none';
}

// Close the popup if the user clicks outside of it or presses the escape key
window.onclick = function(event) {
    var popup = document.getElementById('popup');
    if (event.target == popup) {
        popup.style.display = "none";
    }
}

document.onkeydown = function(evt) {
    evt = evt || window.event;
    var popup = document.getElementById('popup');
    if (evt.keyCode == 27) {
        popup.style.display = "none";
    }
};

function submit(){
    var name = $("#contactForm input[name='name']").val();
    var email = $("#contactForm input[name='email']").val();
    var message = $("#contactForm textarea[name='message']").val();

    // If one of the fields is empty or the email is not valid
    if(name == '' || email == '' || message == '' || !email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)){
        $("#contactForm").empty();
        $("#contactForm").append("<p>Veuillez remplir correctement tous les champs.</p>");
        $("#contactForm").append("<button onclick=\"openPopup('contact')\">Ok</button>");
        return;
    }

    $.ajax('../php/contact-form.php', {
        method : 'POST', 
        data : {
            username : name,
            userMail : email,
            subject : 'Contact Register Page',
            message : message
        },
        success: function(data){
            if(data){
                $("#contactForm").empty();
                $("#contactForm").append("<p>" + data.msg + "</p>");
            }else{
                $("#contactForm").empty();
                $("#contactForm").append("<p>Une erreur est survenue lors de l'envoi du message.</p>");
            }
        }
    });
}
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

                $.ajax('../php/connect.php/checkVerified', {
                    method : 'GET', data : {
                        email : email
                    }
                }).done(function(data){
                    if(!data){
                        window.userMail = email;
                        openPopup('notVerified');
                        return;
                    }

                    $.ajax('../php/connect.php/login', {
                        method : 'POST', data : {
                            email : email,
                            password : password
                        }
                    }).done(function(data){
                        if(data == "admin" || data == "user"){
                            if(data == "admin"){
                                window.location.href = "../ad/html/adhome.html";
                            }
                            if(data == "user"){
                                window.location.href = "../html/home.html";
                            }
                        }else{
                            $("#passwordinput").css("border-color", "#ff0000");
                            $("#passwordInvalid").css("display", "block");
                        }
                    });
                });
            }
        });
    }

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

    if (type == 'password') {
        document.getElementById('popupTitle').innerText = 'Mot de passe oublié';

        var form = document.createElement('div');
        form.setAttribute('id', 'passwordForm');

        var contentTitle = document.createElement('p');
        contentTitle.innerText = 'Entrez votre adresse mail pour recevoir un lien de réinitialisation de mot de passe.';
        form.appendChild(contentTitle);
        
        var mailInput = document.createElement('input');
        mailInput.setAttribute('type', 'email');
        mailInput.setAttribute('name', 'mail');
        mailInput.setAttribute('placeholder', 'Email');
        mailInput.setAttribute('id', 'mailInput');

        var submit = document.createElement('button');
        submit.setAttribute('type', 'submit');
        submit.setAttribute('value', 'Envoyer');
        submit.setAttribute('id', 'submit');
        submit.setAttribute('onclick', 'sendMail()');
        submit.innerText = 'Envoyer';

        form.appendChild(mailInput);
        form.appendChild(submit);

        document.getElementById('popupText').appendChild(form);
    }

    if (type == 'notVerified') {
        document.getElementById('popupTitle').innerText = 'Compte non vérifié';
        var content = document.createElement('div');
        content.setAttribute('id', 'notVerifiedContent');
        var text = document.createElement('p');
        text.innerText = 'Votre compte n\'a pas encore été vérifié. Veuillez vérifier votre boîte mail ainsi que vos spams pour activer votre compte. Si vous n\'avez pas reçu de mail, veuillez cliquer sur le bouton ci-dessous pour renvoyer un mail de confirmation.';
        var resend = document.createElement('button');
        resend.setAttribute('onclick', 'resendMail()');
        resend.innerText = 'Renvoyer le mail de confirmation';
        content.appendChild(text);
        content.appendChild(resend);
        document.getElementById('popupText').appendChild(content);
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
            subject : 'Contact Login Page',
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

function sendMail(){
    var email = $("#mailInput").val();

    if(email == '' || !email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)){
        $("#passwordForm").empty();
        $("#passwordForm").append("<p>Veuillez remplir correctement tous les champs.</p>");
        $("#passwordForm").append("<button onclick=\"openPopup('password')\">Ok</button>");
        return;
    }

    // Check if the email exists in the database
    $.ajax('../php/connect.php/mail', {
        method : 'GET', data : {
            email : email
        }
    }).done(function(data){
        if(!data){
            $("#passwordForm").empty();
            $("#passwordForm").append("<p>Cette adresse mail n'existe pas.</p>");
            $("#passwordForm").append("<button onclick=\"openPopup('password')\">Ok</button>");
        }else{

            $.ajax('../php/connect.php/resetPass', {
                method : 'POST', 
                data : {
                    email : email
                },
                success: function(data){
                    if(data){
                        $("#passwordForm").empty();
                        $("#passwordForm").append("<p>" + data.msg + "</p>");
                    }else{
                        $("#passwordForm").empty();
                        $("#passwordForm").append("<p>Une erreur est survenue lors de l'envoi du message.</p>");
                    }
                }
            });

        }
    });

}

function resendMail(){
    var email = window.userMail;

    console.log(email);

    $.ajax('../php/connect.php/resendMail', {
        method : 'POST', 
        data : {
            email : email
        },
        success: function(data){
            if(data){
                $("#notVerifiedContent").empty();
                $("#notVerifiedContent").append("<p>" + data.msg + "</p>");
            }else{
                $("#notVerifiedContent").empty();
                $("#notVerifiedContent").append("<p>Une erreur est survenue lors de l'envoi du message.</p>");
            }
        }
    });
}
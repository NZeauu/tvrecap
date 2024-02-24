
$(document).ready(function() {
    var url = window.location.href;
    var urlSplit = url.split("?");

    if (urlSplit.length < 2) {
        $('#unvalidlink').css('display', 'flex');
        return;
    }
    
    var token = urlSplit[1];
    var tokenSplit = token.split("=");
    var tokenValue = tokenSplit[1];

    console.log(tokenValue);

    // Check if token is valid
    $.ajax('../php/verifaccount.php/verifToken', {
        type: 'POST',
        data: {
            token: tokenValue
        }
    }).done(function(data) {
        if (!data) {
            $('#unvalidlink').css('display', 'flex');
        }
        else{

            // Verify user account
            $.ajax('../php/verifaccount.php/verifAccount', {
                type: 'POST',
                data: {
                    token: tokenValue
                }
            }).done(function(data) {
                
                if (data) {
                    $("#verif").css('display', 'flex');
                } else {
                    alert("Erreur lors de la vérification de votre compte");
                    window.location.href = "../html/login.html";
                }
            });
        }
    });
});
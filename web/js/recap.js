/************************************
 * Recap
 *************************************/
var Globals = $('body').data('vars');
var JsVars = jQuery('#js-vars').data('vars');

$(document).ready(function () {
// Checking the email at the end of the input process
    document.getElementById("email").addEventListener("blur", function (e) {
        // Corresponds to a string of the form xxx@yyy.zzz
        var regexEmail = /.+@.+\..+/;
        var validityEmail = "";
        if (!regexEmail.test(e.target.value)) {
            validityEmail = Globals.trans.validityEmail;
        }
        document.getElementById("helpEmail").textContent = validityEmail;
    });
    document.getElementById("verifyEmail").addEventListener("blur", function (e) {
        // Corresponds to a string of the form xxx@yyy.zzz
        var regexEmail = /.+@.+\..+/;
        var validityEmail = "";
        if (!regexEmail.test(e.target.value)) {
            validityEmail = Globals.trans.validityEmail;
        }
        document.getElementById("helpVerifyEmail").textContent = validityEmail;
    });
});

//Email registration

function displayPaymentButton() {
    var email = (String($('#email').val()).toLowerCase());
    var verifyEmail = (String($('#verifyEmail').val()).toLowerCase());

    //If we don't have an email or no verification email: error message and return
    if (email === '' || verifyEmail === '') {
        displayWarningToast(Globals.trans.fields);
        return;
    }
    //if the 2 emails entered are different: error message and return
    if (email !== verifyEmail) {
        displayWarningToast(Globals.trans.identical);
        return;
    }
    //If everything is ok: we hide the confirmation button and the email entry form
    $('#confirm').hide(200);
    $('.mail').hide(200);

    //We record the email
    $.post(Globals.routes.url_recap, {
        email: email
    });

    //The payment button is displayed
    $('#payment-button').removeClass('hidden', 200);
    //Message de confirmation
    displayWarningToast(Globals.trans.registered);
}
;
//When you click on the confirmation button, you call the displayPaymentButton function
$(document).ready(function () {
    $('#confirm').click(displayPaymentButton);
});

////inCartTicketsNum = {{ app.session.get('nbTickets')?:0}};
inCartTicketsNum = Globals.data.nbTickets;
// displays the number of items in the shopping cart 
$('#in-cart-tickets-num').html(inCartTicketsNum);






var Globals = $('body').data('vars');
var JsVars = jQuery('#js-vars').data('vars');


function displayWarningToast(message) {
    toastr.warning(message, '', {positionClass: "toast-top-center", timeOut: "10000"});
}

/////////Navbar/////////////


$(document).ready(function () {
    $('[data-toggle="popover"]').each(function (i, obj) {

        $(this).popover({
            html: true,
            content: function () {
                var id = $(this).attr('id');
                return $('#popover-content-' + id).html();
            }
        });

    });

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50)  /*height en pixels quand la navbar devient non-opaque*/
        {
            $('.opaque-navbar').addClass('opaque');
        } else {
            $('.opaque-navbar').removeClass('opaque');
        }
    });
    $(".hover").mouseleave(function () {
        $(this).removeClass("hover");
    });
});

///////Cart/////////

// When the user clicks on the button, open the modal 
$(function () {
    $("#myCart").on('click', function () {
        $('#myModal').show();
    });
});

// When the user clicks on <span> (x), close the modal
$(function () {
    $(".close").on('click', function () {
        $('#myModal').hide();
    });
});

// When the user clicks on the modal, close it
$(function () {
    $('#myModal').on('click', function () {
        $('#myModal').hide();
    });
});


//Display of ticket information
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});


/***************************************
 * SelectDate
 ****************************************/

$(document).ready(function () {
    var now = moment();
    var dateFormat = "Y-M-D";
    var time = moment().hour(14).minute(0).second(0);
    var closeTime = moment().hour(18).minute(0).second(0);

    /**
     * Returns true if it is less than 2pm
     * @param {string} visitDate
     * @returns {Boolean}         
     */
    function checkHour(visitDate) {
        if ((((moment(visitDate, dateFormat)).isSame(moment(), 'day')))
                && (moment().isAfter(time))) {
            displayWarningToast(Globals.trans.quatorze);
            return false;
        }
        return true;
    }
    ;

    /**
     * Returns true if it is less than 6pm
     * @param {string} visitDate
     * @returns {Boolean}         
     */
    function checkClosingHour(visitDate) {
        if ((((moment(visitDate, dateFormat)).isSame(moment(), 'day')))
                && (moment().isAfter(closeTime))) {
            displayWarningToast(Globals.trans.dixhuit);
            return false;
        }
        return true;
    }
    ;
    /**
     * Returns true if the day of visit chosen is not a Tuesday or Sunday
     * @param {date} visitDate
     * @returns {Boolean}         
     */
    function checkDisableDays(visitDate) {
        var day = moment(visitDate, dateFormat).day();
        if (day === 0) {
            displayWarningToast(Globals.trans.opening2);
            return false;
        } else if (day === 2) {
            displayWarningToast(Globals.trans.opening3);
            return false;
        }
        return true;
    }

    /**
     * Returns true if the chosen visit day is not in the table of days unavailable for order
     * @param {date} visitDate
     * @returns {Boolean}     
     */
    function checkArrayDatesDisabled(visitDate) {
        var arrayDatesDisabled = Globals.viewData.disabledDates;
        if (arrayDatesDisabled.includes(visitDate)) {
            displayWarningToast(Globals.trans.opening4);
            return false;
        }
        return true;
    }

    $('#daySelect').click(function () {
        var visitDate = $('#pick_a_date').val();
        //We check the date and time if visit today and time > 14h we can no longer order day tickets 
        if (!checkDisableDays(visitDate) || !checkHour(visitDate) || !checkClosingHour(visitDate) || !checkArrayDatesDisabled(visitDate)) {
            return;
        }
        submitData(true);
    });

    $('#halfDaySelect').click(function () {
        //we check the date and time. If visit today and time >18h, the museum is closed
        var visitDate = $('#pick_a_date').val();
        if (!checkDisableDays(visitDate) || !checkClosingHour(visitDate) || !checkArrayDatesDisabled(visitDate)) {
            return;
        }
        submitData(false);
    });


    function submitData(fullDay) {
        var visitDate = $('#pick_a_date').val();

        if (!$('#pick_a_date').val()) {
            displayWarningToast(Globals.trans.warning1);
        } else {
            $.post(Globals.routes.post_date, {
                visitDate: visitDate,
                fullDay: fullDay,
                event_id: Globals.viewData.eventId
            }, function (response) {
                if (response.success) {
                    document.location = Globals.routes.contact_details;
                } else {
                    if (response.message) {
                        displayWarningToast(response.message);
                    } else if (!$('#pick_a_date').val()) {
                        displayWarningToast(Globals.trans.warning2);
                    } else {
                        displayWarningToast(Globals.trans.warning3);
                    }
                }
                ;
            });
        }
        ;
    }
    ;
});



//////////Recap/////////////
//Messages info
$("#alert-target").click(function () {
    toastr["info"]("");
});

/***************************************
 * Contact Details
 ****************************************/

//Adding and deleting tickets//

$(document).ready(function () {
    //We hide the "Delete" icon
    $('.deleteTicket').hide();
    //When you click on "Add a ticket"
    $('.addTicket').click(function (e) {
        e.preventDefault();
        //Si tous les champs ne sont pas remplis : 
        if ($("#lastname").val() === "" || $("#firstname").val() === "" || $("#dateOfBirth").val() === ""
                || !$("#country").val() === "") {
            displayWarningToast(Globals.trans.fill);
            return false;
        }
        ;

        var form = $('#myForm');
        var ticketList = form.find('.ticket');

        //A cloned ticket
        var clonedTicket = form.find('.ticket:first').clone();
        //The data entered is deleted
        clonedTicket.find('.clientsDetails input', 'select').val('');
        //We target the ticket and delete the checked
        clonedTicket.find('.option-input').attr('checked', false);
        //We update the title
        clonedTicket.find('h3').text('Billet n°' + (ticketList.length + 1));
        //We add after the other tickets
        clonedTicket.insertAfter(form.find('.ticket:last')).hide().fadeIn('slow');
        //we add the deletion link
        $(".deleteTicket").fadeIn("fast");
    });
    //We can delete the last ticket
    $('.deleteTicket').click(function () {
        $(".ticket:last").remove();
        displayWarningToast(Globals.trans.deleteForm);
    });
    //If there is only 1 item, hide the delete button
    if ($('.ticket').length === 1) {
        $('.deleteTicket').hide();
    }
});


var dateFormat = "Y-M-D";

$(document).ready(function () {
    $('#myForm').on('click', function () {
        if ($('.option-input').is(':checked')) {
            $('.legend').removeClass("hidden").fadeIn('slow');
        } else {
            $('.legend').addClass("hidden").fadeOut('slow');
        }
    });
});

function checkDateOfBirth() {
    if ((moment($("#dateOfBirth").val()).isBefore('1905-01-01'))
            || (moment($("#dateOfBirth").val()).isAfter(moment()))) {
        return true;
    }
}

/**
 * @param {type} details
 * @returns {undefined}             
 */
function submitForm(details) {
    //Variables are determined
    var tickets = [];

    $('#myForm .ticket').each(function () {
        tickets.push({
            fareType: $(this).find('#fareType').prop('checked') ? 'reduct' : '',
            lastname: $(this).find('#lastname').val(),
            firstname: $(this).find('#firstname').val(),
            dateOfBirth: $(this).find('#dateOfBirth').val(),
            country: $(this).find('#country').val(),
            visitDate: Globals.data.visitDate
        });
    });

    $.post(Globals.routes.post_type, {
        tickets
    }, function (response) {
        if (response.success) {
            document.location = Globals.routes.recap;
        } else {
            //Display errors on tickets not validated by the controller
            if (response.errors) {
                response.errors.forEach(function (value) {
                    var index = value[0];
                    var message = value[1];
                    $('#myForm .ticket').eq(index).find('.errorMessage').text(message).show();
                });
            }
        }
    });
}

$(document).ready(function () {
    //If we click on the "Finalize your order" button
    $('#envoi').click(function () {
        var errorForm = false;
        //We check each ticket
        $('.ticket').each(function () {
            //If one of the fields is empty: error message
            if ($("#lastname").val() === ""
                    || $("#firstname").val() === "" || $("#dateOfBirth").val() === ""
                    || $("#country").val() === "") {
                displayWarningToast(Globals.trans.fields);
                errorForm = true;
            }
            if (checkDateOfBirth()) {
                displayWarningToast(Globals.trans.birth);
                preventDefault();
                errorForm = true;
            }
        });
        if (!errorForm) {
            submitForm(true);
        }
    });
});

/************************************
 * Recap
 *************************************/


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

inCartTicketsNum = Globals.data.nbTickets;
// displays the number of items in the shopping cart 
$('#in-cart-tickets-num').html(inCartTicketsNum);
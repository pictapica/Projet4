/***************************************
 * Contact Details
 ****************************************/

var Globals = $('body').data('vars');
var JsVars = jQuery('#js-vars').data('vars');

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
        };

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



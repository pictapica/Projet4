/***************************************
 * Contact Details
 ****************************************/

var Globals = $('body').data('vars');
var JsVars = jQuery('#js-vars').data('vars');




var dateFormat = "Y-M-D";



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



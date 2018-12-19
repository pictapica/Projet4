var globals = $('body').data('vars');
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
//
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

/////////SelectDate///////////
$(document).ready(function () {
    var globals = $('body').data('vars');
    var JsVars = jQuery('#js-vars').data('vars');

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
            displayWarningToast(globals.trans.quatorze);
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
            displayWarningToast(globals.trans.dixhuit);
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
            displayWarningToast(globals.trans.opening2);
            return false;
        } else if (day === 2) {
            displayWarningToast(globals.trans.opening3);
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
        var arrayDatesDisabled = globals.viewData.disabledDates;
        if (arrayDatesDisabled.includes(visitDate)) {
            displayWarningToast(globals.trans.opening4);
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
            displayWarningToast(globals.trans.warning1);
        } else {
            $.post(globals.routes.post_date, {
                visitDate: visitDate,
                fullDay: fullDay,
                event_id: globals.viewData.eventId
            }, function (response) {
                if (response.success) {
                    document.location = globals.routes.contact_details;
                } else {
                    if (response.message) {
                        displayWarningToast(response.message);
                    } else if (!$('#pick_a_date').val()) {
                        displayWarningToast(globals.trans.warning2);
                    } else {
                        displayWarningToast(globals.trans.warning3);
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
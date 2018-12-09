var toastr = "http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js";

function displayWarningToast(message) {
    toastr.warning(message, '', {positionClass: "toast-top-center", timeOut: "10000"});
}

/////////Navbar/////////////

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
});


//////////Recap/////////////
//Messages info
$("#alert-target").click(function () {
    toastr["info"]("");
});
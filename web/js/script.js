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




//////////Recap/////////////
//Messages info
$("#alert-target").click(function () {
    toastr["info"]("");
});




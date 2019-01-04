
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
        if ($(this).find("#lastname").val() === "" || $(this).find("#firstname").val() === "" || $(this).find("#dateOfBirth").val() === ""
                || $(this).find("#country").val() === "") {
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
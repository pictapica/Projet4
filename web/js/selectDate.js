/***************************************
 * SelectDate
 ****************************************/
var Globals = $('body').data('vars');
var JsVars = jQuery('#js-vars').data('vars');

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



    /**
     * Displaying the datepicker according to the locale
     * 
     */
    if (Globals.local.locale === 'fr') {
        $.fn.datepicker.dates['fr'] = {
            days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
            daysMin: ["D", "L", "M", "M", "J", "V", "S"],
            months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"],
            monthsShort: ["Janv.", "Févr.", "Mars", "Avril", "Mai", "Juin", "Juil.", "Août", "Sept.", "Oct.", "Nov.", "Dec."],
            titleFormat: "MM yyyy",
            weekStart: 1,
            today: "Aujourd'hui"
        };
    }
    ;

    if (Globals.local.locale === 'en') {
        $.fn.datepicker.dates['en'] = {
            days: ["Sunday", "Monday", "Tuesday", "Wenesday", "Thursday", "Friday", "Saturday"],
            daysShort: ["Sun", "Mon", "Tue", "Wen", "Thu", "Fri", "Sat"],
            daysMin: ["S", "M", "T", "W", "T", "F", "S"],
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthsShort: ["Jan", "Feb", "Mar", "Abr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"],
            titleFormat: "MM yyyy",
            weekStart: 7,
            today: "Today"
        };
    }
    ;

    /**
     * Datepicker info 
     * 
     */

    var arrayDatesDisabled = Globals.viewData.disabledDates;

    $(".form_datetime").datepicker({
        input: "date",
        format: "yyyy/mm/dd",
        language: Globals.local.locale,
        todayBtn: "linked",
        daysOfWeekDisabled: [0, 2],
        datesDisabled: arrayDatesDisabled,
        startDate: (Globals.viewData.startEvent, new Date()),
        endDate: (Globals.viewData.endEvent)
    });
});


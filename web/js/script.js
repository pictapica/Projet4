
// variables 
var url_post_date = "{{path('approjet4_booking_selectDate')}}";
var visitDate = $('#pick_a_date').val();
var day = $("#daySelect").val();
var halfDay = $("#halfDaySelect").val();

//functions 
function submitData(fullDay) {
    $.post(url_post_date, {
        date: visitDate,
        isFullDay: fullDay
    }, function (response) {
        if (response.success) {
            document.location = "<url_true>";
        } else {
            document.location = "<url_false>";
        }
    });
}

// le code exécuté 
$(document).ready(function () {
    $('#daySelect').click(function () {
        submitData(true);
    });

    $('#halfDaySelect').click(function () {
        submitData(false);
    });
});

function saveDate() {
    $.ajax({
        method: "GET",
        url: "{{ url('approjet4_booking_pick_a_date') }}",
        dataType: 'json',
        data: {date: visitDate, day: day, halfDay: halfDay}
    })
            .done(function (response) {
                if (response.day === day) {
                    window.location.href = "{{path('approjet4_booking_contactDetails')}}";
                } else {
                    window.location.href = "{{path('approjet4_booking_contactDetails')}}";
                }
            });
}



// find elements
var tickets = $("#tickets");
var button = $("button");

// handle click and add class
button.on("click", function () {
    var billet_html = "<div class='billet'>Un nouveau ticket</div>";
    tickets.append(billet_html);
});


$(function () {
    $('body').on('click', '.btn-checkbox', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var $checkbox = $(this).find(':input[type=checkbox]');
        if ($checkbox.length) {
            var $icon = $(this).find('[data-icon-on]');
            if ($checkbox.is(':checked')) {
                unchecked($checkbox);
            } else {
                checked($checkbox);
            }
            return;
        }
        var $radio = $(this).find(':input[type=radio]');
        if ($radio.length) {
            var $group = $radio.closest('.btn-group');
            $group.find(':input[type=radio]').not($radio).each(function () {
                unchecked($(this));
            })
            var $icon = $(this).find('[data-icon-on]');
            if ($radio.is(':checked')) {
                unchecked($radio);
            } else {
                checked($radio);
            }
            return;
        }
    });
});


function checked($input) {
    var $button = $input.closest('.btn');
    var $icon = $button.find('[data-icon-on]');
    $button.addClass('active');
    $input.prop('checked', true);
    $icon.css('width', $icon.width());
    $icon.removeAttr('class').addClass($icon.data('icon-on'));
    $input.trigger('change');
}

function unchecked($input) {
    var $button = $input.closest('.btn');
    var $icon = $button.find('[data-icon-on]');
    $button.removeClass('active');
    $input.prop('checked', false);
    $icon.css('width', $icon.width());
    $icon.removeAttr('class').addClass($icon.data('icon-off'));
    $input.trigger('change');
}

function initInput(elements) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

  var defaults = {
    triggerPrefix: 'js',
    statePrefix: 'jsstate',
    filledClassName: 'filled',
    nativeClassName: 'native',
    visitedClassName: 'visited'
  };

  var config = Object.assign({}, defaults, options);
  var filledClassName = config.statePrefix + '-' + config.filledClassName;
  var visitedClassName = config.statePrefix + '-' + config.visitedClassName;
  var nativeSelector = '.' + config.triggerPrefix + '-' + config.nativeClassName;

  function onBlur(e) {
    var match = Array.prototype.filter.call(e.path, function (node) {
      return Array.prototype.filter.call(elements, function (element) {
        return element === node;
      }).length > 0;
    })[0];

    if (!match) return;
    var native = match.querySelector(nativeSelector);

    if (!native) return;
    match.classList.toggle(filledClassName, native.value.length > 0);
    match.classList.add(visitedClassName);
  }

  document.addEventListener('blur', onBlur, true);
  document.addEventListener('change', onBlur, true);
}

initInput(document.querySelectorAll('.js-input'));

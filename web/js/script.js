/*$(document).ready(function submitData() {

    if ($("#pick_a_date").val() === "true") {
        saveDate();
    } else {
        alert("Vous devez choisir une date de visite");
    }


    function saveDate() {
        visitDate = $("#pick_a_date").val();
        day = $("#daySelect").val();
        halfDay = $("#halfDaySelect").val();
        $.ajax({
            method: "GET",
            url: "{{ url('approjet4_booking_pick_a_date') }}",
            dataType: 'json',
            data: {date: visitDate, day: day, halfDay}
        })
                .done(function (response) {
                    if(day === day){
                      window.location.href = "{{path('approjet4_booking_contactDetails')}}";  
                    }else{
                      window.location.href = "{{path('approjet4_booking_contactDetails')}}";   
                    }
                });
    }
});
*/

function initInput(elements, options = {}) {
  var defaults = {
    triggerPrefix: 'js',
    statePrefix: 'jsstate',
    filledClassName: 'filled',
    nativeClassName: 'native',
    visitedClassName: 'visited',
  };
  
  var config = Object.assign({}, defaults, options);
  var filledClassName = `${config.statePrefix}-${config.filledClassName}`;
  var visitedClassName = `${config.statePrefix}-${config.visitedClassName}`;
  var nativeSelector = `.${config.triggerPrefix}-${config.nativeClassName}`;
  
  function onBlur(e) {
  	var match = Array.prototype.filter.call(e.path, function(node){
			return Array.prototype.filter.call(elements, function(element){
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




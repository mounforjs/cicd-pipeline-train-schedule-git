$(document).ready(function() {
    $('.date').each(function(index) {
		var elem = this;

		var date = $(this).data("date");
		localDate = (date != "" && date != "0000-00-00 00:00:00" && date != null) ? new Date(date + "Z") : new Date();
		var datetimepicker = flatpickr(this, {
			enableTime: true,
			altInput: true,
			dateFormat: "Y-m-d H:i",
			minDate: "today",
			defaultDate: localDate,
			minuteIncrement: 1,
			disableMobile: "true"
		});
    })
});

var CustomFormsPickers = {


	dateRangePicker: function () {
		var today = (new Date()).toISOString();
		var dateSplitted = today.split(/-|T/);
		var todayFormatted = dateSplitted[2] + "/" + dateSplitted[1] + "/" + dateSplitted[0];
		console.log("today: " + today + " formatted: " + todayFormatted);
		$('.bootstrap-daterangepicker-basic').daterangepicker({
			singleDatePicker: true,
			format: 'DD/MM/YYYY',
			minDate: todayFormatted
			}, function(start, end, label) {
				console.log(start.toISOString(), end.toISOString(), label);
			}
		);
	},

	dateRangePickerRange: function () {
		$('.bootstrap-daterangepicker-basic-range').daterangepicker(null, function(start, end, label) {
			console.log(start.toISOString(), end.toISOString(), label);
		});
	},

	dateRangePickerTime: function () {
		$('.bootstrap-daterangepicker-date-time').daterangepicker({
			timePicker: true,
			timePickerIncrement: 30,
			format: 'MM/DD/YYYY h:mm A'
			}, function(start, end, label) {
				console.log(start.toISOString(), end.toISOString(), label);
		});
	},

	dateRangePickerBootstrap: function () {
		$('.bootstrap-daterangepicker-dropdown span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
		$('.bootstrap-daterangepicker-dropdown').daterangepicker();
	},

	dateRangePickerSpecific: function () {
		$('.bootstrap-daterangepicker-specific').daterangepicker({
			startDate: moment().subtract(29, 'days'),
			endDate: moment(),
			opens: 'left',
			showDropdowns: true,
			showWeekNumbers: true,
			timePicker: false,
			timePickerIncrement: 1,
			timePicker12Hour: true,
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		},
		function (start, end) {
				$('.bootstrap-daterangepicker-specific span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}
		);
	},


	init: function () {

		this.dateRangePicker();
		this.dateRangePickerRange();
		this.dateRangePickerTime();
		this.dateRangePickerBootstrap();
		this.dateRangePickerSpecific();
	}
};


var FormsIcheck = {

	minimal: function () {
		$('.icheck-minimal').iCheck({
			checkboxClass: 'icheckbox_minimal',
			radioClass: 'iradio_minimal'
		});
		$('.icheck-minimal-red').iCheck({
			checkboxClass: 'icheckbox_minimal-red',
			radioClass: 'iradio_minimal-red'
		});
		$('.icheck-minimal-green').iCheck({
			checkboxClass: 'icheckbox_minimal-green',
			radioClass: 'iradio_minimal-green'
		});
		$('.icheck-minimal-blue').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue'
		});
		$('.icheck-minimal-aero').iCheck({
			checkboxClass: 'icheckbox_minimal-aero',
			radioClass: 'iradio_minimal-aero'
		});
		$('.icheck-minimal-grey').iCheck({
			checkboxClass: 'icheckbox_minimal-grey',
			radioClass: 'iradio_minimal-grey'
		});
		$('.icheck-minimal-orange').iCheck({
			checkboxClass: 'icheckbox_minimal-orange',
			radioClass: 'iradio_minimal-orange'
		});
		$('.icheck-minimal-yellow').iCheck({
			checkboxClass: 'icheckbox_minimal-yellow',
			radioClass: 'iradio_minimal-yellow'
		});
		$('.icheck-minimal-pink').iCheck({
			checkboxClass: 'icheckbox_minimal-pink',
			radioClass: 'iradio_minimal-pink'
		});
		$('.icheck-minimal-purple').iCheck({
			checkboxClass: 'icheckbox_minimal-purple',
			radioClass: 'iradio_minimal-purple'
		});
	},

	square: function () {
		$('.icheck-square').iCheck({
			checkboxClass: 'icheckbox_square',
			radioClass: 'iradio_square'
		});
		$('.icheck-square-red').iCheck({
			checkboxClass: 'icheckbox_square-red',
			radioClass: 'iradio_square-red'
		});
		$('.icheck-square-green').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green'
		});
		$('.icheck-square-blue').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue'
		});
		$('.icheck-square-aero').iCheck({
			checkboxClass: 'icheckbox_square-aero',
			radioClass: 'iradio_square-aero'
		});
		$('.icheck-square-grey').iCheck({
			checkboxClass: 'icheckbox_square-grey',
			radioClass: 'iradio_square-grey'
		});
		$('.icheck-square-orange').iCheck({
			checkboxClass: 'icheckbox_square-orange',
			radioClass: 'iradio_square-orange'
		});
		$('.icheck-square-yellow').iCheck({
			checkboxClass: 'icheckbox_square-yellow',
			radioClass: 'iradio_square-yellow'
		});
		$('.icheck-square-pink').iCheck({
			checkboxClass: 'icheckbox_square-pink',
			radioClass: 'iradio_square-pink'
		});
		$('.icheck-square-purple').iCheck({
			checkboxClass: 'icheckbox_square-purple',
			radioClass: 'iradio_square-purple'
		});
	},

	flat: function () {
		$('.icheck-flat').iCheck({
			checkboxClass: 'icheckbox_flat',
			radioClass: 'iradio_flat'
		});
		$('.icheck-flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-red',
			radioClass: 'iradio_flat-red'
		});
		$('.icheck-flat-green').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});
		$('.icheck-flat-blue').iCheck({
			checkboxClass: 'icheckbox_flat-blue',
			radioClass: 'iradio_flat-blue'
		});
		$('.icheck-flat-aero').iCheck({
			checkboxClass: 'icheckbox_flat-aero',
			radioClass: 'iradio_flat-aero'
		});
		$('.icheck-flat-grey').iCheck({
			checkboxClass: 'icheckbox_flat-grey',
			radioClass: 'iradio_flat-grey'
		});
		$('.icheck-flat-orange').iCheck({
			checkboxClass: 'icheckbox_flat-orange',
			radioClass: 'iradio_flat-orange'
		});
		$('.icheck-flat-yellow').iCheck({
			checkboxClass: 'icheckbox_flat-yellow',
			radioClass: 'iradio_flat-yellow'
		});
		$('.icheck-flat-pink').iCheck({
			checkboxClass: 'icheckbox_flat-pink',
			radioClass: 'iradio_flat-pink'
		});
		$('.icheck-flat-purple').iCheck({
			checkboxClass: 'icheckbox_flat-purple',
			radioClass: 'iradio_flat-purple'
		});
	},

	line: function () {
		$('.icheck-line input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line',
				radioClass: 'iradio_line',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-red input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-red',
				radioClass: 'iradio_line-red',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-green input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-green',
				radioClass: 'iradio_line-green',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-blue input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-blue',
				radioClass: 'iradio_line-blue',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-aero input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-aero',
				radioClass: 'iradio_line-aero',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-grey input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-grey',
				radioClass: 'iradio_line-grey',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-orange input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-orange',
				radioClass: 'iradio_line-orange',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-yellow input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-yellow',
				radioClass: 'iradio_line-yellow',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-pink input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-pink',
				radioClass: 'iradio_line-pink',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
		$('.icheck-line-purple input').each(function(){
			var self = $(this),
			label = self.prev(),
			label_text = label.text();

			label.remove();
			self.iCheck({
				checkboxClass: 'icheckbox_line-purple',
				radioClass: 'iradio_line-purple',
				insert: '<div class="icheck_line-icon"></div>' + label_text
			});
		});
	},

	polaris: function () {
		$('.icheck-polaris').iCheck({
			checkboxClass: 'icheckbox_polaris',
			radioClass: 'iradio_polaris',
			increaseArea: '-10%'
		});
	},

	futurico: function () {
		$('.icheck-futurico').iCheck({
			checkboxClass: 'icheckbox_futurico',
			radioClass: 'iradio_futurico',
			increaseArea: '-10%'
		});
	},

	init: function () {
		this.minimal();
		this.square();
		this.flat();
		this.line();
		this.polaris();
		this.futurico();
	}
}


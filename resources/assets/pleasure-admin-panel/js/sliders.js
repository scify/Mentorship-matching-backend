// https://gist.github.com/zlove/eb95e9d51af5b55eb435
;(function ($) {
	/**
	 * Return an object consisting of properties of 'data'
	 * that match the regex 'pattern'. If trim is not false,
	 * trim the matched 'pattern' from the property name,
	 * and make the first char of the remaining property
	 * name lowercase.
	 */
	function filterData(data, pattern, trim) {
			var data,
					prop, new_prop,
					matched_data = {};

			for (prop in data) {
					if (data.hasOwnProperty(prop) && prop.match(pattern) ) {
							if (trim) {
									new_prop = prop.replace(pattern, '');
									if ( ! new_prop.length ) {
											continue;
									}
									new_prop = new_prop.charAt(0).toLowerCase() + new_prop.slice(1);
									matched_data[new_prop] = data[prop];
							} else {
									matched_data[prop] = data[prop];
							}
					}
			}

			return matched_data;
	}

	/**
	 * Example Usage:
	 *
	 * js:
	 * $('.slider-element').bxSliderAlt({ slideWidth: 200 })
	 *
	 * html:
	 * <div class="slider-element" data-bx-slider-pager="false" data-bx-slider-slide-width="300">
	 * ...
	 * </div>
	 */
	$.fn.bxSliderAlt = function (options) {

			if(this.length == 0) return this;

			if (typeof(options) == 'undefined') {
				options = {};
			}

			this.each( function() {
					var data_options = filterData($(this).data(), /bxSlider/, true);
					$.extend(options, data_options);
					$(this).bxSlider(options)}
			);
	}
})(jQuery);

$(document).ready(function () {
	$('.bxslider').bxSliderAlt({
		//pager: false
	});
});

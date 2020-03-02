/**
 * Script for settings screen
 */
(function ($) {

	/**
	 * Validation
	 */
	$('form *[data-needs-validation]').on('input', function (ev) {
		var $this = $(this);
		var form = $this.closest('form');
		var submit = form.find('*[type=submit]');
		submit.attr('disabled', true);

		$.ajax(ajaxurl, {
			method: 'post',
			dataType: 'json',
			data: {
				action: 'wpmc_validate_option',
				name: $this.attr('name'),
				value: $this.val()
			}

		}).always(function () {
			submit.attr('disabled', false);

		}).done(function (response) {
			if (response.success) {
				$this[0].setCustomValidity('');

			} else { // Invalid Data
				$this[0].setCustomValidity(response.data.message);
			}
		});
	});

	/**
	 * Scanning Method
	 */
	$('select#wpmc_method').on('change', function (ev) {
		var selected = $(this).val();
		if (selected == 'media') { // Method = "Media Library"
			$('input#wpmc_media_library').attr('disabled', true);
		} else { // Method = Other Else
			$('input#wpmc_media_library').attr('disabled', false);
		}
	});

})(jQuery);

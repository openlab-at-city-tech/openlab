(function($){
	var $clicked, $statuses;

	$(document).ready(function() {
		$statuses = $('.group-manage-members-bpges-status input[type="radio"]');
		$statuses.on('change', function() {
			$clicked = $(this);

			disable_radio_buttons();

			// So this is living: Using the non-AJAX URL to make an AJAX request.
			$.ajax({
				method: 'GET',
				url: $(this).data('url'),
				success: function() {
					enable_radio_buttons();
				}
			});
		} );
	});

	disable_radio_buttons = function() {
		$statuses.attr('disabled', 'disabled');
	};

	enable_radio_buttons = function() {
		$statuses.removeAttr('disabled');
	};
}(jQuery));

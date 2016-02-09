(function($){
	var $clicked, $statuses;

	$(document).ready(function() {
		$statuses = $('.group-manage-members-bpges-status input[type="radio"]');
		$statuses.on('change', function() {
			$clicked = $(this);

                        go_status('User email status updating...', 'error');
			disable_radio_buttons();

			// So this is living: Using the non-AJAX URL to make an AJAX request.
			$.ajax({
				method: 'GET',
				url: $(this).data('url'),
				success: function() {
					enable_radio_buttons();
                                        go_status('User email status changed successfully', 'updated');
				},
                                error: function(){
                                    enable_radio_buttons();
                                    go_status('Error updating the status. Please try again or contact us for help', 'error');
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
        go_status = function(status, type){
            
            //clean up first
            $('#group-create-body .bp-template-notice').remove();
            
            //construct
            var statusWrapper = '<div class="bp-template-notice '+ type +' id="message"><p>' + status + '</p></div>';
            
            //add
            $('#group-create-body').prepend(statusWrapper);
            
            if(type === 'updated'){
                $('#group-create-body .bp-template-notice').delay(3000).slideUp("slow");
            }
            
        }
}(jQuery));

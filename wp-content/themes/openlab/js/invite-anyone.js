(function($){
	var $acknowledgeCheckbox,
	  $importMembersAddresses,
	  $importMembersSubmit;

	var updateForm = function() {
		var isAcknowleged = $acknowledgeCheckbox.is(':checked');
		var hasAddresses  = $importMembersAddresses.val().length > 0;

		if ( isAcknowleged && hasAddresses ) {
			$importMembersSubmit.prop('disabled', false);
		} else {
			$importMembersSubmit.prop('disabled', 'disabled');
		}
	}

	$(document).ready(function(){
		$acknowledgeCheckbox = $('#import-acknowledge-checkbox');
		$importMembersAddresses = $('#email-addresses-to-import');
		$importMembersSubmit = $('#import-members-form input[type="submit"]');

		if ( $importMembersAddresses.length === 0 ) {
			return;
		}

		updateForm();

		$acknowledgeCheckbox.on('change', updateForm);
		$importMembersAddresses.on('change keyup', updateForm);
	});
}(jQuery));

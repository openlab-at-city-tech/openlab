(function($){
	var $acknowledgeCheckbox,
	  $importMembersAddresses,
	  $importMembersSubmit;

	var updateForm = function() {
		var isAcknowleged = $acknowledgeCheckbox.is(':checked');
		var hasAddresses  = $importMembersAddresses.val().length > 0;

		if ( isAcknowleged && hasAddresses ) {
			$importMembersSubmit.removeAttr('disabled');
		} else {
			$importMembersSubmit.attr('disabled', 'disabled');
		}
	}

	$(document).ready(function(){
		$acknowledgeCheckbox = $('#import-acknowledge-checkbox');
		$importMembersAddresses = $('#email-addresses-to-import');
		$importMembersSubmit = $('#import-members-form input[type="submit"]');

		updateForm();

		$acknowledgeCheckbox.on('change', updateForm);
		$importMembersAddresses.on('change keyup', updateForm);
	});
}(jQuery));

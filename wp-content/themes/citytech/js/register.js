jQuery(document).ready(function($){
        $('#signup_email').on('blur',function(e){
                var email = $(e.target).val().toLowerCase();
                var emailtype = '';

                if ( 0 <= email.indexOf( 'mail.citytech.cuny.edu' ) ) {
                        emailtype = 'student';
                } else if ( 0 <= email.indexOf( 'citytech.cuny.edu' ) ) {
                        emailtype = 'fs';
                }

                var typedrop = $('#field_7');
                var email_error = '';
                var email_error_message = '';

                var newtypes = '';

                if ( 'student' == emailtype ) {
                        newtypes += '<option value="Student">Student</option>';
                        newtypes += '<option value="Alumni">Alumni</option>';
                }

                if ( 'fs' == emailtype ) {
                        newtypes += '<option value="">----</option>';
                        newtypes += '<option value="Faculty">Faculty</option>';
                        newtypes += '<option value="Staff">Staff</option>';
                }

                if ( '' == emailtype ) {
                        newtypes += '<option value="">----</option>';
                }

                $(typedrop).html(newtypes);
		
		/* Because there is no alternative in the dropdown, the 'change' event never
		 * fires. So we trigger it manually
		 */
		if ( 'student' == emailtype ) {
			wds_load_account_type('field_7','');
		}
        });

	var $account_type_field = $( '#field_' + OLReg.account_type_field );

	// Ensure that the account type field is set properly from the post
	$account_type_field.val( OLReg.post_data.field_7 );
	$account_type_field.children( 'option' ).each( function() {
		if ( OLReg.post_data.field_7 == $( this ).val() ) {
			$( this ).attr( 'selected', 'selected' );
		}
	} );


	$account_type_field.on( 'change', function() {
		load_account_type_fields();
	} );
	load_account_type_fields();

	//load register account type
	function load_account_type_fields() {
		var default_type = '';
		var selected_account_type = $account_type_field.val();

		if ( selected_account_type != "" ) {
			document.getElementById( 'signup_submit' ).style.display='';
		} else {
			document.getElementById( 'signup_submit' ).style.display='none';
		}

		$.ajax( ajaxurl, {
			data: {
				action: 'wds_load_account_type',
				account_type: selected_account_type,
				post_data: OLReg.post_data
			},
			method: 'POST',
			success: function( response ) {
				$( '#wds-account-type' ).html( response );
			}
		} );
	}
},(jQuery));

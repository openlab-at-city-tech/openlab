jQuery(document).ready(function($){
        $('#signup_email').on('blur',function(e){
                var email = $(e.target).val();
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
},(jQuery));

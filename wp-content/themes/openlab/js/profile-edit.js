jQuery(document).ready(function($) {
    $editFrom = $('#profile-edit-form');
    $submitFrom = $('#profile-group-edit-submit');

		$displayNameField = $( '#field_1' );

		const textContainsLastName = (text) => {
			const { userLastName, userEmail } = olProfileEdit;

			if ( text.toLowerCase().includes( userLastName ) ) {
				return true;
			}

			const userLastNameEmailMatch = text.match( /^[^\.@]+\.([a-zA-Z]+)[0-9]*@mail\.citytech\.cuny\.edu$/ );
			const userLastNameEmail = userLastNameEmailMatch ? userLastNameEmailMatch[1] : null;

			if ( userLastNameEmail && userLastNameEmail.toLowerCase() === userLastName ) {
				return true;
			}

			return false;
		}

		const checkDisplayNameForLastName = () => {
			const containsLastName = textContainsLastName( $displayNameField.val() );
			toggleDisplayNameContainsLastNameError( containsLastName );
		}

		const toggleDisplayNameContainsLastNameError = ( show ) => {
			$( '.display-name-contains-last-name-error' ).remove();

			if ( show ) {
				$displayNameField.after( '<div class="display-name-contains-last-name-error field-contains-last-name-error error">It looks like you’re using your last name in your username. Are you sure?</div>' );
			}
		}

		checkDisplayNameForLastName();
		$displayNameField.on( 'change', checkDisplayNameForLastName );

    $editFrom.parsley({
        trigger: 'change'
    }).on('field:error', function() {
        $editFrom.find('.error-container').addClass('error');

        $submitFrom.addClass('btn-disabled')
            .val('Please Complete Required Fields');
    }).on('field:success', function() {
        if ( ! this.parent.isValid() ) {
            return false;
        }

        $editFrom.find('.error-container').removeClass('error');

        $submitFrom.removeClass('btn-disabled')
            .val('Save Changes');
    });
});

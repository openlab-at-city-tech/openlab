( function( window, $ ) {
    // Parsley validation rules.
    window.Parsley.addValidator('lowercase', {
        validateString: function (value) {
            return value === value.toLowerCase();
        },
        messages: {
            en: 'This field supports lowercase letters only.'
        }
    });

    window.Parsley.addValidator('nospecialchars', {
        validateString: function (value) {
            return !value.match(/[^a-zA-Z0-9]/);
        },
        messages: {
            en: 'This field supports alphanumeric characters only.'
        }
    });

    window.Parsley.addValidator('alphanumericAndDashes', {
        validateString: function (value) {
            return !value.match(/[^a-zA-Z0-9\-]/);
        },
        messages: {
            en: 'This field supports alphanumeric characters and hyphens only.'
        }
    });

    // Revalidate confirm fields when primary fields change.
    // This ensures that when email/password is updated after the confirm field,
    // the confirm field's equalto validation is re-checked.
    $( document ).ready( function() {
        $( '#signup_email' ).on( 'input blur', function() {
            var $confirm = $( '#signup_email_confirm' );
            if ( $confirm.val().length > 0 ) {
                $confirm.parsley().validate();
            }
        } );

        $( '#signup_password' ).on( 'input blur', function() {
            var $confirm = $( '#signup_password_confirm' );
            if ( $confirm.val().length > 0 ) {
                $confirm.parsley().validate();
            }
        } );
    } );

		window.Parsley.addValidator( 'passwordStrength', {
			validateString: function( value ) {
				var passwordBlacklist = window.passwordBlacklist || [];
				var strength = wp.passwordStrength.meter( value, passwordBlacklist, '' );

				var minimumStrength = 3;

				return strength >= minimumStrength;
			},
			messages: {
				en: 'Your password is too weak. To complete sign up you must choose a stronger password.'
			}
		} );

		window.Parsley.addAsyncValidator(
			'newSiteValidate',
			function( xhr ) {
				var siteIsRequiredForGroupTypeEl = document.getElementById( 'site-is-required-for-group-type' )
				var siteIsRequiredForGroupType = siteIsRequiredForGroupTypeEl && '1' === siteIsRequiredForGroupTypeEl.value

				if ( ! siteIsRequiredForGroupType && ! $( '#wds_website_check' ).is( ':checked' ) ) {
					return true;
				}

				// Ignore validation on an unselected field.
				var siteType = $( 'input[name="new_or_old"]:checked' ).val();
				if ( 'new' === siteType && 'clone-destination-path' === this.$element.attr( 'id' ) ) {
					return true;
				} else if ( 'clone' === siteType && 'new-site-domain' === this.$element.attr( 'id' ) ) {
					return true;
				}

				if ( 'new' !== siteType && 'clone' !== siteType ) {
					return true;
				}

				return xhr.responseJSON.success;
			},
			'/wp-admin/admin-ajax.php?action=openlab_validate_groupblog_url_handler'
		);

}( window, jQuery ) );

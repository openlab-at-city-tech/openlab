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

    var iffRecursion = false;
    window.Parsley.addValidator('iff', {
        validateString: function (value, requirement, instance) {
            var $partner = $(requirement);
            var isValid = $partner.val() == value;

            if (iffRecursion) {
                iffRecursion = false;
            } else {
                iffRecursion = true;
                $partner.parsley().validate();
            }

            return isValid;
        }
    });

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

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

		window.Parsley.addValidator('studentemail', {
			validateString: function (value) {
				return /^([a-zA-Z\-\._]+)*[0-9]{1,4}@stu-mail\.citytech\.cuny\.edu$/.test( value );
			},
			messages: {
				en: 'Please enter a valid City Tech student email address. Example: first.lastname##@stu-mail.citytech.cuny.edu'
			}
		});

		window.Parsley.addValidator('facultystaffemail', {
			validateString: function (value) {
				return /^([a-zA-Z0-9\-\._]+)@citytech\.cuny\.edu$/.test( value );
			},
			messages: {
				en: 'Please enter a valid City Tech faculty/staff email address.'
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

	( function() {
		var cache = {};
		var inFlightXhr = null;
		var inFlightValue = null;
		var inFlightDeferreds = [];

		var duplicateMessages = {
			exact:  'This email address is already registered. Log in with this address or choose another one.',
			handle: 'You may already have an account on the OpenLab. Please <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us/">contact us</a> for assistance.'
		};

		function setDuplicateMessage( matchType ) {
			if ( duplicateMessages[ matchType ] ) {
				window.Parsley.addMessage( 'en', 'mailEmailDuplicateCheck', duplicateMessages[ matchType ] );
			}
		}

		window.Parsley.addValidator( 'mailEmailDuplicateCheck', {
			validateString: function( value ) {
				var url = ( 'undefined' !== typeof OLReg && OLReg.ajaxurl ) ? OLReg.ajaxurl : ajaxurl;

				// Cached result: return synchronously so no new deferred is created.
				if ( cache.hasOwnProperty( value ) ) {
					if ( cache[ value ] !== 'none' ) {
						setDuplicateMessage( cache[ value ] );
					}
					return cache[ value ] === 'none';
				}

				// Same value already in flight: queue a new deferred resolved alongside the existing request.
				if ( inFlightValue === value ) {
					var d = $.Deferred();
					inFlightDeferreds.push( d );
					return d.promise();
				}

				// New value: abort any previous request and resolve its abandoned deferreds harmlessly.
				if ( inFlightXhr ) {
					inFlightXhr.abort();
					inFlightDeferreds.forEach( function( d ) { d.resolve(); } );
					inFlightDeferreds = [];
				}

				var deferred = $.Deferred();
				inFlightValue = value;
				inFlightDeferreds = [ deferred ];

				inFlightXhr = $.get( url, {
					action: 'openlab_duplicate_email_check',
					email: value
				} ).done( function( response ) {
					inFlightXhr = null;
					inFlightValue = null;
					var matchType = ( response && response.matchType ) ? response.matchType : 'none';
					cache[ value ] = matchType;
					if ( matchType !== 'none' ) {
						setDuplicateMessage( matchType );
					}
					inFlightDeferreds.forEach( function( d ) {
						matchType === 'none' ? d.resolve() : d.reject();
					} );
					inFlightDeferreds = [];
				} ).fail( function( xhr, status ) {
					inFlightXhr = null;
					inFlightValue = null;
					if ( 'abort' !== status ) {
						// On unexpected failure, don't block the user.
						cache[ value ] = 'none';
						inFlightDeferreds.forEach( function( d ) { d.resolve(); } );
					}
					inFlightDeferreds = [];
				} );

				return deferred.promise();
			},
			messages: {
				en: duplicateMessages.handle
			}
		} );

		// Guard against duplicate error elements caused by concurrent Parsley validation contexts
		// each independently calling addError when multiple whenValid() calls resolve simultaneously.
		window.Parsley.on( 'field:error', function() {
			var $dupes = this._ui.$errorsWrapper.find( '.parsley-mailEmailDuplicateCheck' );
			if ( $dupes.length > 1 ) {
				$dupes.not( ':first' ).remove();
			}
		} );
	} )();

}( window, jQuery ) );

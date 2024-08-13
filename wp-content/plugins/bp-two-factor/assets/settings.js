jQuery(function($){
	var $securityKeys = $( '#security-keys-section' );
	var $u2fCheckbox = $( '#two-factor-options input[value="Two_Factor_FIDO_U2F"]' );
	var $checkboxes = $( '.two-factor-methods-table input[type="checkbox"]' );
	var $backupCodes = $( '#two-factor-backup-codes' );
	var $totp = $( '#two-factor-totp-options' );
	var $webAuthnCheckbox = $( '#two-factor-options input[value="TwoFactor_Provider_WebAuthn"]' );
	var $securityKeysWebAuthn = $('#webauthn-security-keys-section');

	// Only show Security Keys section if checked.
	if ( $u2fCheckbox.prop( 'checked' ) ) {
		$securityKeys.show();
	}
	$u2fCheckbox.on( 'change', function() {
		if ( $(this).prop( 'checked' ) ) {
			$securityKeys.show();
		} else {
			$securityKeys.hide();
		}
	} );

	// Only show WebAuthn Security Keys section if checked.
	if ( $webAuthnCheckbox.prop( 'checked' ) ) {
		$securityKeysWebAuthn.show();
	}
	$webAuthnCheckbox.on( 'change', function() {
		if ( $(this).prop( 'checked' ) ) {
			$securityKeysWebAuthn.show();
		} else {
			$securityKeysWebAuthn.hide();
		}
	} );

	// Eek. Inject strings and other stuff.
	$securityKeys.find( '.register-security-key' ).prepend( bp2fa.security_key_desc );
	$securityKeysWebAuthn.find( '.add-webauthn-key' ).prepend( bp2fa.security_key_webauthn_desc );
	$backupCodes.wrap( '<div id="two-factor-backup-codes-container"></div>' );
	$backupCodes.attr( 'data-count', bp2fa.backup_codes_count );
	if ( $backupCodes.data( 'count' ) > 0 ) {
		$backupCodes.parent().prepend( bp2fa.backup_codes_misplaced );
		$backupCodes.find('span').wrap('<p id="previous-codes"></p>' )
		$backupCodes.parent().prepend( $( '#previous-codes' ) );
	} else {
		$backupCodes.parent().prepend( bp2fa.backup_codes_generate );
	}
	$backupCodes.parent().prepend( bp2fa.recovery_codes_desc );

	// Customizations for TOTP provider.
	function totp_toggler() {
		if ( $totp.find( 'a.button' ).length ) {
			$totp.addClass( 'configured' ).removeClass( 'not-configured' );
		} else {
			$totp.addClass( 'not-configured' ).removeClass( 'configured' );

			if ( ! $totp.find( 'p strong' ).length ) {
				$totp.find( 'code' ).before( bp2fa.totp_key );
			}
		}
	}

	totp_toggler();

	/*
	 * Select backup codes as a provider, only if a 2FA provider is enabled
	 * during backup code viewing.
	 */
	$( 'button.button-two-factor-backup-codes-generate' ).on( 'click', function() {
		if ( $checkboxes.filter( ':checked' ).length ) {
			$( 'table.two-factor-methods-table input[type="checkbox"][value="Two_Factor_Backup_Codes"]' ).prop( 'checked', true );
		}
	} );

	$checkboxes.on( 'change', function() {
		var radio = $( 'input[name="_two_factor_provider"]' ),
			radioVal = radio.filter( ':checked' ).val(),
			checkPrimary = false;

		// Enabled a provider.
		if ( $(this).prop( 'checked' ) ) {
			// Primary is Backup codes and Enabled is anything but Backup Codes.
			if ( 'Two_Factor_Backup_Codes' !== $(this).val() && 'Two_Factor_Backup_Codes' === radioVal ) {
				checkPrimary = true;

			// No primary and Enabled is anything but Backup Codes.
			} else if ( ! radioVal &&  'Two_Factor_Backup_Codes' !== $(this).val() ) {
				checkPrimary = true;
			}

			// Check corresponding Primary if allowed.
			if ( checkPrimary ) {
				$(this).parent().next().find('input').prop( 'checked', true );
			}

		// Disabled a provider.
		} else {
			// Deselect Primary provider for unchecked provider.
			if ( radioVal === $(this).val() ) {
				$( 'table.two-factor-methods-table input[type="radio"][value="' + $(this).val() + '"]' ).prop( 'checked', false );
			}

			checked = $checkboxes.filter( ':checked' );

			// Set a fallback primary provider that isn't Backup Codes.
			if ( checked.length && 'Two_Factor_Backup_Codes' !== checked.first().val() ) {
				$( 'table.two-factor-methods-table input[type="radio"][value="' + checked.first().val() + '"]' ).prop( 'checked', true );
			}
		}
	} );

	// Add CSS class when clicking on "Generate New Recovery Codes" button.
	$( '.button-two-factor-backup-codes-generate' ).click( function() {
		$(this).addClass( 'code-loading' );
	});


	// Remove 'code-loading' class after backup count is updated via AJAX.
	var mut = new MutationObserver(function(mutations){
	    mutations.forEach(function(mutationRecord) {
			document.querySelector('.button-two-factor-backup-codes-generate').classList.remove( 'code-loading' );
	    });
	});
	mut.observe(document.querySelector(".two-factor-backup-codes-count"),{
	  'childList': true
	});

	// AJAX mods.
	$( document ).on( "ajaxComplete", function( event, xhr, settings ) {
		if ( -1 === settings.url.indexOf( '/wp-json/two-factor' ) ) {
			return;
		}

		// TOTP.
		if ( -1 !== settings.url.indexOf( '/totp' ) ) {
			var checkbox = $('#enabled-Two_Factor_Totp'),
				checked = true;

			// Invalid TOTP auth code.
			if ( 400 === xhr.status ) {
				checked = false;
				setTimeout( () => {
					$( '#totp-setup-error' ).prop( 'id', 'message' ).addClass( 'totp-setup-error' ).delay(5000).fadeOut();
				}, 250 );
			}

			// Reset TOTP key.
			if ( xhr.responseJSON.success && settings.headers.hasOwnProperty( 'X-HTTP-Method-Override' ) && 'DELETE' === settings.headers['X-HTTP-Method-Override'] ) {
				checked = false;
			}

			checkbox.prop( 'checked', checked ).trigger( 'change' );

			setTimeout( () => {
				totp_toggler();
				if ( checked && ! $totp.find( '#totp-changed' ).length ) {
					$totp.append( '<input id="totp-changed" type="hidden" name="totp-changed" value="1" />' );
				}
			}, 250 );
		}
	} );
})
window.GFDropboxSettings = null;

( function( $ ) {

	GFDropboxSettings = function () {

		var self = this;
		this.legacyUI = ! $( '#customAppKey' ).parent().hasClass( 'gform-settings-input__container' );

		this.init = function() {

			this.initialValues = {
				appKey:    this.getAppKey(),
				appSecret: this.getAppSecret()
			};

			this.pageURL = gform_dropbox_pluginsettings_strings.settings_url;

			this.bindAppKeyUpdate();

			this.bindDeauthorize();

			this.bindDisableCustomApp();

			this.bindEnableCustomApp();

			this.setupValidationIcons();

			this.checkAppKeyValidity();

			// Hide save plugin settings button.
			$( '#tab_gravityformsdropbox #gform-settings-save' ).hide();

		}

		this.bindAppKeyUpdate = function() {

			// Clear validation icons when typing and validate on blur
			$( 'input#customAppKey, input#customAppSecret' ).on( 'keyup', this.clearIcons );
			$( 'input#customAppKey, input#customAppSecret' ).on( 'blur', this.checkAppKeyValidity );

		}

		this.clearIcons = function() {
			if ( self.legacyUI ) {
				$( '#gaddon-setting-row-customAppKey .fa, #gaddon-setting-row-customAppSecret .fa' ).removeClass( 'icon-check icon-remove fa-check fa-times gf_valid gf_invalid' );
			} else {
				$( '#gform_setting_customAppKey .gform-settings-input__container' ).removeClass( 'gform-settings-input__container--feedback-success gform-settings-input__container--feedback-error' );
				$( '#gform_setting_customAppSecret .gform-settings-input__container' ).removeClass( 'gform-settings-input__container--feedback-success gform-settings-input__container--feedback-error' );
			}
		}

		this.bindDeauthorize = function() {

			// De-Authorize Dropbox.
			$( '#gform_dropbox_deauth_button' ).on( 'click', function( e ){

				// Prevent default event.
				e.preventDefault();

				// Set disabled state.
				$( this ).attr( 'disabled', 'disabled' );

				// De-Authorize.
				$.ajax( {
					async:    false,
					url:      ajaxurl,
					dataType: 'json',
					data:     {
						action: 'gfdropbox_deauthorize',
						nonce:  gform_dropbox_pluginsettings_strings.nonce_deauthorize
					},
					success:  function ( response ) {

						if ( response.success ) {
							window.location.href = self.pageURL;
						} else {
							if ( response.data.code === 401 ) {
								// A 401 means the access code was revoked elsewhere. We clear the settings and reload the page.
								window.location.href = self.pageURL;
							} else {
								alert( response.data.message );
							}
						}

						$( this ).removeAttr( 'disabled' );

					}
				} );

			} );

		}

		this.bindDisableCustomApp = function() {

			$( '#gform_dropbox_disable_customApp' ).on( 'click', function( e ) {

				// Prevent default event.
				e.preventDefault();

				// Set custom app value.
				$( 'input#customAppEnable' ).val( '' );

				// Submit form.
				$( '#gform-settings-save' ).trigger( 'click' );

			} );

		}

		this.bindEnableCustomApp = function() {

			$( '#gform_dropbox_enable_customApp' ).on( 'click', function( e ) {

				// Prevent default event.
				e.preventDefault();

				// Set custom app value.
				$( 'input#customAppEnable' ).val( '1' );

				// Submit form.
				$( '#gform-settings-save' ).trigger( 'click' );

			} );

		}

		this.checkAppKeyValidity = function() {

			self.resetAppKeyStatus();

			$.ajax( {
				'url':      ajaxurl,
				'dataType': 'json',
				'type':     'GET',
				'data':     {
					'action':     'gfdropbox_valid_app_key_secret',
					'app_key':    self.getAppKey(),
					'app_secret': self.getAppSecret(),
					'nonce':      gform_dropbox_pluginsettings_strings.nonce_validation
				},
				'success':  function ( result ) {
					if ( ! result.success ) {
						self.lockAppKeyFields( false );
						self.setAppKeyStatus( result.success, null, result.data );
					} else {
						self.setAppKeyStatus( result.success, result.data.auth_url, result.data );
						self.lockAppKeyFields( false );
					}
				},
			} );

		}

		this.getAppKey = function() {

			return $( 'input#customAppKey' ).val();

		}

		this.getAppSecret = function() {

			return $( 'input#customAppSecret' ).val();

		}

		this.lockAppKeyFields = function( locked ) {

			$( 'input#customAppKey, input#customAppSecret' ).prop( 'disabled', locked );

		}

		this.resetAppKeyStatus = function() {

			if ( this.legacyUI ) {
				$( '#gaddon-setting-row-customAppKey .fa, #gaddon-setting-row-customAppSecret .fa' ).removeClass( 'icon-check icon-remove fa-check fa-times gf_valid gf_invalid' );
			} else {
				$( '#gform_setting_customAppKey .gform-settings-field__feedback, #gform_setting_customAppSecret .gform-settings-field__feedback' ).removeClass( 'gform-settings-field__feedback--valid gform-settings-field__feedback--invalid' )
			}

		}

		this.setAppKeyStatus = function( valid, auth_url, data ) {

			if ( data === undefined ) {
				return;
			}

			// If either field is empty, don't set status
			if ( data.valid_key === null || data.valid_secret === null ) {
				return;
			}

			// If the secret field validates, we know both are correct.
			if ( data.valid_secret ) {
				if ( this.legacyUI ) {
					$( '#gaddon-setting-row-customAppKey .fa, #gaddon-setting-row-customAppSecret .fa' ).addClass( 'icon-check fa-check gf_valid' );
				} else {
					$( '#gform_setting_customAppKey .gform-settings-input__container' ).addClass( 'gform-settings-input__container--feedback-success' );
					$( '#gform_setting_customAppSecret .gform-settings-input__container' ).addClass( 'gform-settings-input__container--feedback-success' );
				}
				$( '#gform_dropbox_auth_message' ).hide();
				$( '#gform_dropbox_auth_button' ).show();
				$( '#gform_dropbox_auth_button' ).attr( 'href', auth_url );
			}

			if ( data.valid_key && !data.valid_secret ) {
				if ( this.legacyUI ) {
					$( '#gaddon-setting-row-customAppKey .fa' ).addClass( 'icon-check fa-check gf_valid' );
					$( '#gaddon-setting-row-customAppSecret .fa' ).addClass( 'icon-remove fa-times gf_invalid' );
				} else {
					$( '#gform_dropbox_auth_message' ).show();
					$( '#gform_dropbox_auth_button' ).hide();
					$( '#gform_setting_customAppKey .gform-settings-input__container' ).addClass( 'gform-settings-input__container--feedback-success' );
					$( '#gform_setting_customAppSecret .gform-settings-input__container' ).addClass( 'gform-settings-input__container--feedback-error' );
				}
			}

			if ( data.valid_key === false || data.valid_key === null ) {
				if ( this.legacyUI ) {
					$( '#gaddon-setting-row-customAppKey .fa' ).addClass( 'icon-remove fa-times gf_invalid' );
				} else {
					$( '#gform_setting_customAppKey .gform-settings-input__container' ).addClass( 'gform-settings-input__container--feedback-error' );
				}
			}

			if ( valid === false || valid === null ) {
				$( '#gform_dropbox_auth_message' ).show();
				$( '#gform_dropbox_auth_button' ).hide();
			}

		}

		this.setupValidationIcons = function() {

			if ( $( '#gaddon-setting-row-customAppKey .fa' ).length == 0 ) {
				if ( this.legacyUI ) {
					$( ' <i class="fa"></i>' ).insertAfter( $( '#customAppKey' ) );
				} else {
					$( '<span class="gform-settings-field__feedback" aria-hidden="true"></span>' ).insertAfter( $( '#customAppKey' ) );
				}
			}

			if ( $( '#gaddon-setting-row-customAppSecret .fa' ).length == 0 ) {
				if ( this.legacyUI ) {
					$( ' <i class="fa"></i>' ).insertAfter( $( '#customAppSecret' ) );
				} else {
					$( '<span class="gform-settings-field__feedback" aria-hidden="true"></span>' ).insertAfter( $( '#customAppSecret' ) );
				}
			}

		}

		this.init();
	}

	$( document ).ready( GFDropboxSettings );

} )( jQuery );

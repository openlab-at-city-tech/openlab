(function ($) {
	function checkPasswordStrength(pw, blacklist) {
		var score = window.wp.passwordStrength.meter(pw, blacklist, '');

		var message = window.pwsL10n.short;
		switch (score) {
			case 2 :
				return window.pwsL10n.bad;

			case 3 :
				return window.pwsL10n.good;

			case 4 :
				return window.pwsL10n.strong;
		}
	}

	var $account_type_field;

	jQuery(document).ready(function () {
		var $signup_form = $('#signup_form');

		$account_type_field = $('#openlab-account-type');

		var registrationFormValidation = $signup_form.parsley({
			errorsWrapper: '<ul class="parsley-errors-list"></ul>'
		}).on('field:error', function (formInstance) {

			this.$element.closest('.form-group')
					.find('.other-errors').remove();

			this.$element.closest('.form-group')
					.addClass('has-error')
					.find('.error-container').addClass('error');

			var errorMsg = this.$element.prevAll("div.error-container:first").find('li:first');

			//in some cases errorMsg is further up the chain
			if (errorMsg.length === 0) {
				errorMsg = this.$element.parent().prevAll("div.error-container:first").find('li:first');
			}

			if ( errorMsg.length === 0 ) {
				errorMsg = $(this.$element.data('parsley-errors-container')).find('li:first');
			}

			var jsElem = errorMsg[0];
						if ( 'undefined' !== typeof jsElem ) {
							jsElem.style.clip = 'auto';
							var alertText = document.createTextNode(" ");
							jsElem.appendChild(alertText);
							jsElem.style.display = 'none';
							jsElem.style.display = 'inline';
						}

			if (errorMsg.attr('role') !== 'alert') {
				errorMsg.attr('role', 'alert');
			}
		}).on('field:success', function (formInstance) {

			this.$element.closest('.form-group')
					.removeClass('has-error')
					.find('.error-container').removeClass('error');

			var errorMsg = this.$element.prevAll("div.error-container:first").find('li:first');

			//in some cases errorMsg is further up the chain
			if (errorMsg.length === 0) {
				errorMsg = this.$element.parent().prevAll("div.error-container:first").find('li:first');
			}

			errorMsg.attr('role', '');
		});

		var inputBlacklist = [
			'signup_username',
			'field_1', // Display Name
			'field_241', // First Name
			'field_3'    // Last Name
		];

		var $password_strength_notice = $('#password-strength-notice');
		$('body').on('keyup', '#signup_password', function (e) {
			var blacklistValues = [];
			for (var i = 0; i < inputBlacklist.length; i++) {
				var blacklistField = document.getElementById( inputBlacklist[i] );
				if ( blacklistField ) {
					var fieldValue = document.getElementById(inputBlacklist[ i ]).value;
					if (4 <= fieldValue.length) {
						// Exclude short items. See password-strength-meter.js.
						blacklistValues.push(fieldValue);
					}
				}
			}

			var score = window.wp.passwordStrength.meter(e.target.value, blacklistValues, '');

			var message = window.pwsL10n.short;
			switch (score) {
				case 2 :
					message = window.pwsL10n.bad;
					break;

				case 3 :
					message = window.pwsL10n.good;
					break;

				case 4 :
					message = window.pwsL10n.strong;
					break;
			}

			$password_strength_notice
					.show()
					.html(message)
					.removeClass('strength-0 strength-1 strength-2 strength-3 strength-4').
					addClass('strength-' + score);
		});

		var initValidation = false;
		var asyncLoaded = false;
		formValidation($signup_form);

				$('.email-autocomplete').each(function(){
					var emailInput = $(this);
					var inputHasAutocomplete = false;

					emailInput.on('keyup change',function(){
						// Remove siblings with the .submitted-form-validation-error class.
						$( this ).siblings( '.submitted-form-validation-error' ).remove();

						validateEmail( this );

						var selectedAccountType = $account_type_field.val();

						// Do nothing for Non-City Tech.
						if ( 'Non-City Tech' === selectedAccountType ) {
							return;
						}

						var dataListId = emailInput.attr( 'name' ) + '-datalist';

						var atPosition = this.value.indexOf( '@' );
						if ( -1 === atPosition && inputHasAutocomplete ) {
							$( '#' + dataListId ).remove();
							emailInput.removeAttr( 'list' );
							inputHasAutocomplete = false;
						} else if ( -1 !== atPosition && ! inputHasAutocomplete ) {
							var beforeAt = this.value.substr( 0, atPosition )

							var emailDomain = 'citytech.cuny.edu';
							if ( 'student' === selectedAccountType || 'alumni' === selectedAccountType ) {
								emailDomain = 'mail.citytech.cuny.edu';
							}

							// Show nothing if user has selected Student but account format doesn't match.
							if ( 'student' === selectedAccountType || 'alumni' === selectedAccountType ) {
								var studentRegExp = /^[a-z0-9]+\.[a-z0-9]+$/i
								if ( ! studentRegExp.exec( beforeAt ) ) {
									return;
								}
							}

							var suggestions = [ beforeAt + '@' + emailDomain ];

							var dataList = '<datalist id="' + dataListId + '">';
							suggestions.forEach(function(suggestion){
								dataList += '<option value="' + suggestion + '" />';
							});
							dataList += '</datalist>';

							emailInput.after( dataList );
							emailInput.attr( 'list', dataListId );
							inputHasAutocomplete = true;
						}
					});
				});

		$('#signup_email').on('blur', function (e) {
			var email = $(e.target).val().toLowerCase();
			if (!email.length) {
				set_account_type_fields();
				return;
			}

			var emailtype = getEnteredEmailType();
			var $emaillabel = $('#signup_email_error');
			var $validationdiv = $('#validation-code');
			var $emailconfirm = $('#signup_email_confirm');

			if (0 <= email.indexOf('@mail.citytech.cuny.edu')) {
				emailtype = 'student';
			} else if (0 <= email.indexOf('@citytech.cuny.edu')) {
				emailtype = 'fs';
			} else {
				emailtype = 'nonct';
			}

			if ('nonct' == emailtype) {
				// Fade out and show a 'Checking' message.
				$emaillabel.html('<p class="parsley-errors-list other-errors">&mdash; Checking...</p>');
				$emaillabel.css('color', '#000');
				$emaillabel.fadeIn();
				$emaillabel.addClass('error');

				// Non-City Tech requires an AJAX request for verification.
				$.post(ajaxurl, {
					action: 'cac_ajax_email_check',
					'email': email,
					'code': $('#signup_validation_code').val(),
				},
				function (response) {
					var message = '';
					var show_validation = false;
					var emailCode = response.emailCode;

					switch (emailCode) {
						/*
							* Return values:
							*   1: success
							*   2: no email provided
							*   3: not a valid email address
							*   4: unsafe
							*   5: not in domain whitelist
							*   6: email exists
							*   7: Is a student email
							*/
						case "6" :
							message = 'An account already exists using that email address.';
							break;
						case "5" :
						case "4" :
							message = 'Must be a City Tech email address.';
							show_validation = true;
							break;
						case "3" :
							message = 'Not a well-formed email address. Please try again.';
							break;
						case "2" :
							message = 'The Email Address field is required.';
							break;

						case '1' :
							message = '&mdash; OK!';
							break;
						default :
							message = '';
							break;
					}

					message = '<ul class="parsley-errors-list filled other-errors"><li role="alert">' + message + '</li></ul>';

					if (emailCode != '1' && emailCode != '5' && emailCode != '4') {
						$emaillabel.fadeOut(function () {
							$emaillabel.html(message);
							$emaillabel.fadeIn();
						});
					} else if (emailCode == '1') {
						$emaillabel.fadeOut(function () {
							$emaillabel.html(message);
							$emaillabel.fadeIn();
						});
						$( '#register-avatar-upload' ).show();
					} else {
						$emaillabel.fadeOut();

						// Don't add more than one
						if (!$validationdiv.length) {
							var valbox = '<div id="validation-code" style="display:none"><label for="signup_validation_code" role="alert">Signup code <em aria-hidden="true">(required)</em> <span>Required for non-City Tech addresses</span></label><input name="signup_validation_code" id="signup_validation_code" type="text" val="" /></div>';
							$('input#signup_email').before(valbox);
							$validationdiv = $('#validation-code');
						}
					}

					if (show_validation) {
						$validationdiv.show();
					} else {
						$validationdiv.hide();
						//$emailconfirm.focus();
					}

					set_account_type_fields(response.accountType);
				});

			} else {
				$validationdiv.hide();
				$emaillabel.fadeOut();
				//$emailconfirm.focus();
				set_account_type_fields();
			}
		});

		$(document).on('blur', '#signup_validation_code', function() {
			var code = $(this).val();

			var vcodespan = $('#signup_email_error');

			$(vcodespan).fadeOut(function () {
				$(vcodespan).html('<p class="parsley-errors-list">&mdash; Checking...</p>');
				$(vcodespan).css('color', '#000');
				$(vcodespan).fadeIn();
				$(vcodespan).addClass('error');
			});

			/* Handle email verification server side because there we have the functions for it */
			$.post(ajaxurl, {
				action: 'cac_ajax_vcode_check',
				'code': code
			},
			function (response) {
				if (response.isValid) {
					$(vcodespan).fadeOut(function () {
						$(vcodespan).html('&mdash; OK!');
						$(vcodespan).css('color', '#000');
						$(vcodespan).fadeIn();
						$('div#submit')
					});

					set_account_type_fields(response.accountType);
				} else {
					$(vcodespan).fadeOut(function () {
						$(vcodespan).html('&mdash; Required for non-CUNY addresses');
						$(vcodespan).css('color', '#f00');
						$(vcodespan).fadeIn();

						// Re-set account types if non-valid code.
						set_account_type_fields();
					});
				}
			});
		});

		// Ensure that the account type field is set properly from the post
		$account_type_field.val(OLReg.post_data['openlab-account-type']);
		$account_type_field.children('option').each(function () {
			if (OLReg.post_data['openlab-account-type'] == $(this).val()) {
				$(this).attr('selected', 'selected');
			}
		});

		$account_type_field.on('change', function () {
			set_email_label( this.value );
			set_email_helper( this.value );
			load_account_type_description( this.value );
			load_account_type_fields();
			init_visible_metaboxes();
		});

		$( '[name="account-description-approval"]' ).on( 'change', function() {
			init_visible_metaboxes();
		} )

		load_account_type_description( $account_type_field.val() );
		load_account_type_fields();
		init_visible_metaboxes();

		if ( 'undefined' !== typeof OpenLab_Submitted_Visibility_Values ) {
			setTimeout( () => {
				for ( var key in OpenLab_Submitted_Visibility_Values ) {
					var value = OpenLab_Submitted_Visibility_Values[ key ];

					if ( 'undefined' !== typeof value ) {
						$( '#field-visibility-settings-select-' + key ).val( value );
					}
				}
			}, 3000 );
		}

		function containsLastName( text ) {
			if ( text.length === 0 ) {
				return false;
			}

			const userLastNameEmailMatch = document.getElementById( 'signup_email' ).value.match( /^[^\.@]+\.([a-zA-Z]+)[0-9]*@mail\.citytech\.cuny\.edu$/ );
			const userLastNameEmail = userLastNameEmailMatch ? userLastNameEmailMatch[1] : '';

			const userLastNameField = document.querySelector( '.last-name-field' )?.value;

			return (
				( userLastNameEmail.length > 0 && text.toLowerCase().includes( userLastNameEmail.toLowerCase() ) )
				||
				( userLastNameField.length > 0 && text.toLowerCase().includes( userLastNameField.toLowerCase() ) )
			);
		}

		const signupUsernameField = document.getElementById( 'signup_username' );
		if ( signupUsernameField ) {
			signupUsernameField.addEventListener( 'input', ( event ) => {
				if ( 'student' !== $account_type_field.val() ) {
					return;
				}

				const userName = event.target.value;

				const userNameContainsLastName = containsLastName( userName );

				if ( ! userNameContainsLastName ) {
					toggleUsernameContainsLastNameError( false );
					return;
				}

				toggleUsernameContainsLastNameError( true );
			} )
		}

		function toggleUsernameContainsLastNameError( show ) {
			$( '.username-contains-last-name-error' ).remove();

			if ( show ) {
				$( '#signup_username' ).after( '<div class="username-contains-last-name-error field-contains-last-name-error error">It looks like you’re using your last name in your username. Are you sure?</div>' );
			}
		}

		document.getElementById( 'field_1' ).addEventListener( 'input', ( event ) => {
			if ( 'student' !== $account_type_field.val() ) {
				return;
			}

			const displayName = event.target.value;

			const displayNameContainsLastName = containsLastName( displayName );

			if ( ! displayNameContainsLastName ) {
				toggleDisplayNameContainsLastNameError( false );
				return;
			}

			toggleDisplayNameContainsLastNameError( true );
		} )

		function toggleDisplayNameContainsLastNameError( show ) {
			$( '.display-name-contains-last-name-error' ).remove();

			if ( show ) {
				$( '#field_1' ).after( '<div class="display-name-contains-last-name-error field-contains-last-name-error error">It looks like you’re using your last name in your Display Name. Are you sure?</div>' );
			}
		}

		function validateEmail( field ) {
			var emailValue = field.value;
			var invalidCharRegExp = /[^a-zA-Z0-9\-\.@]/g
			var newValue = emailValue.replace( invalidCharRegExp, '' );

			if ( emailValue === newValue ) {
				return;
			}

			field.value = emailValue.replace( invalidCharRegExp, '' );

			var $field = $( field );
			if ( $field.parsley().isValid() ) {
				$( '#' + field.id + '_error .parsley-errors-list li' ).remove();
			}
		}

		function set_email_label( accountType ) {
			var label = 'Non-City Tech' === accountType ? 'Email Address' : 'City Tech Email Address';
			$( '#signup-email-label .label-text' ).html( label );
		}

		function set_email_helper( accountType ) {
			var helper = '';

			if ( 'student' === accountType ) {
				helper = 'Example: first.lastname@mail.citytech.cuny.edu or first.lastname1@mail.citytech.cuny.edu.';
			} else if ( 'faculty' === accountType ) {
				helper = 'Example: jdoe@citytech.cuny.edu.';
			}

			$('.email-requirements').fadeOut( function() {
				$(this).html( helper ).fadeIn();
			} );
		}

		function get_account_type_option_markup( value, text, typeSelected ) {
			var markup = '<option value="' + value + '"';

			if ( typeSelected === value ) {
				markup += ' selected="selected"';
			}

			markup += '>' + text + '</option>';

			return markup;
		}

		function set_account_type_fields( accountType ) {
			var newtypes = '';
			var emailtype = getEnteredEmailType();

			var typeSelected = $account_type_field.children('option:selected').val();

			if ('fs' == emailtype || 'empty' === emailtype ) {
				newtypes += get_account_type_option_markup( '', '----', typeSelected );
				newtypes += get_account_type_option_markup( 'faculty', 'Faculty', typeSelected );
				newtypes += get_account_type_option_markup( 'staff', 'Staff', typeSelected );
			}

			if ('student' == emailtype || 'empty' === emailtype ) {
				newtypes += get_account_type_option_markup( 'student', 'Student', typeSelected );
				newtypes += get_account_type_option_markup( 'alumni', 'Alumni', typeSelected );
			}

			if ('nonct' == emailtype || 'empty' === emailtype ) {
				newtypes += get_account_type_option_markup( 'non-city-tech', 'Non-City Tech', typeSelected );
			}

			if ( accountType ) {
				newtypes = get_account_type_option_markup( accountType, accountType, typeSelected );
			}

			$account_type_field.html(newtypes);

			/*
			 * Because there is no alternative in the dropdown, the 'change' event never
			 * fires. So we trigger it manually.
			 */
			load_account_type_fields();
			$account_type_field.parsley().validate();
		}

		/**
		 * Load the account type description for the selected account type.
		 */
		function load_account_type_description( accountType ) {
			$( '.account-type-description' ).hide();
			$( '.account-description-approval-fieldset' ).hide();

			if ( accountType && accountType.length > 0 ) {
				$( '.account-type-description[data-account-type="' + accountType + '"]' ).show();
				$( '.account-description-approval-fieldset' ).show();
			}
		}

		/**
		 * Initialize the visible metaboxes based on the selected account type.
		 */
		function init_visible_metaboxes() {
			if ( ! $account_type_field.length ) {
				return;
			}

			// All .panel except #panel-welcome.
			var $panels = $( '.panel' ).not( '#panel-welcome' );

			const showMetaboxes = $account_type_field.val().length > 0 && $('[name="account-description-approval"]:checked').val() === 'yes';

			if ( showMetaboxes ) {
				$panels.show();
			} else {
				$panels.hide();
			}
		}

		//load register account type
		function load_account_type_fields() {
			var default_type = '';
			var selected_account_type = $account_type_field.children('option:selected').val();

			if (document.getElementById('signup_submit')) {

				$('#signup_submit').on('click', function (e) {

					var thisElem = $(this);

					if (thisElem.hasClass('btn-disabled')) {
						e.preventDefault();
						var message = 'Please Complete Required Fields To Continue';
						$('#submitSrMessage').text(message);
					}

				});

				deinitAvatarFields();

				$.ajax(ajaxurl, {
					data: {
						action: 'wds_load_account_type',
						account_type: selected_account_type,
						post_data: OLReg.post_data
					},
					method: 'POST',
					success: function (response) {

						var $wds_fields = $('#wds-account-type');

						$wds_fields.html(response);

						load_error_messages();

						if (response !== 'Please select an Account Type.') {

							asyncLoaded = true;
							//reset validation
							initValidation = false;
							formValidation($wds_fields);
							updateSubmitButtonStatus();
							openlab.academicUnits.init();
							initAvatarFields();

							// If this is an Account Type other than 'Non-City Tech', show account fields.
							if ( 'non-city-tech' !== selected_account_type ) {
								$( '#register-avatar-upload' ).show();
							}
						}
					}
				});
			}
		}

		function formValidation(fieldParent) {
			evaluateFormValidation();

			fieldParent.find('input').on('input blur', function (e) {

				evaluateFormValidation();

			});

			fieldParent.find('select').on('change blur', function (e) {

				evaluateFormValidation();

			});
		}

		function updateSubmitButtonStatus() {

			if (initValidation) {
				$('#signup_submit').removeClass('btn-disabled');
				$('#signup_submit').val('Complete Sign Up');
			} else if (!$('#signup_submit').hasClass('btn-disabled')) {
				$('#signup_submit').addClass('btn-disabled');
				$('#signup_submit').val('Please Complete Required Fields');
			}

		}

		/**
		 * Unhide avatar upload fields, and move to the correct place in the DOM.
		 */
		function initAvatarFields() {
			var $avatar_fields = $( '#register-avatar-upload' );

			/*
			 * We insert:
			 * a. After the S/O/D fields (Faculty/Staff)
			 * b. After 'Major Program of Study' (Student/Alumni)
			 * c. At the beginning of the wds-account-type div (Non-City Tech)
			 */
			var elementToInsertAfter = null;
			var elementToInsertInto = null;
			var academicUnitSelector = document.querySelector( '.academic-unit-selector' );
			var departmentSelector = document.querySelector( 'select[name="departments-dropdown"]' );
			if ( academicUnitSelector ) {
				elementToInsertAfter = academicUnitSelector.closest( '.editfield' );
			} else if ( departmentSelector ) {
				elementToInsertAfter = departmentSelector.closest( '.editfield' );
			} else {
				elementToInsertInto = document.getElementById( 'wds-account-type' );
			}

			if ( ! elementToInsertAfter && ! elementToInsertInto ) {
				return;
			}

			if ( elementToInsertInto ) {
				$( elementToInsertInto ).prepend( $avatar_fields );
			} else {
				$( elementToInsertAfter ).after( $avatar_fields );
			}
		}

		/**
		 * "Deinit" avatar fields by moving them back to their original place in the DOM.
		 *
		 * This is necessary because the new location is removed as part of the
		 * member-type profile field AJAX.
		 */
		function deinitAvatarFields() {
			var $avatar_fields = $( '#register-avatar-upload' );
			var $register_page = $( '#register-page' );
			$register_page.after( $avatar_fields );
			$avatar_fields.hide();
		}

		function evaluateFormValidation() {
			if ( asyncLoaded ) {
				registrationFormValidation.whenValid()
				  .then(
						function() {
							initValidation = true;
							updateSubmitButtonStatus();
						}
					).
					catch(
						function() {
							initValidation = false;
							updateSubmitButtonStatus();
						}
					);
				return;
			} else {
				initValidation = false;
				updateSubmitButtonStatus();
			}
		}

		/**
		 * Put registration error messages into the template dynamically.
		 *
		 * See openlab_registration_errors_object().
		 */
		function load_error_messages() {
			jQuery.each(OpenLab_Registration_Errors, function (k, v) {
				$('#' + k).before(v);
			});
		}
	});

	function getEnteredEmailType() {
		var email = $('#signup_email').val();
		var emailtype;

		if (0 <= email.indexOf('mail.citytech.cuny.edu')) {
			emailtype = 'student';
		} else if (0 <= email.indexOf('citytech.cuny.edu')) {
			emailtype = 'fs';
		} else if ( 0 === email.length ) {
			emailtype = 'empty';
		} else {
			emailtype = 'nonct';
		}

		return emailtype;
	}

}(jQuery));

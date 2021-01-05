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

    jQuery(document).ready(function () {
        var $signup_form = $('#signup_form');
        var $account_type_field = $('#field_' + OLReg.account_type_field);

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
            jsElem.style.clip = 'auto';
            var alertText = document.createTextNode(" ");
            jsElem.appendChild(alertText);
            jsElem.style.display = 'none';
            jsElem.style.display = 'inline';

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
                var fieldValue = document.getElementById(inputBlacklist[ i ]).value;
                if (4 <= fieldValue.length) {
                    // Exclude short items. See password-strength-meter.js.
                    blacklistValues.push(fieldValue);
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
        var asyncValidation = false;
        var asyncLoaded = false;
        formValidation($signup_form);

				$('.email-autocomplete').each(function(){
					var emailInput = $(this);
					var inputHasAutocomplete = false;

					emailInput.on('keyup',function(){
						var dataListId = emailInput.attr( 'name' ) + '-datalist';

						var atPosition = this.value.indexOf( '@' );
						if ( -1 === atPosition && inputHasAutocomplete ) {
							$( '#' + dataListId ).remove();
							emailInput.removeAttr( 'list' );
							inputHasAutocomplete = false;
						} else if ( -1 !== atPosition && ! inputHasAutocomplete ) {
							var beforeAt = this.value.substr( 0, atPosition )

							var emailDomain = 'citytech.cuny.edu';
							if ( 'Student' === $account_type_field.val() ) {
								emailDomain = 'mail.citytech.cuny.edu';
							}

							// Show nothing if user has selected Student but account format doesn't match.
							if ( 'Student' === $account_type_field.val() ) {
								var studentRegExp = /^[a-z0-9]+\.[a-z0-9]+$/
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
                return;
            }

            var emailtype = '';
            var $emaillabel = $('#signup_email_error');
            var $validationdiv = $('#validation-code');
            var $emailconfirm = $('#signup_email_confirm');

            if (0 <= email.indexOf('mail.citytech.cuny.edu')) {
                emailtype = 'student';
            } else if (0 <= email.indexOf('citytech.cuny.edu')) {
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
                    'email': email
                },
                        function (response) {
                            var message = '';
                            var show_validation = false;

                            switch (response) {
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

                            if (response != '1' && response != '5' && response != '4') {
                                $emaillabel.fadeOut(function () {
                                    $emaillabel.html(message);
                                    $emaillabel.fadeIn();
                                });
                            } else if (response == '1') {
                                $emaillabel.fadeOut(function () {
                                    $emaillabel.html(message);
                                    $emaillabel.fadeIn();
                                });
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
                                //                           $emailconfirm.focus();
                            }

                            set_account_type_fields();
                        });

            } else {
                $validationdiv.hide();
                $emaillabel.fadeOut();
//            $emailconfirm.focus();
                set_account_type_fields();
            }

            function set_account_type_fields() {
                var newtypes = '';

                if ('student' == emailtype) {
                    newtypes += '<option value="Student">Student</option>';
                    newtypes += '<option value="Alumni">Alumni</option>';
                }

                if ('fs' == emailtype) {
                    newtypes += '<option value="">----</option>';
                    newtypes += '<option value="Faculty">Faculty</option>';
                    newtypes += '<option value="Staff">Staff</option>';
                }

                if ('nonct' == emailtype) {
                    newtypes += '<option value="Non-City Tech">Non-City Tech</option>';
                }

                if ('' == emailtype) {
                    newtypes += '<option value="">----</option>';
                }

                var $typedrop = $('#field_7');
                $typedrop.html(newtypes);

                /*
                 * Because there is no alternative in the dropdown, the 'change' event never
                 * fires. So we trigger it manually.
                 */
                load_account_type_fields();
								$typedrop.parsley().validate();
            }
        });

        $('input#signup_validation_code').live('blur', function () {
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
                        if ('1' == response) {
                            $(vcodespan).fadeOut(function () {
                                $(vcodespan).html('&mdash; OK!');
                                $(vcodespan).css('color', '#000');
                                $(vcodespan).fadeIn();
                                $('div#submit')
                            });
                        } else {
                            $(vcodespan).fadeOut(function () {
                                $(vcodespan).html('&mdash; Required for non-CUNY addresses');
                                $(vcodespan).css('color', '#f00');
                                $(vcodespan).fadeIn();
                            });
                        }
                    });
        });

        // Ensure that the account type field is set properly from the post
        $account_type_field.val(OLReg.post_data.field_7);
        $account_type_field.children('option').each(function () {
            if (OLReg.post_data.field_7 == $(this).val()) {
                $(this).attr('selected', 'selected');
            }
        });

        $account_type_field.on('change', function () {
						set_email_placeholder( this.value );
						set_email_helper( this.value );
            load_account_type_fields();
        });
        load_account_type_fields();

				function set_email_placeholder( accountType ) {
					var placeholder = '';

					if ( 'Student' === accountType ) {
						placeholder = 'first.lastname@mail.citytech.cuny.edu';
					} else if ( 'Faculty' === accountType || 'Staff' === accountType ) {
						placeholder = 'first.lastname@citytech.cuny.edu';
					}

					if ( placeholder.length > 0 ) {
						//$('#signup_email').attr('placeholder', placeholder);
					}
				}

				function set_email_helper( accountType ) {
					var helper = 'Please use your City Tech email address to register.';

					if ( 'Student' === accountType ) {
						helper = 'Please use your City Tech email address. Example: first.lastname@mail.citytech.cuny.edu or first.lastname1@mail.citytech.cuny.edu.';
					}

					$('.email-requirements').fadeOut( function() {
						$(this).html( helper ).fadeIn();
					} );
				}

        //load register account type
        function load_account_type_fields() {
            var default_type = '';
            var selected_account_type = $account_type_field.val();

            if (document.getElementById('signup_submit')) {

                $('#signup_submit').on('click', function (e) {

                    var thisElem = $(this);

                    if (thisElem.hasClass('btn-disabled')) {
                        e.preventDefault();
                        var message = 'Please Complete Required Fields To Continue';
                        $('#submitSrMessage').text(message);
                    }

                });

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

        function evaluateFormValidation() {

            if (asyncLoaded && registrationFormValidation.isValid()) {
                initValidation = true;
            } else {
                initValidation = false;
            }

            updateSubmitButtonStatus();
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

}(jQuery));

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

    window.Parsley.addValidator('atleastonedept', {
        validateMultiple: function (value) {
            return value.length !== 0;
        },
        messages: {
            en: 'Please provide a school and department.'
        }
    });

    window.Parsley.addValidator('newSiteValidate', {
        validateString: function(value, requirement, instance) {
            // Remove errors related to this validation
            instance.removeError('en');

            // Setup site is checked AND create new site is checked
            if( $('#wds_website_check').is(':checked') && $(requirement).is(':checked') ) {
                
                if( value.length == 0 ) {
                    return !!value;
                }

                if ( $('body').hasClass( 'group-admin' ) ) {
                    form  = document.getElementById( 'group-settings-form' );
                } else {
                    form  = document.getElementById( 'create-group-form' );
                }
        
                $form = $( form );

                $.post(
                    '/wp-admin/admin-ajax.php', // Forward-compatibility with ajaxurl in BP 1.6
                    {
                        action: 'openlab_validate_groupblog_url_handler',
                        'path': value
                    },
                    function( response ) {
                        if ( 'exists' == response ) {
                            instance.addError('en', { message: 'Sorry, that URL is already taken.'});
                            return !!value;
                        } else {
                            $form.append( '<input name="save" value="1" type="hidden" />' );
                            return true;
                        }
                    }
                );

                return true;
            }

            return true;
        },
        messages: {
            en: 'Please provide a site address.'
        }
    });

}( window, jQuery ) );

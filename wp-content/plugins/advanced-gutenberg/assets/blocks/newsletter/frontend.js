jQuery(document).ready(function ($) {
    var { __ } = wp.i18n;

    $('.advgb-newsletter input.advgb-form-input').on('keydown', function (e) {
        if(e.which === 13) {
            e.preventDefault();
            return false;
        }
    });

    $('.advgb-newsletter.style-alt .advgb-grecaptcha').each(function () {
        var submitWrapper = $(this).closest('.advgb-newsletter.style-alt').find('.advgb-form-submit-wrapper');
        $(this).insertBefore(submitWrapper);
    });

    $('.advgb-newsletter form').submit(function (e) {
        e.preventDefault();
        var $thisForm = $(this).closest('.advgb-newsletter');
        var firstName = $(this).find('.advgb-form-input-fname').val();
        var lastName = $(this).find('.advgb-form-input-lname').val();
        var email = $(this).find('.advgb-form-input-email').val();
        var date = new Date();
        var submitDate = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ' - ' + (date.getHours() < 10 ? '0' : '') + date.getHours() + ':' + (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
        var g_id = parseInt($thisForm.find('.advgb-grecaptcha').data('gid'));
        var emailValid = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (typeof firstName !== "undefined") firstName = firstName.trim();
        if (typeof lastName !== "undefined") lastName = lastName.trim();
        if (typeof email !== "undefined") email = email.trim();

        if (firstName === '' || lastName === '' || email === '') {
            var alertText = $thisForm.find('.advgb-form-submit').data('alert');
            alertText = alertText ? alertText : __('You need to fill all fields!', 'advanced-gutenberg');
            alert(alertText);
            return false;
        }

        if(emailValid.test(email)) {
            console.log(__('Email is valid.', 'advanced-gutenberg'));
        } else {
            alert(__('Use a valid email address!', 'advanced-gutenberg'));
            return false;
        }

        var nonce_val = $('[name="advgb_blockform_nonce_field"]').val();

        $.ajax( {
            url: advgbNewsletter.ajax_url,
            type: "POST",
            data: {
                action: 'advgb_newsletter_save',
                f_name: firstName,
                l_name: lastName,
                email: email,
                submit_date: submitDate,
                captcha: typeof grecaptcha !== "undefined" ? grecaptcha.getResponse(g_id) : undefined,
                nonce: nonce_val
            },
            beforeSend: function () {
                $thisForm.find('.advgb-form-submit-wrapper').append('<div class="advgb-form-sending" />');
                $thisForm.find('.advgb-form-submit-success').remove();
            },
            success: function () {
                $thisForm.find('.advgb-form-sending').remove();
                var successText = $thisForm.find('.advgb-form-submit').data('success');
                successText = successText ? successText : __('Submitted with success!', 'advanced-gutenberg');
                $thisForm.append('<div class="advgb-form-submit-success">'+ successText +'</div>');
            },
            error: function ( jqxhr, textStatus, error ) {
                alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                $thisForm.find('.advgb-form-sending').remove();
            }
        } )
    });
});

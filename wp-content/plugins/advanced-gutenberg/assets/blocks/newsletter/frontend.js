jQuery(document).ready(function ($) {
    var { __ } = wp.i18n;
    var l10n = typeof advgbNewsletter !== 'undefined' ? advgbNewsletter : {};
    var srOnlyStyle = 'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;';

    // Accessibility: autocomplete, required state and announcement regions
    $('.advgb-newsletter').each(function (index) {
        var $block = $(this);
        var formId = 'advgb-newsletter-' + (index + 1);

        $block.find('.advgb-form-input-fname').attr('autocomplete', 'given-name');
        $block.find('.advgb-form-input-lname').attr('autocomplete', 'family-name');
        $block.find('.advgb-form-input-email').attr('autocomplete', 'email');
        $block.find('.advgb-form-input').attr('aria-required', 'true').each(function () {
            var fieldClass = (this.className.match(/advgb-form-input-(\w+)/) || [])[1] || 'field';
            $(this).attr('data-advgb-error-id', formId + '-' + fieldClass + '-error');
        });

        $block.append('<div class="advgb-form-error-message" role="alert"></div>');
        $block.append('<div class="advgb-form-status" role="status" aria-live="polite"></div>');
    });

    function clearErrors($thisForm) {
        $thisForm.find('.advgb-form-error-message').empty();
        $thisForm.find('.advgb-form-field-error').remove();
        $thisForm.find('.advgb-form-input').removeAttr('aria-invalid').each(function () {
            var describedBy = ($(this).attr('aria-describedby') || '').replace($(this).data('advgb-error-id'), '').trim();
            if (describedBy) {
                $(this).attr('aria-describedby', describedBy);
            } else {
                $(this).removeAttr('aria-describedby');
            }
        });
    }

    function markInvalid($field, message) {
        var errorId = $field.data('advgb-error-id');
        var describedBy = $field.attr('aria-describedby');
        $field.attr('aria-invalid', 'true')
            .attr('aria-describedby', describedBy ? describedBy + ' ' + errorId : errorId)
            .after($('<span/>', { class: 'advgb-form-field-error', id: errorId }).text(message));
    }

    function showError($thisForm, message) {
        $thisForm.find('.advgb-form-error-message').text(message);
    }

    function serverErrorMessage(jqxhr) {
        var message = jqxhr && typeof jqxhr.responseJSON === 'string' ? jqxhr.responseJSON : '';
        message = message.replace(/<[^>]*>/g, '').trim();
        return message || l10n.server_error || __('Something went wrong. Please try again later.', 'advanced-gutenberg');
    }

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
        var submitDate = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ' - ' + ( date.getHours() < 10 ? '0' : '' ) + date.getHours() + ':' + ( date.getMinutes() < 10 ? '0' : '' ) + date.getMinutes();
        var g_id = parseInt($thisForm.find('.advgb-grecaptcha').data('gid'));
        var emailValid = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (typeof firstName !== "undefined") firstName = firstName.trim();
        if (typeof lastName !== "undefined") lastName = lastName.trim();
        if (typeof email !== "undefined") email = email.trim();

        clearErrors($thisForm);

        if (firstName === '' || lastName === '' || email === '') {
            var alertText = $thisForm.find('.advgb-form-submit').data('alert');
            alertText = alertText ? alertText : (l10n.fill_all_fields || __('You need to fill all fields!', 'advanced-gutenberg'));
            var $invalid = $thisForm.find('.advgb-form-input').filter(function () {
                return $(this).val().trim() === '';
            });
            $invalid.each(function () {
                var fieldName = $(this).attr('placeholder') || '';
                markInvalid($(this), (l10n.field_required || __('%s is required.', 'advanced-gutenberg')).replace('%s', fieldName));
            });
            showError($thisForm, alertText);
            $invalid.first().trigger('focus');
            return false;
        }

        if(emailValid.test(email)) {
            console.log(__('Email is valid.', 'advanced-gutenberg'));
        } else {
            var invalidEmailText = l10n.invalid_email || __('Use a valid email address!', 'advanced-gutenberg');
            var $emailField = $thisForm.find('.advgb-form-input-email');
            markInvalid($emailField, invalidEmailText);
            showError($thisForm, invalidEmailText);
            $emailField.trigger('focus');
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
                $thisForm.find('.advgb-form-status').empty().append(
                    $('<span/>', { style: srOnlyStyle }).text(l10n.sending || __('Sending...', 'advanced-gutenberg'))
                );
            },
            success: function () {
                $thisForm.find('.advgb-form-sending').remove();
                var successText = $thisForm.find('.advgb-form-submit').data('success');
                successText = successText ? successText : (l10n.success || __('Submitted with success!', 'advanced-gutenberg'));
                $thisForm.find('.advgb-form-status').empty().append(
                    $('<div/>', { class: 'advgb-form-submit-success' }).text(successText)
                );
            },
            error: function ( jqxhr ) {
                $thisForm.find('.advgb-form-sending').remove();
                $thisForm.find('.advgb-form-status').empty();
                showError($thisForm, serverErrorMessage(jqxhr));
            }
        } )
    });
});

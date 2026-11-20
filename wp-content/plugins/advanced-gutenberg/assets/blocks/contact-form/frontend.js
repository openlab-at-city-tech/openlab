jQuery(document).ready(function ($) {
    var l10n = typeof advgbContactForm !== 'undefined' ? advgbContactForm : {};
    var srOnlyStyle = 'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;';

    // Accessibility: autocomplete, required state and announcement regions
    $('.advgb-contact-form').each(function (index) {
        var $block = $(this);
        var formId = 'advgb-contact-form-' + (index + 1);

        $block.find('.advgb-form-input-name').attr('autocomplete', 'name');
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
        return message || l10n.server_error || 'Something went wrong. Please try again later.';
    }

    $('.advgb-contact-form input.advgb-form-input').on('keydown', function (e) {
        if(e.which === 13) {
            e.preventDefault();
            return false;
        }
    });

    $('.advgb-contact-form form').submit(function (e) {
        e.preventDefault();
        var $thisForm = $(this).closest('.advgb-contact-form');
        var contactName = $(this).find('.advgb-form-input-name').val();
        var contactEmail = $(this).find('.advgb-form-input-email').val();
        var contactMsg = $(this).find('.advgb-form-input-msg').val();
        var date = new Date();
        var submitDate = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ' - ' + ( date.getHours() < 10 ? '0' : '' ) + date.getHours() + ':' + ( date.getMinutes() < 10 ? '0' : '' ) + date.getMinutes();
        var g_id = parseInt($thisForm.find('.advgb-grecaptcha').data('gid'));

        clearErrors($thisForm);

        if (contactName === '' || contactEmail === '' || contactMsg === '') {
            var alertText = $thisForm.find('.advgb-form-submit').data('alert');
            alertText = alertText ? alertText : (l10n.fill_all_fields || 'You need to fill all fields!');
            var $invalid = $thisForm.find('.advgb-form-input').filter(function () {
                return $(this).val() === '';
            });
            $invalid.each(function () {
                var fieldName = $(this).attr('placeholder') || $(this).attr('name');
                markInvalid($(this), (l10n.field_required || '%s is required.').replace('%s', fieldName));
            });
            showError($thisForm, alertText);
            $invalid.first().trigger('focus');
            return false;
        }

        var nonce_val = $('[name="advgb_blockform_nonce_field"]').val();

        $.ajax( {
            url: advgbContactForm.ajax_url,
            type: "POST",
            data: {
                action: 'advgb_contact_form_save',
                contact_name: contactName,
                contact_email: contactEmail,
                contact_msg: contactMsg,
                submit_date: submitDate,
                captcha: typeof grecaptcha !== "undefined" ? grecaptcha.getResponse(g_id) : undefined,
                nonce: nonce_val
            },
            beforeSend: function () {
                $thisForm.find('.advgb-form-submit-wrapper').append('<div class="advgb-form-sending" />');
                $thisForm.find('.advgb-form-submit-success').remove();
                $thisForm.find('.advgb-form-status').empty().append(
                    $('<span/>', { style: srOnlyStyle }).text(l10n.sending || 'Sending...')
                );
            },
            success: function () {
                $thisForm.find('.advgb-form-sending').remove();
                var successText = $thisForm.find('.advgb-form-submit').data('success');
                successText = successText ? successText : (l10n.success || 'Message sent with success!');
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

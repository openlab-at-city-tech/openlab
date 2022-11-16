jQuery(function ($) {
    'use strict';
    let $modal           = $('#bookly-confirm-email-modal'),
        $code            = $('#bookly-confirmation-code', $modal),
        $resendBtn       = $('.bookly-js-resend-confirmation', $modal),
        $applyBtn        = $('#bookly-apply-confirmation-code', $modal),
        $doItLaterBtn    = $('.modal-footer button[data-dismiss="bookly-modal"]', $modal),
        $openModalBtn    = $('#bookly-open-email-confirm'),
        $openSettingsBtn = $('#bookly-open-account-settings');

    // Apply code.
    $applyBtn.on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this);
        ladda.start();
        $.post(
            ajaxurl,
            {
                action: 'bookly_apply_confirmation_code',
                code: $code.val(),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            function (response) {
                if (response.success) {
                    $openModalBtn.hide();
                    $openSettingsBtn.removeClass('btn-danger').addClass('btn-primary')
                        .find('i').removeClass('fa-user-slash').addClass('fa-user');
                    $modal.booklyModal('hide');
                } else {
                    booklyAlert({error: [response.data.message]});
                }
                ladda.stop();
            });
    });

    // Resend code.
    $resendBtn.on('click', function (e) {
        e.preventDefault();
        $.post(
            ajaxurl,
            {
                action: 'bookly_resend_confirmation_code',
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            function (response) {
                if (response.success) {
                    booklyAlert({success: [BooklyCloudPanelL10n.confirm_email_code_resent]})
                } else {
                    booklyAlert({error: [response.data.message]});
                }
            });
    });

    // I'll do it later button.
    $doItLaterBtn.on('click', function (e) {
        $.post(
            ajaxurl,
            {
                action: 'bookly_dismiss_confirm_email',
                csrf_token: BooklyL10nGlobal.csrf_token
            });
    });

    // Open table settings modal.
    $openModalBtn.on('click', function () {
        $modal.booklyModal('show');
    });
    if (BooklyCloudPanelL10n.show_confirm_email_dialog) {
        $modal.booklyModal('show');
    }
});
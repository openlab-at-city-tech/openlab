jQuery(function ($) {
    'use strict';
    let $modal = $('#bookly-smtp-test-modal'),
        $log = $('#bookly-smtp-log', $modal),
        $status = $('#bookly-smtp-status', $modal);

    $modal.on('show.bs.modal', function () {
        $log.html('');
        $status.hide();
    });

    $('#bookly-send-smtp-test', $modal).on('click', function () {
        let ladda = Ladda.create(this);
        ladda.start();

        $log.html('<div class="bookly-loading"></div>').show();
        $status.hide();

        $.post(
            ajaxurl,
            {
                action: 'bookly_send_smtp_test',
                csrf_token: BooklyL10nGlobal.csrf_token,
                to: $('#bookly-smtp-to', $modal).val(),
                host: $('#bookly_smtp_host').val(),
                port: $('#bookly_smtp_port').val(),
                user: $('#bookly_smtp_user').val(),
                password: $('#bookly_smtp_password').val(),
                secure: $('#bookly_smtp_secure :selected').val(),
            },
            function (response) {
                $status
                .find('#bookly-smtp-status-text')
                .html(response.data.status ? BooklySmtpTestDialogL10n.success : BooklySmtpTestDialogL10n.failed)
                .removeClass('text-success text-danger')
                .addClass(response.data.status ? 'text-success' : 'text-danger');

                $status.show();
                $log.html(response.data.result);
                ladda.stop();
            }
        );
    });
});
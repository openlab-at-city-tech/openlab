jQuery(function ($) {
    let $alert = $('#bookly-subscribe-notice');
    $('#bookly-subscribe-btn').on('click', function () {
        let $email = $('#bookly-subscribe-email', $alert),
            ladda  = Ladda.create(this);
        $email.removeClass('is-invalid');
        ladda.start();
        $.post(ajaxurl, {action: 'bookly_subscribe', csrf_token: BooklyL10nGlobal.csrf_token, email: $email.val()}, function (response) {
            ladda.stop();
            if (response.success) {
                $alert.alert('close');
                booklyAlert({success: [response.data.message]});
            } else {
                $email.addClass('is-invalid');
                booklyAlert({error: [response.data.message]});
            }
        });
    });
    $alert.on('close.bs.alert', function () {
        $.post(ajaxurl, {action: 'bookly_dismiss_subscribe_notice', csrf_token: BooklyL10nGlobal.csrf_token}, function () {
            // Indicator for Selenium that request has completed.
            $alert.remove();
        });
    });
});
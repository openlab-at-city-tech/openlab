jQuery(function ($) {
    'use strict';

    let $notice = $('#bookly-js-renew'),
        $renewPayPal = $('.bookly-js-renew-paypal', $notice),
        $renewStripe = $('.bookly-js-renew-stripe', $notice),
        $dismiss = $('.bookly-js-maybe-later', $notice),
        $close = $('.bookly-js-dismiss', $notice),
        data = {
            action: 'bookly_hide_renew_notice',
            csrf_token: BooklyL10nGlobal.csrf_token,
            hide_until: 'forever'
        };

    $close
        .on('click', function () {
            data.hide_until = 'forever';
            $.post(ajaxurl, data);
            $notice.remove();
        });
    $dismiss
        .on('click', function () {
            data.hide_until = 'short-time';
            $.post(ajaxurl, data);
            $notice.remove();
        });
    $renewPayPal
        .on('click', function () {
            renewAutoRecharge('paypal');
        });
    $renewStripe
        .on('click', function () {
            renewAutoRecharge('stripe');
        });

    function renewAutoRecharge(gateway) {
        $.ajax({
            method: 'POST',
            url: ajaxurl,
            data: {
                action: 'bookly_renew_auto_recharge',
                csrf_token: BooklyL10nGlobal.csrf_token,
                gateway: gateway
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.replace(response.data.redirect_url);
                } else {
                    $notice.remove()
                    booklyAlert({error: [response.data.message]});
                }
            }
        });
    }
});
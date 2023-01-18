jQuery(function ($) {
    'use strict';
    let $logout = $('#bookly-logout'),
        $support = $('#bookly-cloud-support'),
        $auto_recharge = $('#bookly-cloud-auto-recharge');

    // Logout button from panel.
    $logout.on('click', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            method: 'POST',
            url: ajaxurl,
            data: {
                action: 'bookly_cloud_logout',
                csrf_token: BooklyL10nGlobal.csrf_token,
            },
            dataType: 'json',
            success: function () {
                window.location = BooklyCloudPanelL10n.productsUrl;
            }
        });
    });

    $support.booklyPopover({
        html: true,
        placement: 'bottom',
        container: $support,
        template: '<div class="bookly-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
        content: function () {
            let $txt = $('<span></span>'),
                $exp = $('<small class="text-muted"></small>'),
                $hiw = $('<small></small>'),
                $btn = $('<button class="btn btn-success bookly-js-recharge-dialog-activator"></button>');
            $txt.text(BooklyCloudPanelL10n.cloud_support_text);
            $exp.text(BooklyCloudPanelL10n.cloud_support_exp_date);
            $hiw.append(
                $('<a href="https://api.booking-wp-plugin.com/go/bookly-support" target="_blank"></a>')
                    .text(BooklyCloudPanelL10n.cloud_support_hiw)
                    .append(' <i class="fas fa-external-link-alt fa-sm"></i>')
            );
            $btn.text(BooklyCloudPanelL10n.cloud_support_extend);

            let $content = $('<div class="text-center mt-2" style="min-width:200px"><br/><br/><hr style="border-top-color: rgba(0,0,0,.1)"/></div>');
            $content.find('br:first').before($txt).after($exp);
            $content.find('hr').before($hiw).after($btn);

            return $content.get(0);
        },
        trigger: 'hover'
    });

    $auto_recharge.booklyPopover({
        html: true,
        placement: 'bottom',
        container: $auto_recharge,
        template: '<div class="bookly-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
        content: function () {
            let $txt = $('<span></span>'),
                $pmt = $('<small class="text-muted"></small>'),
                $end = $('<small class="text-muted"></small>'),
                $btn = $('<button class="btn btn-success bookly-js-recharge-dialog-activator"></button>');
            $txt.text(BooklyCloudPanelL10n.auto_recharge_text);
            $pmt.text(BooklyCloudPanelL10n.auto_recharge_payment_method);
            $end.text(BooklyCloudPanelL10n.auto_recharge_end_date);
            $btn.text(BooklyCloudPanelL10n.auto_recharge_button);

            let $content = $('<div class="text-center mt-2" style="min-width:200px"><br/><br/><hr style="border-top-color: rgba(0,0,0,.1)"/></div>');
            $content.find('br:first').before($txt);
            $content.find('br:last').before($pmt).after($end);
            $content.find('hr').after($btn);

            return $content.get(0);
        },
        trigger: 'hover'
    });

    $('#bookly-open-account-settings ~ .dropdown-menu a.bookly-js-ladda').on('click', function () {
        let ladda = Ladda.create($('#bookly-open-account-settings').get(0));
        ladda.start();
    });
});
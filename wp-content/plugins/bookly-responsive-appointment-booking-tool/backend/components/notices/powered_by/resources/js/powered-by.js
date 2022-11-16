jQuery(function ($) {
    let $notice = $('#bookly-powered-by');
    $notice.on('close.bs.alert', function () {
        $.post(ajaxurl, {action: $notice.data('action'), csrf_token: BooklyL10nGlobal.csrf_token});
    }).on('click', '#bookly-show-powered-by', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        $.post(ajaxurl, {action: 'bookly_enable_show_powered_by', csrf_token: BooklyL10nGlobal.csrf_token}, function (response) {
            $notice.alert('close');
        });
    });
});
jQuery(function ($) {
    let $notice = $('#bookly-collect-stats-notice');
    $notice.on('close.bs.alert', function () {
        $.post(ajaxurl, {action: $notice.data('action'), csrf_token : BooklyL10nGlobal.csrf_token});
    });
    $notice.find('#bookly-enable-collecting-stats-btn').on('click', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        $.post(ajaxurl, {action: 'bookly_enable_collecting_stats', csrf_token: BooklyL10nGlobal.csrf_token}, function (response) {
            $notice.alert('close');
        });
    });
});
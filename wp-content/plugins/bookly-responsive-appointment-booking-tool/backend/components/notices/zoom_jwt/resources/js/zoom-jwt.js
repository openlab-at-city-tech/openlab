jQuery(function ($) {
    let $notice = $('#bookly-zoom-jwt-notice');
    $notice.on('close.bs.alert', function () {
        $.post(ajaxurl, {action: $notice.data('action'), csrf_token: BooklyL10nGlobal.csrf_token});
    });
    $notice.find('[data-action="settings"]').on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            url = $(this).attr('href');
        ladda.start();
        $.post(ajaxurl, {action: $notice.data('action'), csrf_token: BooklyL10nGlobal.csrf_token}, function (response) {
            $notice.alert('close');
            window.location.replace(url);
        });
    });
});
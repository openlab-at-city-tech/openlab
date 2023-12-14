jQuery(function($){
    $('.ngg_admin_notice .dismiss').on('click',function(e){
        var $button = $(this);
        var code = $button.data('dismiss-code');
        if (!code) code = 1;
        if ($button.attr('href') == '#') e.preventDefault();

        var $notice = $(this).parents('.ngg_admin_notice');
        var $notice_name = $notice.attr('data-notification-name');
        if ($notice_name.length > 0) {
            let url = ngg_notification_dismiss_settings.url;
            url += '&name=' + $notice_name;
            url += '&code=' + code;
            url += '&nonce=' + ngg_notification_dismiss_settings.nonce;
            $.post(url, function(response){
                if (typeof(response) != 'object') response = JSON.parse(response);
                if (response.success) {
                    $notice.fadeOut();
                }
                else alert(response.msg);
            });
        }
    });
});
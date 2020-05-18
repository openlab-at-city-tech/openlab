jQuery(function($){
    $('.ngg_admin_notice .dismiss').click(function(e){
        var $button = $(this);
        var code = $button.data('dismiss-code');
        if (!code) code = 1;
        if ($button.attr('href') == '#') e.preventDefault();

        var $notice = $(this).parents('.ngg_admin_notice');
        var $notice_name = $notice.attr('data-notification-name');
        if ($notice_name.length > 0) {
            var url = ngg_dismiss_url+'&name='+$notice_name+'&code='+code;
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
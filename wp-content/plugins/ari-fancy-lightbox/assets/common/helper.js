;(function($) {
    if (typeof(AppHelper) == 'undefined')
        return ;

    $(document).ready(function() {
        if ( typeof(window['ARI_APP']) == 'undefined' )
            return ;

        var globalAppConfig = window['ARI_APP'];
        AppHelper.options = AppHelper.options = $.extend(true, AppHelper.options, globalAppConfig['options'] || {});
        AppHelper.createApp('ari_fancybox_plugin', globalAppConfig['app']);
    });
})(jQuery);
jQuery(function($) {

    var callback = function() {
        var selector = nextgen_lightbox_filter_selector($, $([]));
        selector.addClass('shutterset');
        window.shutterSettings = {
            imageCount: true,
            msgLoading: nextgen_shutter_i18n.msgLoading,
            msgClose: nextgen_shutter_i18n.msgClose
        };
        shutterReloaded.init();
    };


    $(document).on('refreshed', callback);

    var flag = 'shutter';

    if (typeof($(window).data(flag)) == 'undefined') {
        $(window).data(flag, true);
    } else {
        return;
    }

    callback();
});

jQuery(function($){
	var callback = function(){
		var selector = nextgen_lightbox_filter_selector($, $([]));
		selector.addClass('shutterset');
        if (typeof nextgen_shutter2_i18n != 'undefined') {
            shutterReloaded.L10n = nextgen_shutter2_i18n;
        }
        shutterReloaded.Init();
    };
    $(this).on('refreshed', callback);

    var flag = 'shutterReloaded';
    if (typeof($(window).data(flag)) == 'undefined')
        $(window).data(flag, true);
    else return;

    callback();
});

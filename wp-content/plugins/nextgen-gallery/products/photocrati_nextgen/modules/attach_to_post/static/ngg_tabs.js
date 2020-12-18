jQuery(function($) {

	// Creates a Firefox-friendly wrapper around jQuery Tabs
	$.fn.ngg_tabs = function(options) {

		// Create jQuery tabs
		this.tabs(options);

		// Change from display:none to visbibility:hidden
		var i = 0;
		this.find('.main_menu_tab').each(function() {
			if (i === 0) {
				$.fn.ngg_tabs.show_tab(this, options.onShowTab);
			} else {
				$.fn.ngg_tabs.hide_tab(this, options.onHideTab);
            }
			i++;
		});

		// When the selected tab changes, then we need to re-adjust
		this.on('tabsactivate', function(event, ui) {
			// Ensure that all tabs are still displayed, but hidden ;)
			$.fn.ngg_tabs.hide_tab($.fn.ngg_tabs.get_tab_by_li(ui.oldTab), options.onHideTab);
			$.fn.ngg_tabs.show_tab($.fn.ngg_tabs.get_tab_by_li(ui.newTab), options.onShowTab);
		});
	};

	$.fn.ngg_tabs.hide_tab = function(tab, cb) {
		setTimeout(function() {
            $(tab).css({
				display: 'block',
                'z-index': -10,
                visibility:	'hidden',
				opacity: 0
            }).addClass('ngg-tab-inactive').removeClass('ngg-tab-active').trigger('tab-hidden');

            if (cb) cb(tab)
		}, 0);
	};

	$.fn.ngg_tabs.show_tab = function(tab, cb){
		tab = $(tab);

        setTimeout(function() {
        	var iframe = tab.find('iframe')[0];
            if (typeof iframe !== 'undefined'
			&&  typeof iframe.contentWindow !== 'undefined') {
                adjust_height_for_frame(top, iframe.contentWindow);
			}
        }, 50);

        setTimeout(function() {
            tab.css({
                'z-index': 1,
				visibility: 'visible',
				opacity: 1
            }).addClass('ngg-tab-active').removeClass('ngg-tab-inactive').trigger('tab-visible');

            if (cb) cb(tab)
		}, 50);

        if (cb) cb(tab)
	};

	$.fn.ngg_tabs.get_tab_by_li = function(list_item) {
		return list_item.parents('div')
			            .find('.main_menu_tab[aria-labelledby="' + list_item.attr('aria-labelledby') + '"]');
	}

});
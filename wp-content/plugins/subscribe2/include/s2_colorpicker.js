// version 1.0 - original version
jQuery(document).ready(function () {
	var version = jQuery.fn.jquery.split('.');
	if (parseFloat(version[1]) < 7) {
		// use .live as we are on jQuery prior to 1.7
		jQuery('.colorpickerField').live('click', function () {
			if (jQuery(this).attr('id').search("__i__") === -1) {
				var picker,
					field = jQuery(this).attr('id').substr(0, 20);
				jQuery('.s2_colorpicker').hide();
				jQuery('.s2_colorpicker').each(function () {
					if (jQuery(this).attr('id').search(field) !== -1) {
						picker = jQuery(this).attr('id');
					}
				});
				jQuery.farbtastic('#' + picker).linkTo(this);
				jQuery('#' + picker).slideDown();
			}
		});
	} else {
		// use .on as we are using jQuery 1.7 and up where .live is deprecated
		jQuery(document).on('focus', '.colorpickerField', function () {
			if (jQuery(this).is('.s2_initialised') || this.id.search('__i__') !== -1) {
				return; // exit early, already initialized or not activated
			}
			jQuery(this).addClass('s2_initialised');
			var picker,
				field = jQuery(this).attr('id').substr(0, 20);
			jQuery('.s2_colorpicker').each(function () {
				if (jQuery(this).attr('id').search(field) !== -1) {
					picker = jQuery(this).attr('id');
					return false; // stop looping
				}
			});
			jQuery(this).on('focusin', function (event) {
				jQuery('.s2_colorpicker').hide();
				jQuery.farbtastic('#' + picker).linkTo(this);
				jQuery('#' + picker).slideDown();
			});
			jQuery(this).on('focusout', function (event) {
				jQuery('#' + picker).slideUp();
			});
			jQuery(this).trigger('focus'); // retrigger focus event for plugin to work
		});
	}
});

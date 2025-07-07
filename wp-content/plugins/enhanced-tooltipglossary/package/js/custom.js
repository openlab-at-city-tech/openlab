jQuery(document).ready(function($) {
    $('a:has(.cmseparator)').replaceWith(function() {
        return $(this).contents();
    });
	$('.cmtt_show_hide_pro_options').trigger('click');
});
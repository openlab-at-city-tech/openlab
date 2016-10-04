 
	
	/* Menu */
	jQuery(document).ready(function() {
	if( jQuery(window).width() > 767) {
	   jQuery('.nav li.dropdown').hover(function() {
		   jQuery(this).addClass('open');
	   }, function() {
		   jQuery(this).removeClass('open');
	   }); 
	   jQuery('.nav li.dropdown-menu').hover(function() {
		   jQuery(this).addClass('open');
	   }, function() {
		   jQuery(this).removeClass('open');
	   }); 
	}
	
	jQuery('.nav li.dropdown').find('.caret').each(function(){
		jQuery(this).on('click', function(){
			if( jQuery(window).width() < 768) {
				jQuery(this).parent().next().slideToggle();
			}
			return false;
		});
	});
	/* Menu Tab */
	jQuery("li").on('click', function () {
    jQuery(".p_front").addClass("hidden");
    jQuery("." + jQuery(this).attr("id")).removeClass("hidden");
});
});

/*about theme page menu active */
jQuery(document).ready(function() {
	var active_menu;
	jQuery('.theme-menu').click(function(){
		active_menu=jQuery(this).attr('id');
		jQuery('.theme-menu').removeClass('active');
		jQuery('.theme-menu#'+active_menu).addClass('active');
	});
});
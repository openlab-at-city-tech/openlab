(function($){
	var $mainNav;

	var maybeStackMenus = function() {
		// Let the theme's native stacking take place.
		if ( window.innerWidth < 769 ) {
			return;
		}

		/*
		if ( $mainNav.height() <= 80 ) {
			$('body').removeClass( 'stacked-nav-header' );
		} else {
			$('body').addClass( 'stacked-nav-header' );
		}
		*/
	}

	$(document).ready(function(){
		$mainNav = $('.header-widget-area nav.nav-header ul.menu');
		maybeStackMenus();

		$(window).resize(maybeStackMenus);
	});
}(jQuery))

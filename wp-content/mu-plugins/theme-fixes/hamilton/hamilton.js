(function($){
	var $header,
	  $siteNav,
		resizeTimer;

	$(window).ready(function(){
		$header = $('.site-header');
		$siteNav = $('.site-nav');

		var $menusWithChildren = $('.menu-item-has-children');
		var counter = 0;
		$menusWithChildren.each(function(){
			counter++;

			var $theMenu = $(this);
			var subMenuId = 'sub-menu-' + counter;
			var toggleId = 'menu-toggle-' + counter;

			$theMenu.find('.sub-menu').attr( 'id', subMenuId ).attr('aria-labelledby', toggleId);
			$theMenu.children('.sub-menu').before('<button class="menu-toggle" id="' + toggleId + '" aria-controls="' + subMenuId + ' aria-expanded="false""><span class="screen-reader-text">Toggle Submenu</span></button>');
			$theMenu.find('.menu-toggle').click(function(){
				toggleMenu($theMenu);
			});

			toggleMenu($theMenu);
		});

		$menusWithChildren.click(function(e){
			e.stopPropagation();

			var $el = $(e.target);
			toggleMenu($el);
		});
	});

	$(window).on('load resize orientationchange', function(){
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(
			function() {
				var headerBottom = $header.position().top + $header.outerHeight(true);
				var newPadding = headerBottom + 40;

				// On mobile, adjust for fixed space above header.
				mq = window.matchMedia('(max-width: 768px)');
				if ( mq.matches ) {
					newPadding -= 60;
				}

				$siteNav.css('padding-top', newPadding + 'px');
			},
		250);
	});

	var toggleMenu = function( $menuItem ) {
		if ( $menuItem.hasClass( 'menu-collapsed' ) ) {
			$menuItem.removeClass( 'menu-collapsed' );
			$menuItem.find( '.sub-menu' ).attr( 'aria-hidden', 'false' ).attr( 'aria-expanded', 'true' );

		} else {
			$menuItem.addClass( 'menu-collapsed' );
			$menuItem.find( '.sub-menu' ).attr( 'aria-hidden', 'true' ).attr( 'aria-expanded', 'false' );
		}
	}
}(jQuery));

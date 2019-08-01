(function($){
	var $sidebar, $footer;
	$(document).ready(function(){
		$sidebar = $('#site-header');
		$footer = $('#openlab-footer');

		var $menusWithChildren = $('.menu-item-has-children');
		var counter = 0;
		$menusWithChildren.each(function(){
			counter++;

			var $theMenu = $(this);
			var subMenuId = 'sub-menu-' + counter;
			var toggleId = 'menu-toggle-' + counter;

			$theMenu.find('.sub-menu').attr( 'id', subMenuId ).attr('aria-labelledby', toggleId);
			$theMenu.append('<button class="menu-toggle" id="' + toggleId + '" aria-controls="' + subMenuId + ' aria-expanded="false""><span class="screen-reader-text">Toggle Submenu</span></button>');
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

		var resizeTimer;
		$(window).on('load resize orientationchange', function(){
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(
				function() {
					adjustSidebar();
				},
			250);
		});

		// a11y adjustments.
		$('img[aria-hidden="true"]').attr('role', 'presentation').attr('alt','');

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

  var adjustSidebar = function() {
		mq = window.matchMedia('(max-width: 1000px)');
		if ( mq.matches ) {
			$sidebar.css('bottom', 'auto');
		} else {
			var footerHeight = $footer.height();

			// Ensure the footer appears below the sidebar.
			$sidebar.css('bottom', footerHeight);
		}
	}
}(jQuery));

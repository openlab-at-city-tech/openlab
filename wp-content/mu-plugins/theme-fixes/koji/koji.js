(function($){
	$(document).ready(function(){
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

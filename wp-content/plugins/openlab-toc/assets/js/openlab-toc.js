jQuery(document).ready( function( $ ) {
	var sticky = $('.ez-toc-widget-container');

	if ( ! sticky.length ) {
		return;
	}

	var hiddenByDefault = !! ezTOC.visibility_hide_by_default;
	if ( ! hiddenByDefault ) {
		sticky.addClass( 'ez-toc-expanded' );
	}

	var	stickyTop = sticky.offset().top,
		hasAdminBar = $('body').hasClass('admin-bar'),
		scrollTop,
		$window = $(window);

	stickyTop = hasAdminBar ? stickyTop - 32 : stickyTop;

	function handleScroll( event ) {
		scrollTop = $window.scrollTop();

		if ( scrollTop >= stickyTop ) {
			sticky.addClass( 'toc-fixed' );
		} else if ( scrollTop < stickyTop ) {
			sticky.removeClass( 'toc-fixed' );
		}
	}

	$window.on( 'scroll', handleScroll );

	$( 'a.ez-toc-toggle' ).click( function( event ) {
		event.preventDefault();

		$(this)
			.closest('.ez-toc-widget-container')
			.toggleClass( 'ez-toc-expanded' );
	} );
} );

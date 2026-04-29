/**
 * FAQs shortcode
 */
jQuery(document).ready(function($) {

	// Mode 'Accordion' - collapse articles by default, expand/collapse article on click event
	// click triggered only if data-faqs-type is 'faqs'
	$( document ).on( 'click keydown', '.epkb-faqs-accordion-mode .epkb-faqs__item__question[data-faq-type="faqs"]', function ( e ) {

		// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
		if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
			return;
		}

		// Prevent default for Space key to avoid page scroll
		if ( e.type === 'keydown' && e.keyCode === 32 ) {
			e.preventDefault();
		}

		// Get current article container
		const container = $( this ).closest( '.epkb-faqs__item-container' ).eq( 0 );
		let isExpanding = !container.hasClass( 'epkb-faqs__item-container--active' );

		// Hide the current article
		if ( container.hasClass( 'epkb-faqs__item-container--active' ) ) {
			container.find( '.epkb-faqs__item__answer' ).stop().slideUp( 400 );
			container.removeClass( 'epkb-faqs__item-container--active' );
			$( this ).attr( 'aria-expanded', 'false' );
			return;
		}

		// Show the current article
		container.find( '.epkb-faqs__item__answer' ).stop().slideDown( 400 );
		container.addClass( 'epkb-faqs__item-container--active' );

		// Update aria-expanded attribute (WCAG Accessibility)
		$( this ).attr( 'aria-expanded', 'true' );
	} );

	// Mode 'Toggle' - show only one article at the same time (collapse previous article before show new article)
	$( document ).on( 'click keydown', '.epkb-faqs-toggle-mode .epkb-faqs__item__question[data-faq-type="faqs"]', function ( e ) {

		// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
		if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
			return;
		}

		// Prevent default for Space key to avoid page scroll
		if ( e.type === 'keydown' && e.keyCode === 32 ) {
			e.preventDefault();
		}

		// Get current article container
		const container = $( this ).closest( '.epkb-faqs__item-container' ).eq( 0 );
		let isExpanding = !container.hasClass( 'epkb-faqs__item-container--active' );

		// Collapse other opened articles and update their aria-expanded
		$( '.epkb-faqs__item-container--active' ).not( container ).removeClass( 'epkb-faqs__item-container--active' )
			.find( '.epkb-faqs__item__answer' ).stop().slideUp( 400 );
		$( '.epkb-faqs__item-container--active' ).not( container ).find( '.epkb-faqs__item__question' ).attr( 'aria-expanded', 'false' );

		// Show the current article
		if ( container.hasClass( 'epkb-faqs__item-container--active' ) ) {
			container.find( '.epkb-faqs__item__answer' ).stop().slideUp( 400 );
			container.removeClass( 'epkb-faqs__item-container--active' );
			$( this ).attr( 'aria-expanded', 'false' );
			return;
		}

		// Hide the current article
		container.find( '.epkb-faqs__item__answer' ).stop().slideDown( 400 );
		container.addClass( 'epkb-faqs__item-container--active' );

		// Update aria-expanded attribute (WCAG Accessibility)
		$( this ).attr( 'aria-expanded', 'true' );
	} );
});

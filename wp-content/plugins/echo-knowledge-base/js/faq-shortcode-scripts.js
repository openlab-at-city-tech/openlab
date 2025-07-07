/**
 * FAQs shortcode
 */
jQuery(document).ready(function($) {

	// Mode 'Accordion' - collapse articles by default, expand/collapse article on click event
	// click triggered only if data-faqs-type is 'faqs'
	$( document ).on( 'click', '.epkb-faqs-accordion-mode .epkb-faqs__item__question[data-faq-type="faqs"]', function () {

		// Get current article container
		const container = $( this ).closest( '.epkb-faqs__item-container' ).eq( 0 );

		// Hide the current article
		if ( container.hasClass( 'epkb-faqs__item-container--active' ) ) {
			container.find( '.epkb-faqs__item__answer' ).stop().slideUp( 400 );
			container.removeClass( 'epkb-faqs__item-container--active' );
			return;
		}

		// Show the current article
		container.find( '.epkb-faqs__item__answer' ).stop().slideDown( 400 );
		container.addClass( 'epkb-faqs__item-container--active' );
	} );

	// Mode 'Toggle' - show only one article at the same time (collapse previous article before show new article)
	$( document ).on( 'click', '.epkb-faqs-toggle-mode .epkb-faqs__item__question[data-faq-type="faqs"]', function () {

		// Get current article container
		const container = $( this ).closest( '.epkb-faqs__item-container' ).eq( 0 );

		// Collapse other opened articles
		$( '.epkb-faqs__item-container--active' ).not( container ).removeClass( 'epkb-faqs__item-container--active' )
			.find( '.epkb-faqs__item__answer' ).stop().slideUp( 400 );

		// Show the current article
		if ( container.hasClass( 'epkb-faqs__item-container--active' ) ) {
			container.find( '.epkb-faqs__item__answer' ).stop().slideUp( 400 );
			container.removeClass( 'epkb-faqs__item-container--active' );
			return;
		}

		// Hide the current article
		container.find( '.epkb-faqs__item__answer' ).stop().slideDown( 400 );
		container.addClass( 'epkb-faqs__item-container--active' );
	} );
});
/**
 * Main scripts file for the About Page
 */

/* global tiAboutPageObject */
/* global console */

jQuery( document ).ready(
	function () {
        jQuery( '#about-tabs' ).tabs().show();
        /**
		 *  With a small height tab scroll is not working on WordPress menu
		 *  That's why a windows resize is needed
		 */
        jQuery(window).resize();
		handleLinkingInTabs();

		/* Show required actions next to page title and tab title */
		if ( tiAboutPageObject.nr_actions_required > 0 ) {
			jQuery( '#about-tabs ul li > .recommended_actions' ).append( '<span class="badge-action-count">' + tiAboutPageObject.nr_actions_required + '</span>' );
		}

		jQuery( '.ti-about-page-required-action-button' ).click( function () {

			var plugin_slug = jQuery( this ).attr( 'data-slug' );

			var card = jQuery( '.' + plugin_slug );

			jQuery.ajax(
				{
					type: 'POST',
					data: { action: 'update_recommended_plugins_visibility', slug: plugin_slug, nonce: tiAboutPageObject.nonce },
					url: tiAboutPageObject.ajaxurl,
					beforeSend: function() {
						jQuery(card).fadeOut();

					},
					success: function ( response ) {
						console.log(response.required_actions);
						if( response.required_actions === 0 ) {
							jQuery('#about-tabs #recommended_actions, [data-tab-id="recommended_actions"], #adminmenu .wp-submenu li a span.badge-action-count').fadeOut().remove();
							jQuery( '#about-tabs ul > li:first-child a' ).click();
						}
						jQuery(card).remove();
						jQuery( '#about-tabs ul li > .recommended_actions span, #adminmenu .wp-submenu li a span.badge-action-count' ).text( response.required_actions );
					},
					error: function ( jqXHR, textStatus, errorThrown ) {
						jQuery(card).fadeIn();
						console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
					}
				}
			);
		} );

		// Remove activate button and replace with activation in progress button.
		jQuery( document ).on(
			'DOMNodeInserted', '.activate-now', function () {
				var activateButton = jQuery( this );
				if ( activateButton.length ) {
					var url = jQuery( activateButton ).attr( 'href' );
					if ( typeof url !== 'undefined' ) {
						// Request plugin activation.
						jQuery.ajax(
							{
								beforeSend: function () {
									jQuery( activateButton ).replaceWith( '<a class="button updating-message">' + tiAboutPageObject.activating_string + '...</a>' );
								},
								async: true,
								type: 'GET',
								url: url,
								success: function () {
									// Reload the page.
									location.reload();
								}
							}
						);
					}
				}
			}
		);
	}
);

function handleLinkingInTabs() {
	jQuery( '#about-tabs > div a[href^=\'#\']' ).on( 'click', function () {
		var index = jQuery( this ).attr( 'href' ).substr( 1 );
		jQuery( 'li[data-tab-id="' + index + '"] > a' ).click();
		return false;
	} );
}


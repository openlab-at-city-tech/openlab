/**======================================================
 * WORDPRESS ADMINISTRATION SIDEBAR METABOX JS
 * ======================================================
 * 
 * This file is used to enable the metabox toggle 
 * functionality in the admin area.
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 *
 * Licensed under the Apache License, Version 2.0 
 * (the "License") you may not use this file except in 
 * compliance with the License. You may obtain a copy 
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in 
 * writing, software distributed under the License is 
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES 
 * OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing 
 * permissions and limitations under the License.
 *
 * PLEASE NOTE: The following dependancies are required
 * in order for this file to run correctly:
 *
 * 1. jQuery	( http://jquery.com/ )
 * 2. jQueryUI	( http://jqueryui.com/ )
 * 3. sidebarL10n js object to be enqueued on the page
 *
 * ======================================================= */
( function( $ ){

	$( document ).ready( function () {

		// Expand/Collapse on click
		$( '.accordion-container' ).on( 'click keydown', '.accordion-section-title', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) // "return" key
					return;
			e.preventDefault(); // Keep this AFTER the key filter above

			accordionSwitch( $( this ) );
		});

		// Re-initialize accordion when screen options are toggled
		$( '.hide-postbox-tog' ).click( function () {
			accordionInit();
		});

	});

	var accordionOptions = $( '.accordion-container li.accordion-section' ),
		sectionContent   = $( '.accordion-section-content' );

	function accordionInit () {
		// Rounded corners
		accordionOptions.removeClass( 'top bottom' );
		accordionOptions.filter( ':visible' ).first().addClass( 'top' );
		accordionOptions.filter( ':visible' ).last().addClass( 'bottom' ).find( sectionContent ).addClass( 'bottom' );
	}

	function accordionSwitch ( el ) {
		var section  = el.closest( '.accordion-section' );
		var siblings = section.closest( '.accordion-container' ).find( '.open' );
		var content  = section.find( sectionContent );

		if ( section.hasClass( 'cannot-expand' ) )
			return;

		if ( section.hasClass( 'open' ) ) {
			section.toggleClass( 'open' );
			section.find( '.accordion-section-content' ).toggle( true ).slideToggle( 150 );

		} else {
			siblings.removeClass( 'open' );
			siblings.find( '.accordion-section-content' ).show().slideUp( 150 );
			section.find( '.accordion-section-content' ).toggle( false ).slideToggle( 150 );
			section.toggleClass( 'open' );
		}

		accordionInit();
	}

	// Initialize the accordion (currently just corner fixes)
	accordionInit();
})(jQuery);

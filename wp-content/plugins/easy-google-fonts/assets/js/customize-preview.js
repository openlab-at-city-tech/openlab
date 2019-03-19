/** =================================================
 *  Customizer Controls
 *  =================================================
 * 
 * This file contains all custom jQuery plugins and 
 * code used on the WordPress Customizer screen. It 
 * contains all of the js code necessary to enable 
 * the live previewer. Big performance enhancement 
 * in this version, this file has been completely 
 * rewritten from the ground up to leverage the new 
 * js customizer api.
 *
 * PLEASE NOTE: The following jQuery plugin 
 * dependancies are required in order for 
 * this file to run correctly:
 *
 * 1. jQuery			( http://jquery.com/ )
 * 2. jQuery UI			( http://jqueryui.com/ )
 * 3. Underscore JS
 * 4. Backbone
 *
 * @since 1.3.4
 * @version 1.4.4
 *
 * @todo - Leverage backbone templates to load 
 *     <style> elements into the <head> instead 
 *     of generating it on the fly.
 * 
 */
;( function( api, $, window, document, undefined ) {

	// Current document object.
	var preview = this;

	// Cache <head> object.
	var head = $( 'head' );

	/**
	 * Init Live Preview for Font Controls
	 * 
	 * @description - Gets all of the settings that 
	 *     have a font control, checks if the setting 
	 *     has live preview enabled and sets up the 
	 *     live previewer if the setting supports it.
	 *
	 * @uses object egfFontPreviewControls  
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.init = function() {
		// console.log(egfFontPreviewControls);
		// console.log('------------------------');
		_.each( egfFontPreviewControls, function( value, id ) {

			// console.log( "This is the id " + id );

			// Get all of the properties for this setting.
			var type       = value["type"];
			var transport  = value.setting.transport;
			var valueObj   = value;
			var importance = value.force_styles ? '!important' : '';
			var selector   = value.selector;

			// Initialize the live preview.
			if ( 'font' === type && 'postMessage' === transport ) {
				api.bind( 'preview-ready', function() {
					
					// Style preview elements.
					preview.enqueueStylesheet( id );
					preview.setFontFamily( id, selector, importance );
					preview.setFontWeight( id, selector, importance );
					preview.setFontStyle( id, selector, importance );
					preview.setTextDecoration( id, selector, importance );
					preview.setTextTransform( id, selector, importance );

					// Appearance preview elements.
					preview.setFontColor( id, selector, importance );
					preview.setBackgroundColor( id, selector, importance );
					preview.setFontSize( id, selector, importance );
					preview.setLineHeight( id, selector, importance );
					preview.setLetterSpacing( id, selector, importance );

					// Positioning preview elements.
					preview.setMargin( id, selector, importance );
					preview.setPadding( id, selector, importance );
					preview.setDisplay( id, selector, importance );
					preview.setBorder( id, selector, importance );
					preview.setBorderRadius( id, selector, importance );
				});
			}
		});
	};

	/**
	 * Set Style Property
	 *
	 * @description - Utility function designed to set
	 *     any css property and its value (without units)
	 *     and inject the style into the <head> of the
	 *     page.
	 * 
	 * @param {string} 	id 			ID key, used to fetch this font control's properties.
	 * @param {string} 	setting 	Setting ID.
	 * @param {string} 	styleId 	Unique id for style tag.
	 * @param {string} 	selector 	Selector managed by this font control.
	 * @param {string} 	property 	CSS property to change.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 * @param {boolean} withUnits 	Whether this CSS property value has units.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setStyle = function( id, setting, styleId, selector, property, importance, withUnits ) {

		// Check if this property has units.
		withUnits = typeof withUnits !== "undefined" ? withUnits : false;

		// Bind value change in the previewer.
		api.preview.bind( setting, function( to ) {
			if ( to === '' || typeof to === "undefined" ) {
				$( '#' + styleId ).remove();
			} else {

				// Build inline style.
				var style  = '<style id="' + styleId + '" type="text/css">';

				style += preview.getOpeningMediaQuery( id );

				if ( withUnits ) {
					style += selector +' { ' + property + ': ' + to.amount + to.unit + importance + '; }';
				} else {
					style += selector +' { ' + property + ': ' + to + importance + '; }';
				}

				style += preview.getClosingMediaQuery( id );
				style += '</style>';

				// Update previewer.
				if ( $( '#' + styleId ).length !== 0 ) {
					$( '#' + styleId ).replaceWith( style );
				} else {
					$( style ).appendTo( head );	
				}
			}
		});
	};

	/**
	 * Get Opening Media Query Markup
	 *
	 * @description - Returns the opening media
	 *     query markup or an empty string if 
	 *     this font control has no media query 
	 *     settings. 
	 * 
	 * @param {string} 	id     Control ID.
	 *
	 * @since 1.4.0
	 * @version 1.4.4
	 * 
	 */
	preview.getOpeningMediaQuery = function( id ) {
		var output = '';
		
		if ( typeof egfFontPreviewControls[ id ] !== "undefined" ) {

			// Get the min and max properties for 
			// this font control.
			var minScreen = egfFontPreviewControls[ id ].egf_properties.min_screen;
			var maxScreen = egfFontPreviewControls[ id ].egf_properties.max_screen;

			// Return the output if this option
			// has no min and max value.
			if ( "" === minScreen.amount && "" === maxScreen.amount ) {
				return output;
			}

			// Build the output.
			output += "@media ";
			
			// Append min-width value if applicable.
			if ( "" !== minScreen.amount ) {
				output += "(min-width: " + minScreen.amount + minScreen.unit + ")";
			}

			// Append 'and' keyword if min and max value exists.
			if ( "" !== minScreen.amount && "" !== maxScreen.amount ) {
				output += " and ";
			}

			// Append max-width value if applicable.
			if ( "" !== maxScreen.amount ) {
				output += "(max-width: " + maxScreen.amount + maxScreen.unit + ")";
			}

			output += " {\n\t";
		}

		return output;
	};

	/**
	 * Get Closing Media Query Markup
	 *
	 * @description - Returns the closing { or an
	 *     empty string if this font control has
	 *     no media query settings. 
	 * 
	 * @param {string} 	id     Control ID.
	 *
	 * @since 1.4.0
	 * @version 1.4.4
	 * 
	 */
	preview.getClosingMediaQuery = function( id ) {
		if ( preview.getOpeningMediaQuery( id ) !== "" ) {
			return "\n}\n";
		} else {
			return "";
		}
	};

	/**
	 * Enqueue Font Stylesheet into <head>
	 *
	 * @description - Takes the font control object and 
	 *     injects the appropriate stylesheet in the <head>.
	 *     Used to load the appropriate google fonts css
	 *     stylesheet in the previewer.
	 * 
	 * 
	 * @param {string} 	id     Control ID.
	 * @param {obj} 	obj    Object containing all of the current settings.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.enqueueStylesheet = function( id ) {
		var setting = 'tt_font_theme_options[' + id + '][stylesheet_obj]';

		api.preview.bind( setting, function( to ) {
			
			// Attempt to fetch the stylsheet if it is present.
			var stylesheet = $( 'link[href="' + to.url + '"]' );
			
			// Enqueue the stylesheet if it wasn't found.
			if ( '' !== to.url && typeof to.url !== "undefined" && 0 === stylesheet.length ) {
				$( '<link href="' + to.url + '&subset=' + to.subset + '" type="text/css" media="all" rel="stylesheet">' ).appendTo( head );
			}
		});
	};

	/**
	 * Set Font Family
	 *
	 * @description - Sets the font family css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setFontFamily = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][font_name]',
			'tt-font-' + id + '-font-family',
			selector,
			'font-family',
			importance
		);
	};

	/**
	 * Set Font Weight
	 *
	 * @description - Sets the font weight css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setFontWeight = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][font_weight]',
			'tt-font-' + id + '-font-weight',
			selector,
			'font-weight',
			importance
		);
	};

	/**
	 * Set Font Style
	 *
	 * @description - Sets the font style css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setFontStyle = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][font_style]',
			'tt-font-' + id + '-font-style',
			selector,
			'font-style',
			importance
		);
	};

	/**
	 * Set Text Decoration
	 *
	 * @description - Sets the text decoration css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setTextDecoration = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][text_decoration]',
			'tt-font-' + id + '-text-decoration',
			selector,
			'text-decoration',
			importance
		);
	};

	/**
	 * Set Text Transform
	 *
	 * @description - Sets the text transform css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setTextTransform = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][text_transform]',
			'tt-font-' + id + '-text-transform',
			selector,
			'text-transform',
			importance
		);
	};

	/**
	 * Set Font Color
	 *
	 * @description - Sets the font color css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setFontColor = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][font_color]',
			'tt-font-' + id + '-color',
			selector,
			'color',
			importance
		);
	};

	/**
	 * Set Background Color
	 *
	 * @description - Sets the background color css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setBackgroundColor = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][background_color]',
			'tt-font-' + id + '-background-color',
			selector,
			'background-color',
			importance
		);
	};

	/**
	 * Set Font Size
	 *
	 * @description - Sets the font-size css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setFontSize = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][font_size]',
			'tt-font-' + id + '-font-size',
			selector,
			'font-size',
			importance,
			true
		);
	};

	/**
	 * Set Line Height
	 *
	 * @description - Sets the line-height css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setLineHeight = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][line_height]',
			'tt-font-' + id + '-line-height',
			selector,
			'line-height',
			importance
		);
	};

	/**
	 * Set Letter Spacing
	 *
	 * @description - Sets the letter-spacing css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setLetterSpacing = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][letter_spacing]',
			'tt-font-' + id + '-letter-spacing',
			selector,
			'letter-spacing',
			importance,
			true
		);
	};

	/**
	 * Set Margin
	 *
	 * @description - Sets the margin-{position} css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setMargin = function( id, selector, importance ) {
		
		// Define the different positions.
		var positions = [ 'top', 'bottom', 'left', 'right' ];

		// Set up the margin preview for each position.
		_.each( positions, function( position ) {
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][margin_' + position + ']',
				'tt-font-' + id + '-margin-' + position,
				selector,
				'margin-' + position,
				importance,
				true
			);
		});
	};

	/**
	 * Set Padding
	 *
	 * @description - Sets the display css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setPadding = function( id, selector, importance ) {

		// Define the different positions.
		var positions = [ 'top', 'bottom', 'left', 'right' ];

		// Set up the padding preview for each position.
		_.each( positions, function( position ) {
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][padding_' + position + ']',
				'tt-font-' + id + '-padding-' + position,
				selector,
				'padding-' + position,
				importance,
				true
			);
		});
	};

	/**
	 * Set Border
	 *
	 * @description - Sets all of the border css properties for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setBorder = function( id, selector, importance ) {
		// Define the different positions.
		var positions = [ 'top', 'bottom', 'left', 'right' ];

		// Set up the border preview for each position.
		_.each( positions, function( position ) {

			// Set border color.
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][border_' + position + '_color]',
				'tt-font-' + id + '-border-' + position + '-color',
				selector,
				'border-' + position + '-color',
				importance
			);

			// Set border style.
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][border_' + position + '_style]',
				'tt-font-' + id + '-border-' + position + '-style',
				selector,
				'border-' + position + '-style',
				importance
			);

			// Set border width.
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][border_' + position + '_width]',
				'tt-font-' + id + '-border-' + position + '-width',
				selector,
				'border-' + position + '-width',
				importance,
				true
			);
		});
	};

	/**
	 * Set Border Radius
	 *
	 * @description - Sets the border-radius css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setBorderRadius = function( id, selector, importance ) {
		// Define the different positions.
		var positions = [ 'top', 'bottom' ];

		_.each( positions, function( position ) {
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][border_radius_' + position + '_left]',
				'tt-font-' + id + '-border-' + position + '-left-radius',
				selector,
				'border-' + position +'-left-radius',
				importance,
				true
			);
			preview.setStyle(
				id,
				'tt_font_theme_options[' + id + '][border_radius_' + position + '_right]',
				'tt-font-' + id + '-border-' + position + '-right-radius',
				selector,
				'border-' + position +'-right-radius',
				importance,
				true
			);
		});
	};

	/**
	 * Set Display
	 *
	 * @description - Sets the display css property for
	 *     the selectors passed in the parameter and injects
	 *     the styles into the <head> of the page.
	 * 
	 * @param {string} 	id         	Control ID.
	 * @param {string} 	selector   	Selector managed by this font control.
	 * @param {string} 	importance 	Whether to force styles using !important.
	 *
	 * @since 1.3.4
	 * @version 1.4.4
	 * 
	 */
	preview.setDisplay = function( id, selector, importance ) {
		preview.setStyle(
			id,
			'tt_font_theme_options[' + id + '][display]',
			'tt-font-' + id + '-display',
			selector,
			'display',
			importance
		);
	};

	// Initialize live preview.
	preview.init();
	
}( wp.customize, jQuery, window, document ) );

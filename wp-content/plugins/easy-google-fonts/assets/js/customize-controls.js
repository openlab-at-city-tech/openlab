/**===============================================================
 * Customizer Controls
 * ===============================================================
 * 
 * This file contains all custom jQuery plugins and code used on 
 * the WordPress Customizer screen. It contains all of the js
 * code necessary to enable the custom controls used in the live
 * previewer. Big performance enhancement in this version, this
 * file has been completely rewritten from the ground up.
 *
 * PLEASE NOTE: The following jQuery plugin dependancies are 
 * required in order for this file to run correctly:
 *
 * 1. jQuery			( http://jquery.com/ )
 * 2. jQuery UI			( http://jqueryui.com/ )
 * 3. Underscore JS
 * 4. Backbone
 *
 * @since 1.3.4
 * @version 1.4.2
 *
 * Note: this.renderContent(); is used to rerender the control
 *
 */

/**
 * Font Subset Query Caching
 * =========================
 *
 * By querying the list of google fonts once and
 * storing it into the global window context we
 * can benefit from a huge performance increase
 * and we can also reference the query from the
 * template files.
 * 
 * @since 1.3.4
 * @version 1.4.2
 * 
 */
;( function( api, $, window, document, undefined ) { "use strict"
	
	// Bail if customizer object isn't in the DOM.
	if ( ! wp || ! wp.customize ) { 
		return; 
	}

	/**
	 * Get Font by Subset
	 * 
	 * Helper function to get fonts with a
	 * particular subset. This function is 
	 * referenced within template files.
	 *
	 * @param  {string} subset        - Subset to retrieve.
	 * @param  {object} fonts         - JSON object containing fonts.
	 * @return {object} matchingFonts - JSON object containing fonts with subset.
	 * 
	 * @since 1.3.4
	 * @version 1.4.2
	 *
	 */
	window.egfGetFontsBySubset = function ( subset, fonts ) {
		
		// Return fonts if all subsets are selected.
		if ( 'all' === subset || 'latin,all' === subset ) {
			return fonts;
		}

		// Find matching fonts.
		var matchingFonts = {};
		
		_.each( fonts, function( font, id ) {
			if ( _.contains( font.subsets, subset ) ) {
				matchingFonts[ id ] = font;
			}
		});

		// Return matching fonts.
		return matchingFonts;
	};

	/**
	 * Cache Google Font Subsets Lookup Query
	 *
	 * @description  - By looping through the fonts 
	 *     object once and caching the subsets we
	 *     increase performance in the customizer.
	 * 
	 * @since 1.3.4
	 * @version 1.4.2
	 * 
	 */
	var standard     = {},
		serif        = {},
		sansSerif    = {},
		display      = {},
		handwriting  = {},
		monospace    = {};

	// Filter fonts by subset.
	var subsets = _.filter( egfAllFonts, function( value, key ) {
		if ( "default" === value.font_type ) {
			standard[ key ] = value;
		} else {
			switch( value.category ) {
				case "serif":
					serif[ key ] = value;
					break;

				case "sans-serif":
					sansSerif[ key ] = value;
					break;

				case "display":
					display[ key ] = value;
					break;

				case "handwriting":
					handwriting[ key ] = value;
					break;

				case "monospace":
					monospace[ key ] = value;
					break;
			}
		}
	});

	/**
	 * Add the results as a json object in the
	 * global window context so that we can
	 * reference it in our control template.
	 * 
	 */
	window.egfAllFontsBySubset         = {};
	egfAllFontsBySubset["standard"]    = standard;
	egfAllFontsBySubset["serif"]       = serif;
	egfAllFontsBySubset["sansSerif"]   = sansSerif;
	egfAllFontsBySubset["display"]     = display;
	egfAllFontsBySubset["handwriting"] = handwriting;
	egfAllFontsBySubset["monospace"]   = monospace;

}( wp.customize, jQuery, window, document ) );	

/**
 * EGF Font Control Plugin
 * =======================
 *
 * Extends the wp.customize.Control class and
 * defines a custom control to use in the
 * customizer that is completely client side
 * driven. This plugin now utilises the new
 * customizer js api.
 * 
 * @since 1.3.4
 * @version 1.4.2
 * 
 */
;( function( api, $, window, document, undefined ) { "use strict"

	// Bail if customizer object isn't in the DOM.
	if ( ! wp || ! wp.customize ) { 
		return; 
	}

	/**
	 * An EGF Font Control
	 *
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 *
	 * @since 1.3.4
	 * @version 1.4.2
	 * 
	 */
	api.EGFFontControl = api.Control.extend({});
	_.extend( api.EGFFontControl.prototype, {

		/**
		 * Font Control Setup
		 * 
		 * @description - Triggered when the control's 
		 *     markup has been injected into the DOM.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		ready: function() {
			var control = this;
			
			// Shortcut so that we don't have to use 
			// _.bind every time we add a callback.
			_.bindAll( 
				control, 
				'changeProperty',
				'toggleProperties',
				'selectTabPane',
				'setupFontSearch',
				'focusFontFamilyInput',
				'reset',
				'initControls',
				'initFontChangeListeners',
				'setSubset',
				'setFontFamily',
				'setFontWeight',
				'setFontWeightField',
				'setTextDecoration',
				'setTextTransform',
				'setFontColor',
				'setBackgroundColor',
				'createSliderControl',
				'createColorControl',
				'setupFontSizeSlider',
				'setupLineHeightSlider',
				'setupLetterSpacingSlider',
				'setupMarginSliders',
				'setupPaddingSliders',
				'setDisplay',
				'setupBorderControls',
				'setupBorderVisibility',
				'setupBorderRadiusSliders'
			);

			// Inititialize the font control functionality.
			control.container.on( 'click', '.egf-font-toggle-trigger', control.toggleProperties );
			control.container.on( 'click', '.egf-customizer-tabs li', control.selectTab );
			control.container.on( 'click', '.egf-customizer-tabs li', control.selectTabPane );
			control.container.on( 'click', '.egf-font-toggle .toggle-section-title', control.togglePositioningAccordion );

			// Initialise controls (lazy loaded for performance).
			control.container.one( 'click', '.egf-font-toggle-trigger', control.initControls );
			control.container.one( 'click', '.egf-font-toggle-trigger', control.initFontChangeListeners );

			// Main reset event.
			control.container.on( 'click', '.egf-reset-font', control.reset );

			// Bind reset events to all of the controls 
			// within this control.
			control.container.on( 'click', '.egf-reset-font', control.initControls );

			// Border control <select> listeners
			var positions = [ 'top', 'bottom', 'left', 'right' ];

			_.each( positions, function( position ) {
				control.container.on(
					'keyup change',
					'.egf-border-' + position + '-controls .egf-border-style',
					function() {
						var property    = {};
						var borderStyle = control.container.find( '.egf-border-' + position + '-controls .egf-border-style' ).val();

						// Get property.
						property[ 'border_' + position + '_style' ] = borderStyle;

						// Set property.
						control.changeProperty( property );
					}
				);
			});
		},

		/**
		 * Reset Font Control
		 *
		 * @description - Resets this font control to it's
		 *     default values.
		 *     
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		reset: function() {
			this.setting.set( this.params.egf_defaults );
			this.changeProperty( this.params.egf_defaults );
			this.renderContent();
		},

		/**
		 * Initialise Controls
		 * 
		 * @description - Sets up the sliders, color
		 *     pickers and chosen js script required
		 *     for this font control. Wrapped in a 
		 *     function so that it can be lazy 
		 *     loaded for performance.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		initControls: function() {
			var control = this;
			control.setupFontSearch();
			control.setFontColor();
			control.setBackgroundColor();
			control.setupFontSizeSlider();
			control.setupLineHeightSlider();
			control.setupLetterSpacingSlider();
			control.setupMarginSliders();
			control.setupPaddingSliders();
			control.setupBorderControls();
			control.setupBorderRadiusSliders();
		},

		/**
		 * Initialise Font Change Listeners
		 * 
		 * @description - Sets up the listeners required
		 *     to change the fonts in realtime. Wrapped
		 *     in a function so that it can be lazy
		 *     loaded for performance.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		initFontChangeListeners: function() {
			var control = this;
			// Bind control listeners to all of the
			// controls within this control.
			control.container.on( 'keyup change', '.egf-font-subsets', control.setSubset );
			control.container.on( 'keyup change', '.egf-font-subsets', control.setFontFamily );
			control.container.on( 'keyup change', '.egf-font-subsets', control.focusFontFamilyInput );
			control.container.on( 'keyup change', '.egf-font-subsets', control.initControls );
			control.container.on( 'keyup change', '.egf-font-family', control.setFontFamily );
			control.container.on( 'keyup change', '.egf-font-family', control.setFontWeight );
			control.container.on( 'keyup change', '.egf-font-family', control.focusFontFamilyInput );
			control.container.on( 'keyup change', '.egf-font-family', control.initControls );
			
			control.container.on( 'keyup change', '.egf-font-weight', control.setFontWeight );
			control.container.on( 'keyup change', '.egf-text-decoration', control.setTextDecoration );
			control.container.on( 'keyup change', '.egf-text-transform', control.setTextTransform );
			control.container.on( 'keyup change', '.egf-font-display-element', control.setDisplay );
			control.container.on( 'keyup change', '.egf-switch-border-control', control.setupBorderVisibility );
		},

		/**
		 * Change Single Property
		 *
		 * @description - Changes a single property for 
		 *     this font control by parsing the new 
		 *     values with the current settings.
		 * 
		 * @param  {object} propertyObj - Object with properties and values to change.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		changeProperty: function( propertyObj ) {
			var control = this;
			var id      = control["id"];

			// Send the updated property to the previewer.
			_.each( propertyObj, function( value, key ) {
				api.previewer.send( 'tt_font_theme_options[' + id + '][' + key + ']', value );
			});
			
			// Set the property.
			this.setting.set( _.defaults( propertyObj, this.setting() ) );
		},

		/**
		 * Toggle Properties
		 *
		 * @description - Display/Hide the properties for
		 *     this font control when the toggle trigger 
		 *     is clicked.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		toggleProperties: function() {
			this.container.toggleClass( 'egf-active' );
		},

		/**
		 * Initialize Tab Selection
		 * 
		 * @param  {object} e - Event object.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		selectTab: function(e) {
			$(this).addClass( 'selected' );
			$(this).siblings().removeClass( 'selected' );
		},

		/**
		 * Select Tab Pane
		 *
		 * @description - Switches the tab pane content
		 *     depending on the tab selected.
		 * 
		 * @param  {object} e - Event object.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		selectTabPane: function(e) {
			// Get selected tab pane id.
			var id = this.container.find( '[data-customize-tab].selected' ).data( 'customize-tab' );
			
			// Show/hide the appropriate tab panes.
			this.container.find( "[data-customize-tab-pane]" ).removeClass( 'selected' );
			this.container.find( "[data-customize-tab-pane=" + id + "]" ).addClass( 'selected' );
		},

		/**
		 * Setup Font Search
		 *
		 * @description - Initialises the chosen js script
		 *     to allow a more user friendly way of 
		 *     searching the font list.
		 * 
		 * @param  {object} e - Event object.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupFontSearch: function(e) {
			this.container.find( '.egf-font-family' ).chosen({
				width: "100%",
				search_contains : true
			});
		},

		/**
		 * Focus Font Search Input
		 *
		 * @description - Sets the focus back on the font 
		 *     family input when the controls are refreshed
		 *     to improve usability.
		 *     
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		focusFontFamilyInput: function() {
			this.container.find( '.egf-font-family' ).trigger("chosen:open");
			this.container.find( '.egf-font-family' ).trigger("chosen:activate");
		},

		/**
		 * Set Subset
		 *
		 * @description - Sets the subset value for this
		 *     font control based on the users selection.
		 * 
		 * @param  {object} e - Event object.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setSubset: function(e) {
			var subset = this.container.find( '.egf-font-subsets' ).val();
			this.changeProperty({ subset: subset });
			this.renderContent();
			this.setupFontSearch();
		},
		
		/**
		 * Set Font Family
		 *
		 * @description - Sets the font family and the initial
		 *     font weight for this font control (to the first
		 *     available font weight) based on the users 
		 *     selection. Sets the following properties for
		 *     this font control:
		 *         - font_id
		 *         - font_name
		 *         - font_style
		 *         - font_weight
		 *         - font_weight_style
		 *         - stylesheet_url
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setFontFamily: function() {
			// Get the id for the selected font.
			var fontId = this.container.find( '.egf-font-family' ).val();
			var subset = this.container.find( '.egf-font-subsets' ).val();

			// Check for theme defaults.
			if ( "" === fontId || "undefined" === typeof( egfAllFonts[ fontId ]["name"] ) ) {
				
				// Revert to defaults.
				this.changeProperty({
					font_id 			: this.params.egf_defaults.font_id,
					font_name 			: this.params.egf_defaults.font_name,
					font_style 			: this.params.egf_defaults.font_style,
					font_weight 		: this.params.egf_defaults.font_weight,
					font_weight_style 	: this.params.egf_defaults.font_weight_style,
					stylesheet_url 		: stylesheetUrl,
					stylesheet_obj 		: { 
						'url' : this.params.egf_defaults.stylesheet_url, 
						'subset' : this.params.egf_defaults.subset 
					}				
				});

				// Rerender the font weight control.
				this.setFontWeightField();

				// Exit function.
				return;
			}

			// Get font family properties.
			var fontName        = egfAllFonts[ fontId ]["name"];
			var fontWeightStyle = egfAllFonts[ fontId ]["font_weights"][0];
			var weight          = parseInt( fontWeightStyle, 10 );
			var style           = 'normal';
			var stylesheetUrl   = egfAllFonts[ fontId ]["urls"][ fontWeightStyle ];

			// Set default font weight if weight is NaN
			if ( ( ! weight ) || fontWeightStyle.indexOf( 'regular' ) !== -1 ) {
				weight = 400;
			}

			// Set font style attribute if it is italic
			if ( 'italic' === fontWeightStyle || fontWeightStyle.indexOf( 'italic' ) !== -1 ) {
				style = 'italic';
			}

			// Change the setting
			this.changeProperty({
				font_id 			: fontId,
				font_name 			: fontName,
				font_style 			: style,
				font_weight 		: weight,
				font_weight_style 	: fontWeightStyle,
				stylesheet_url 		: stylesheetUrl,
				stylesheet_obj 		: { 
					'url' : stylesheetUrl, 
					'subset' : subset 
				}
			});

			// Rerender the font weight control.
			this.setFontWeightField();
		},

		/**
		 * Set Font Weight
		 *
		 * @description - Sets the font weight and style 
		 *     for this font control based on the users 
		 *     selection. Sets the following properties 
		 *     for this font control:
		 *         - font_style
		 *         - font_weight
		 *         - font_weight_style
		 *         - stylesheet_url
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setFontWeight: function() {
			// Get selected font weight style.
			var fontWeightStyle = this.container.find( '.egf-font-weight' ).val();
			var subset          = this.container.find( '.egf-font-subsets' ).val();

			// Check for theme defaults.
			if ( "" === fontWeightStyle ) {
				
				// Revert to defaults.
				this.changeProperty({
					font_style 			: this.params.egf_defaults.font_style,
					font_weight 		: this.params.egf_defaults.font_weight,
					font_weight_style 	: this.params.egf_defaults.font_weight_style,
					stylesheet_url 		: stylesheetUrl,
					stylesheet_obj 		: { 
						'url' : this.params.egf_defaults.stylesheet_url, 
						'subset' : this.params.egf_defaults.subset 
					}
				});

				// Exit function.				
				return;
			}
			
			// Get font family properties.
			var settings      = this.setting();
			var fontId        = settings.font_id;
			var weight        = parseInt( fontWeightStyle, 10 );
			var style         = 'normal';
			var stylesheetUrl = egfAllFonts[ fontId ]["urls"][ fontWeightStyle ];

			// Set default font weight if weight is NaN.
			if ( ( ! weight ) || fontWeightStyle.indexOf( 'regular' ) !== -1 ) {
				weight = 400;
			}

			// Set font style attribute if it is italic.
			if ( 'italic' === fontWeightStyle || fontWeightStyle.indexOf( 'italic' ) !== -1 ) {
				style = 'italic';
			}

			// Change the setting.
			this.changeProperty({
				font_style 			: style,
				font_weight 		: weight,
				font_weight_style 	: fontWeightStyle,
				stylesheet_url 		: stylesheetUrl,
				stylesheet_obj 		: { 
					'url' : stylesheetUrl, 
					'subset' : subset 
				}
			});

			// Refocus input.
			this.container.find( '.egf-font-weight' ).focus();
		},

		/**
		 * Get Font Weights
		 *
		 * @description - Sets the <option> values for the
		 *     font weight <select> field. This function 
		 *     should only be called once the font family
		 *     json object is set.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setFontWeightField: function() {
			var settings = this.setting();
			var fontId   = settings.font_id;
			var control  = this.container.find( '.egf-font-weight' );
			var output   = '';
			
			if ( typeof egfAllFonts[ fontId ] === "undefined" ) {
				output += '<option value="">' + egfTranslation.themeDefault + '</option>';
			} else {
				_.each( egfAllFonts[ fontId ]['font_weights'], function( value ) {
					output += '<option value="' + value + '">' + value + '</option>'
				});				
			}

			// Build the new control output.
			control.empty().append( output );
		},

		/**
		 * Set Text Decoration
		 *
		 * @description - Sets the text decoration for this
		 *     font control based on the users selection.
		 * 
		 * @param  {object} e - Event object.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setTextDecoration: function(e) {
			var textDecoration = this.container.find( '.egf-text-decoration' ).val();
			this.changeProperty({ text_decoration: textDecoration });
			this.container.find( '.egf-text-decoration' ).focus();
		},

		/**
		 * Set Text Decoration
		 *
		 * @description - Sets the text decoration for this
		 *     font control based on the users selection.
		 * 
		 * @param  {object} e - Event object.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setTextTransform: function(e) {
			var textTransform = this.container.find( '.egf-text-transform' ).val();
			this.changeProperty({ text_transform: textTransform });
			this.container.find( '.egf-text-transform' ).focus();
		},

		/**
		 * Set Font Color
		 *
		 * @description - Initialises an iris color picker 
		 *     and sets the font color for this font control
		 *     based on the users selection.
		 *     
		 * @param {object} e - Event object.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setFontColor: function(e) {
			this.createColorControl( 
				'.egf-font-color-container',
				'font_color'
			);
		},

		/**
		 * Set Background Color
		 *
		 * @description - Initialises an iris color picker 
		 *     and sets the background color for this font 
		 *     control based on the users selection.
		 *     
		 * @param {object} e - Event object.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setBackgroundColor: function(e) {
			this.createColorControl( 
				'.egf-background-color-container',
				'background_color'
			);
		},

		/**
		 * Create Slider Control
		 *
		 * @description - Utility function used to create
		 *     a new automattic color picker iris control.
		 *     
		 * @param  {string} classname CSS class selector.
		 * @param  {string} property  Setting property key to update.
		 * @param  {number} width     Color picker width (default 250)
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		createColorControl: function( classname, property, width ) {

			width = width || 255;

			var control     = this;
			var propertyObj = {};
			var picker      = control.container.find( classname + ' .egf-color-picker-hex' );

			var callback = function() {
				var propertyObj = {};
				propertyObj[ property ] = picker.wpColorPicker( 'color' );

				// Update setting.
				control.changeProperty( propertyObj );
			};

			var resetSetting = function() {
				var propertyObj = {};
				propertyObj[ property ] = false;

				// Update setting.
				control.changeProperty( propertyObj );
			};

			picker.wpColorPicker({
				width  : width,
				change : callback,
				clear  : resetSetting
			});
		},

		/**
		 * Create Slider Control
		 *
		 * @description - Utility function used to create
		 *     a new jquery ui slider control. Handles 
		 *     settings with and without units.
		 *     
		 * @param  {string} classname CSS class selector.
		 * @param  {string} property  Setting property key to update.
		 * @param  {string} prefix    Property prefix.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		createSliderControl: function( classname, property, prefix ) {
			
			prefix = prefix || property;

			var control    = this;
			var slider     = this.container.find( classname + ' .egf-slider' );
			var display    = this.container.find( classname + ' .egf-font-slider-display span' );
			var reset      = this.container.find( classname + ' .egf-font-slider-reset' );
			var settings   = this.setting();
			var properties = this.params.egf_properties;
			var min        = this.params.egf_properties[ prefix + '_min_range' ];
			var max        = this.params.egf_properties[ prefix + '_max_range' ];
			var step       = this.params.egf_properties[ prefix + '_step' ];
			var value      = _.isObject( settings[ property ] ) ? settings[ property ].amount : settings[ property ];

			// Init defaults.
			value = value || 0;
			min   = min   || 0;
			max   = max   || 100;
			step  = step  || 1;

			// Default callback function.
			var callback = function( event, ui ) {
				var text                = ui.value;
				var propertyObj         = {};
				propertyObj[ property ] = ui.value;

				// Update callback functions if units have 
				// been passed in the parameter.
				if ( _.isObject( control.params.egf_defaults[ property ] ) ) {
					propertyObj[ property ] = {
						amount: ui.value,
						unit: control.params.egf_defaults[ property ].unit
					};

					text += control.params.egf_defaults[ property ].unit;
				}

				// Update display.
				display.text( text );
				
				// Update setting.
				control.changeProperty( propertyObj );
			};

			// Default reset function
			var resetSetting = function() {
				var text                = control.params.egf_defaults[ property ];
				var value               = control.params.egf_defaults[ property ];
				var propertyObj         = {};
				propertyObj[ property ] = control.params.egf_defaults[ property ];

				if ( _.isObject( control.params.egf_defaults[ property ] ) ) {

					// Update value.
					value = control.params.egf_defaults[ property ].amount;

					// Update property.
					propertyObj[ property ] = {
						amount: control.params.egf_defaults[ property ].amount,
						unit: control.params.egf_defaults[ property ].unit
					};

					// Update display text.
					text = control.params.egf_defaults[ property ].amount + control.params.egf_defaults[ property ].unit;
				}

				// Update visual control.
				slider.slider({ value : value });
				
				// Update display.
				display.text( text );
				
				// Update setting.
				control.changeProperty( propertyObj );
			};

			// Initialize slider.
			slider.slider({
				min: min,
				max: max,
				value: value,
				slide: callback,
				step: step
			});

			// Reset listener.
			reset.on( 'click', function() {
				resetSetting();
			});
		},

		/**
		 * Setup Font Slider
		 * 
		 * @description - Initialises a new jquery ui
		 *     slider to set the font size for this font 
		 *     control based on the users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupFontSizeSlider: function() {
			this.createSliderControl(
				'.egf-font-size-slider',
				'font_size'
			);
		},

		/**
		 * Setup Line Height Slider
		 * 
		 * @description - Initialises a new jquery ui
		 *     slider to set the line height for this 
		 *     font control based on the users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupLineHeightSlider: function() {
			this.createSliderControl(
				'.egf-line-height-slider',
				'line_height'
			);
		},

		/**
		 * Setup Letter Spacing Slider
		 * 
		 * @description - Initialises a new jquery ui
		 *     slider to set the letter spacing for this 
		 *     font control based on the users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupLetterSpacingSlider: function() {
			this.createSliderControl(
				'.egf-letter-spacing-slider',
				'letter_spacing'
			);
		},

		/**
		 * Setup Margin Sliders
		 * 
		 * @description - Initialises a new jquery ui
		 *     slider to set the margin for this font 
		 *     control based on the users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupMarginSliders: function() {
			var control   = this;
			var positions = [ 'top', 'bottom', 'left', 'right' ];

			_.each( positions, function( position ) {
				control.createSliderControl(
					'.egf-margin-' + position + '-slider',
					'margin_' + position,
					'margin'
				);				
			});
		},

		/**
		 * Setup Padding Sliders
		 * 
		 * @description - Initialises a new jquery ui
		 *     slider to set the padding for this font 
		 *     control based on the users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupPaddingSliders: function() {
			var control   = this;
			var positions = [ 'top', 'bottom', 'left', 'right' ];

			_.each( positions, function( position ) {
				control.createSliderControl(
					'.egf-padding-' + position + '-slider',
					'padding_' + position,
					'padding'
				);				
			});
		},

		/**
		 * Setup Padding Sliders
		 * 
		 * @description - Initialises the accordion 
		 *     functionality in the positioning tab.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		togglePositioningAccordion: function() {
			$(this).parent().toggleClass( 'selected' );
			$(this).parent().siblings().removeClass( 'selected' );
		},

		/**
		 * Setup Display
		 * 
		 * @description - Sets the display for this font 
		 *     control based on the users selection.
		 *     
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setDisplay: function() {
			var display = this.container.find( '.egf-font-display-element' ).val();
			this.changeProperty({ display: display });
			this.container.find( '.egf-font-display-element' ).focus();
		},

		/**
		 * Setup Padding Sliders
		 * 
		 * @description - Initialises the color picker
		 *     and jquery ui slider in order to control
		 *     the border color and width based on the
		 *     users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupBorderControls: function() {
			var control   = this;
			var positions = [ 'top', 'bottom', 'left', 'right' ];

			_.each( positions, function( position ) {

				// Create border color control.
				control.createColorControl(
					'.egf-border-' + position + '-controls',
					'border_' + position + '_color',
					230
				);

				// Create border width slider control.
				control.createSliderControl(
					'.egf-border-' + position + '-controls',
					'border_' + position + '_width',
					230
				);

			});
		},

		/**
		 * Select Border Control Visibility
		 *
		 * @description - Switches the border control 
		 *     depending on the option selected.
		 * 
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupBorderVisibility: function() {
			var control   = this;
			var position  = control.container.find( '.egf-switch-border-control' ).val();
			$( '.egf-border-' + position + '-controls' ).addClass( 'selected' ).siblings().removeClass( 'selected' );
		},

		/**
		 * Setup Border Radius Sliders
		 * 
		 * @description - Initialises a new jquery ui
		 *     slider to set the border radius for this 
		 *     font control based on the users selection.
		 *
		 * @since 1.3.4
		 * @version 1.4.2
		 * 
		 */
		setupBorderRadiusSliders: function() {
			var control   = this;
			var positions = [ 'top', 'bottom' ];

			_.each( positions, function( position ) {
				control.createSliderControl(
					'.egf-border-radius-' + position + '-left-slider',
					'border_radius_' + position + '_left',
					'border_radius'
				);
				control.createSliderControl(
					'.egf-border-radius-' + position + '-right-slider',
					'border_radius_' + position + '_right',
					'border_radius'
				);				
			});
		}
	});
	
	console.log( api.EGFFontControl );

	/**
	 * Register Control constructor.
	 * 
	 * @description - Registers our custom control with 
	 *     the wp.customize.controlConstructor JSON
	 *     object.
	 *
	 * @since 1.3.4
	 * @version 1.4.2
	 * 
	 */
	api.controlConstructor.egf_font = api.EGFFontControl;
}( wp.customize, jQuery, window, document ) );

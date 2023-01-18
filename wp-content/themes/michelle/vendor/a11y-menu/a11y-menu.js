/*!
@package      A11y Menu
@description  A keyboard accessible navigational menu script.
@version      1.2.0
@author       WebMan Design, Oliver Juhas, https://www.webmandesign.eu
@copyright    2019 WebMan Design, Oliver Juhas
@license      GPL-3.0-or-later, https://www.gnu.org/licenses/gpl-3.0-standalone.html
@link         https://github.com/webmandesign/a11y-menu

@global  window, document, a11yMenuConfig
*/

( function( options ) {

	const a11yMenu = {

		// Initialization

			/**
			 * Initializes the functionality on DOM load.
			 *
			 * @param  {Object} options={}  Configuration options.
			 *
			 * @return  {Void}
			 */
			init: function( options = {} ) {
				const _ = this;

				// Set options first.
				_.setOptions( options );

				// Load required polyfills first (already used in getMenus()).
				_.polyfill();

				// No point if there is no menu to process or we have no class to apply.
				if ( ! _.getMenus().length || ! _.getOption( 'expanded_class' ) ) {
					return;
				}

				// Iterate over each menu.
				_.getMenus().forEach( ( menu ) => {

					// Get child menus.
					const childMenus = menu.querySelectorAll( _.getOption( 'child_menu_selector' ) );

					// No point if there is no child menu in the menu.
					if ( ! Object.keys( childMenus ).length ) {
						return;
					}

					// Get child menu toggle button from configured attributes object.
					const button = _.getButton( _.getOption( 'button_attributes' ) );

					// Iterate over each child menu in the menu.
					childMenus.forEach( ( childMenu ) => {

						// Get the parent menu item.
						const menuItem = childMenu.parentNode;

						// Indicate we have a child menu within the menu item.
						menuItem.classList.add( 'a11y-menu--has-children' );

						// Prepend child menu with toggle button. The button mode check is done in getButton().
						if ( null != button ) {
							const childButton = button.cloneNode( true );
							menuItem.insertBefore( childButton, childMenu );

							_.changeButtonAttributes( menuItem );

							// Watch for `click` event on the toggle button.
							// (Can't actually use `click` as it triggers focus/blur first, messing things up.)
							childButton.addEventListener( 'mousedown', ( event ) => _.onClickButton( event ) );
							childButton.addEventListener( 'keyup',     ( event ) => _.onClickButton( event ) );
						}

						// Watch for touch event on a link within.
						if ( _.isMode( 'touch' ) ) {
							const link = menuItem.querySelector( 'a[href]' );
							if ( null != link ) {
								link.addEventListener( 'touchstart', ( event ) => _.onTouchLink( event ), true );
							}
						}

					} );

					// Watch for focus events within the menu, but don't bubble up.
					if ( _.isMode( 'tab' ) ) {
						menu.addEventListener( 'focusin',  ( event ) => _.onFocus( event ), true );
						menu.addEventListener( 'focusout', ( event ) => _.onFocus( event ), true );
					}

					// Watch for keydown event (checking for ESC key).
					if ( _.isMode( 'esc' ) ) {
						document.addEventListener( 'keyup', ( event ) => _.onKeyESC( event ) );
					}

				} );
			},

		// Events

			/**
			 * Action on focus/blur event within the menu.
			 *
			 * @param  {Event} event
			 *
			 * @return  {Void}
			 */
			onFocus: function( event ) {
				const
					_ = this,
					parents = _.getParents( event );

				if ( 'focusin' === event.type ) {
					parents.map( ( menuItem ) => menuItem.classList.add( _.getOption( 'expanded_class' ) ) );
				} else {
					parents.map( ( menuItem ) => menuItem.classList.remove( _.getOption( 'expanded_class' ) ) );
				}

				if ( _.isMode( 'button' ) ) {
					// Toggle button attributes for this menu item.
					_.changeButtonAttributes( event );
					// Toggle button attributes for parents.
					parents.map( ( menuItem ) => _.changeButtonAttributes( menuItem ) );
				}
			},

			/**
			 * Action on click/touch event on the toggle button.
			 *
			 * No need to check for mode as this has already been done.
			 *
			 * @param  {Event} event
			 *
			 * @return  {Void}
			 */
			onClickButton: function( event ) {
				// No point if key pressed and is not enter or spacebar.
				if ( 'keyup' === event.type && -1 === [ 13, 32 ].indexOf( event.keyCode ) ) {
					return false;
				}

				const
					_ = this,
					classExpanded = _.getOption( 'expanded_class' ),
					menuItem      = event.target.parentNode,
					isExpanded    = menuItem.classList.contains( classExpanded );

				// Let's treat siblings first.
				_.getSiblings( menuItem ).map( ( sibling ) => {
					// Remove the class from siblings.
					sibling.classList.remove( classExpanded );
					// Toggle button attributes for siblings.
					_.changeButtonAttributes( sibling )
				} );

				// Toggle the class on direct parent menu item only.
				if ( isExpanded ) {
					menuItem.classList.remove( classExpanded );
				} else {
					menuItem.classList.add( classExpanded );
				}

				// Toggle button attributes for this menu item.
				_.changeButtonAttributes( event );
				// Toggle button attributes for parents.
				_.getParents( menuItem ).map( ( parent ) => _.changeButtonAttributes( parent ) );

				// Fixing issue with focus and blur events.
				event.preventDefault();
			},

			/**
			 * Action on touch event on the link within the expandable menu item.
			 *
			 * @param  {Event} event
			 *
			 * @return  {Void}
			 */
			onTouchLink: function( event ) {
				const
					_ = this,
					link          = event.target,
					menuItem      = link.parentNode,
					classExpanded = _.getOption( 'expanded_class' );

				if ( ! menuItem.classList.contains( classExpanded ) ) {
				// Touched once, child menu is collapsed -> expanded child menu.

					event.preventDefault();
					link.focus();

					const
						siblings = _.getSiblings( menuItem ),
						parents  = _.getParents( event );

					siblings.map( ( sibling ) => sibling.classList.remove( classExpanded ) );
					parents.map( ( menuItem ) => menuItem.classList.add( classExpanded ) );

					if ( _.isMode( 'button' ) ) {
						// Toggle button attributes for this menu item.
						_.changeButtonAttributes( event );
						// Toggle button attributes for parents.
						parents.map( ( menuItem ) => _.changeButtonAttributes( menuItem ) );
					}
				} else if ( link !== document.activeElement ) {
				// Touched once, child menu is expanded -> collapse child menu.

					event.preventDefault();
					menuItem.classList.remove( classExpanded );

					if ( _.isMode( 'button' ) ) {
						// Toggle button attributes for this menu item.
						_.changeButtonAttributes( event );
					}
				}
			},

			/**
			 * Action on a keyboard key press.
			 *
			 * @param  {Event} event
			 *
			 * @return  {Void}
			 */
			onKeyESC: function( event ) {
				if ( 27 === event.keyCode ) {
					const
						_ = this,
						classExpanded = _.getOption( 'expanded_class' );

					_.getMenus().forEach( ( menu ) => {
						menu.querySelectorAll( '.' + classExpanded ).forEach( ( menuItem ) => {
							menuItem.classList.remove( classExpanded );

							if ( _.isMode( 'button' ) ) {
								// Toggle button attributes for this menu item.
								_.changeButtonAttributes( menuItem );
							}
						} );
					} );
				}
			},

		// Elements

			/**
			 * Sets and returns an array of all accessible menu containers we are processing.
			 *
			 * @return  {Object} Array of menu container nodes, or an empty array.
			 */
			getMenus: function() {
				const
					_ = this,
					selector = _.getOption( 'menu_selector' );

				if ( ! _.menus.length && selector ) {
					document.querySelectorAll( selector ).forEach( ( menu ) => _.menus.push( menu ) );
				}

				return _.menus;
			},

			/**
			 * Returns an array of sibling nodes (expandable menu items by default).
			 *
			 * @param  {node}   node                           DOM node to get siblings for.
			 * @param  {String} selector='.a11y-menu--has-children'  Filters siblings by this selector.
			 *
			 * @return  {Object} Array of nodes, or an empty array.
			 */
			getSiblings: function( node, selector = '.a11y-menu--has-children' ) {
				let
					siblings = [],
					sibling  = node.parentNode.firstChild;

				// Iterate over all siblings, but return valid nodes only.
				for (; sibling; sibling = sibling.nextElementSibling ) {
					if (
						1 !== sibling.nodeType
						|| ! sibling.matches( selector )
						|| node === sibling
					) {
						continue;
					}
					siblings.push( sibling );
				}

				return siblings;
			},

			/**
			 * Returns an array of matched parent nodes.
			 *
			 * @param  {Event/node} eventOrNode                   An event object or a DOM node to get parents for.
			 * @param  {String}     selector='a11y-menu--has-children'  Filters parents by this selector.
			 *
			 * @return  {Object} Array of (filtered) nodes, or an empty array.
			 */
			getParents: function( eventOrNode, selector = 'a11y-menu--has-children' ) {
				const
					_ = this,
					menus = _.getMenus();

				let
					parents = [],
					node    = ( 1 === eventOrNode.nodeType ) ? ( eventOrNode ) : ( eventOrNode.target );

				// We don't need parents outside the menu container.
				while ( -1 === menus.indexOf( node ) ) {
					parents.push( node );
					node = node.parentNode;
				}

				// Remove parents not matching `selector` function attribute.
				if ( selector ) {
					parents = parents.filter( ( parent ) => parent.matches( selector ) );
				}

				return parents;
			},

			/**
			 * Returns a toggle button element.
			 *
			 * @param  {Object} atts  HTML attributes the button should have.
			 *
			 * @return  {node} A button DOM node.
			 */
			getButton: function( atts ) {
				atts = atts || {};

				// No button when its attributes are empty or when no button functionality.
				const attKeys = Object.keys( atts );
				if ( ! attKeys.length || ! this.isMode( 'button' ) ) {
					return null;
				}

				// Create a button element and set allowed attributes.
				const
					button      = document.createElement( 'button' ),
					allowedAtts = [
						'class',
						'tabindex',
						'title',
					];

				// Set `aria-expanded` as it's mandatory attribute.
				button.setAttribute( 'aria-expanded', 'false' );

				// Set allowed attributes only.
				attKeys.forEach( ( name ) => {
					if (
						-1 !== allowedAtts.indexOf( name )
						|| 0 === name.indexOf( 'aria-' )
						|| 0 === name.indexOf( 'data-' )
					) {
						// The value is secured by Element.setAttribute() directly.
						button.setAttribute( name.toLowerCase(), atts[ name ] );
					}
				} );

				// Preset dynamic `aria-label` attribute.
				if ( null != atts['aria-label'] && null != atts['aria-label'].expand ) {
					button.setAttribute( 'aria-label', atts['aria-label'].expand );
				}

				return button;
			},

			/**
			 * Modifies the button HTML attributes based on the child menu expansion state.
			 *
			 * @param  {Event/node} eventOrNode  An event object or a DOM node of button parent.
			 *
			 * @return  {Void}
			 */
			changeButtonAttributes: function( eventOrNode ) {
				const
					_ = this,
					menuItem = ( 1 === eventOrNode.nodeType ) ? ( eventOrNode ) : ( eventOrNode.target.parentNode );

				let
					button,
					menuItemLabel = '';

				if ( null != menuItem ) {
					button = menuItem.querySelector( 'button[aria-expanded]' );
				}

				// Don't bother if no button.
				if ( null == button || 1 !== button.nodeType ) {
					return;
				}

				// Get menu item label.
				const link = menuItem.querySelector( 'a[data-submenu-label]' );
				if ( null != link ) {
					menuItemLabel = link.dataset.submenuLabel;
				}

				const
					isExpanded  = menuItem.classList.contains( _.getOption( 'expanded_class' ) ),
					buttonLabel = _.getOption( 'button_attributes', 'aria-label' );

				// Change `aria-label` value dynamically, if we should.
				if ( 'string' !== typeof buttonLabel && null != buttonLabel ) {
					if ( isExpanded && null != buttonLabel.collapse ) {
						button.setAttribute( 'aria-label', buttonLabel.collapse.replace( '%s', menuItemLabel ) );
					} else if ( ! isExpanded && null != buttonLabel.expand ) {
						button.setAttribute( 'aria-label', buttonLabel.expand.replace( '%s', menuItemLabel ) );
					}
				}

				// Change `aria-expanded` value.
				button.setAttribute( 'aria-expanded', isExpanded.toString() );
			},

		// Options

			/**
			 * Sets options.
			 *
			 * @param  {Object} config  Optional configuration options.
			 *
			 * @return  {Void}
			 */
			setOptions: function( config ) {
				const
					_ = this,
					options = {
					// Setting default values.
						// {Object} Object of attribute name and value pairs for created toggle button.
						'button_attributes': {
							// {String} Default button class.
							'class': 'button-toggle-sub-menu',
							// {Object/String} If object, attribute value dynamically changes. If string, value is static.
							'aria-label': {
								'collapse': 'Collapse child menu',
								'expand': 'Expand child menu',
							},
						},
						// {String} Required child menu selector.
						'child_menu_selector': '.sub-menu',
						// {String} Required sub menu toggle class.
						'expanded_class': 'has-expanded-sub-menu',
						// {String} Required navigational menu container(s) selector.
						'menu_selector': 'nav .menu',
						// {Array} Array of enabled functionality modes.
						'mode': [ 'tab', 'esc', 'button', 'touch' ],
					};

				// If we have a custom option, override the default value.
				for ( let id in config ) {
					if ( options.hasOwnProperty( id ) ) {
						// Make sure to sanitize a class name.
						if ( 'expanded_class' === id ) {
							options[ id ] = config[ id ].replace( /[^a-zA-Z0-9\-_]/g, '' );
						} else {
							options[ id ] = config[ id ];
						}
					}
				}

				// Set actual options.
				_.options = options;

				// Also preset the array of menu containers.
				_.menus = [];
			},

			/**
			 * Get an option value.
			 *
			 * A function argument stand for an option label to retrieve the value for.
			 * Multiple function arguments (labels) dive deep into options object hierarchy.
			 * No function argument (label) provided returns the whole options object.
			 *
			 * @return  {Mixed}
			 */
			getOption: function() {
				let val = this.options;
				const
					args    = arguments,
					argsLen = args.length;

				if ( 1 === argsLen ) {
					val = ( null != val[ args[0] ] ) ? ( val[ args[0] ] ) : ( false );
				} else {
					for ( let i = 0; i < argsLen && false !== val; i++ ) {
						val = ( null != val[ args[ i ] ] ) ? ( val[ args[ i ] ] ) : ( false );
					}
				}

				return val;
			},

			/**
			 * Check the enabled functionality mode.
			 *
			 * @param  {String} mode  Name of the mode to check for.
			 *
			 * @return  {Boolean}
			 */
			isMode: function( mode ) {
				if ( -1 !== this.getOption( 'mode' ).indexOf( mode ) ) {
					return true;
				} else {
					return false;
				}
			},

		// Polyfills

			/**
			 * Polyfills for NodeList.forEach() and Element.matches().
			 *
			 * @return  {Void}
			 */
			polyfill: function() {
				// @see  https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach#Polyfill
				if ( window.NodeList && ! NodeList.prototype.forEach ) {
					NodeList.prototype.forEach = Array.prototype.forEach;
				}

				// @see  https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
				if ( ! Element.prototype.matches ) {
					Element.prototype.matches = Element.prototype.msMatchesSelector ||
					                            Element.prototype.webkitMatchesSelector;
				}
			},

	};

	// We're all set, load the functionality now.
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', () => a11yMenu.init( options ) );
	} else {
		// The DOM has already been loaded.
		a11yMenu.init( options );
	}

} )( window.a11yMenuConfig || {} );

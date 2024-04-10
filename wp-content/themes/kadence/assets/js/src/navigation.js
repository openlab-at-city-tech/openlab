/* global kadenceConfig */
/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */

(function() {
	'use strict';
	window.kadence = {

		/**
		 * Function to init different style of focused element on keyboard users and mouse users.
		 */
		initOutlineToggle: function() {
			document.body.addEventListener( 'keydown', function() {
				document.body.classList.remove( 'hide-focus-outline' );
			});

			document.body.addEventListener( 'mousedown', function() {
				document.body.classList.add( 'hide-focus-outline' );
			});
		},

		/**
		 * Get element's offset.
		 */
		getOffset: function( el ) {
			if ( el instanceof HTMLElement ) {
				var rect = el.getBoundingClientRect();

				return {
					top: rect.top + window.pageYOffset,
					left: rect.left + window.pageXOffset
				}
			}

			return {
				top: null,
				left: null
			};
		},

		/**
		 * traverses the DOM up to find elements matching the query
		 *
		 * @param {HTMLElement} target
		 * @param {string} query
		 * @return {NodeList} parents matching query
		 */
		findParents: function( target, query ) {
			var parents = [];

			// recursively go up the DOM adding matches to the parents array
			function traverse( item ) {
				var parent = item.parentNode;
				if ( parent instanceof HTMLElement ) {
					if ( parent.matches( query ) ) {
						parents.push( parent );
					}
					traverse( parent );
				}
			}

			traverse( target );

			return parents;
		},
		/**
		 * Toggle an attribute.
		 */
		toggleAttribute: function( element, attribute, trueVal, falseVal ) {
			if ( trueVal === undefined ) {
				trueVal = true;
			}
			if ( falseVal === undefined ) {
				falseVal = false;
			}
			if ( element.getAttribute( attribute ) !== trueVal ) {
				element.setAttribute( attribute, trueVal );
			} else {
				element.setAttribute( attribute, falseVal );
			}
		},
		/**
		 * Initiate the script to process all
		 * navigation menus with submenu toggle enabled.
		 */
		initNavToggleSubmenus: function() {
			var navTOGGLE = document.querySelectorAll( '.nav--toggle-sub' );

			// No point if no navs.
			if ( ! navTOGGLE.length ) {
				return;
			}

			for ( let i = 0; i < navTOGGLE.length; i++ ) {
				window.kadence.initEachNavToggleSubmenu( navTOGGLE[ i ] );
				window.kadence.initEachNavToggleSubmenuInside( navTOGGLE[ i ] );
			}
		},
		initEachNavToggleSubmenu: function( nav ) {
			// Get the submenus.
			var SUBMENUS = nav.querySelectorAll( '.menu ul' );
		
			// No point if no submenus.
			if ( ! SUBMENUS.length ) {
				return;
			}
		
			for ( let i = 0; i < SUBMENUS.length; i++ ) {
				var parentMenuItem = SUBMENUS[ i ].parentNode;
				let dropdown = parentMenuItem.querySelector( '.dropdown-nav-toggle' );
				// If dropdown.
				if ( dropdown ) {
					var dropdown_label = parentMenuItem.querySelector( '.nav-drop-title-wrap' ).firstChild.textContent.trim();
					var dropdownBtn = document.createElement( 'BUTTON' ); // Create a <button> element
					dropdownBtn.setAttribute( 'aria-label', ( dropdown_label ? kadenceConfig.screenReader.expandOf + ' ' + dropdown_label : kadenceConfig.screenReader.expand ) );
					dropdownBtn.classList.add( 'dropdown-nav-special-toggle' );
					parentMenuItem.insertBefore( dropdownBtn, parentMenuItem.childNodes[1] );
					// Toggle the submenu when we click the dropdown button.
					dropdownBtn.addEventListener( 'click', function( e ) {
						e.preventDefault();
						window.kadence.toggleSubMenu( e.target.closest('li') );
					} );
		
					// Clean up the toggle if a mouse takes over from keyboard.
					parentMenuItem.addEventListener( 'mouseleave', function( e ) {
						window.kadence.toggleSubMenu( e.target, false );
					} );
		
					// When we focus on a menu link, make sure all siblings are closed.
					parentMenuItem.querySelector( 'a' ).addEventListener( 'focus', function( e ) {
						var parentMenuItemsToggled = e.target.parentNode.parentNode.querySelectorAll( 'li.menu-item--toggled-on' );
						for ( let j = 0; j < parentMenuItemsToggled.length; j++ ) {
							if ( parentMenuItem !== parentMenuItemsToggled[ j ] ) {
								window.kadence.toggleSubMenu( parentMenuItemsToggled[ j ], false );
							}
						}
					} );
		
					// Handle keyboard accessibility for traversing menu.
					SUBMENUS[ i ].addEventListener( 'keydown', function( e ) {
						// These specific selectors help us only select items that are visible.
						var focusSelector = 'ul.toggle-show > li > a, ul.toggle-show > li > .dropdown-nav-special-toggle';
		
						// 9 is tab KeyMap
						if ( 9 === e.keyCode ) {
							if ( e.shiftKey ) {
								// Means we're tabbing out of the beginning of the submenu.
								if ( window.kadence.isfirstFocusableElement (SUBMENUS[ i ], document.activeElement, focusSelector ) ) {
									window.kadence.toggleSubMenu( SUBMENUS[ i ].parentNode, false );
								}
								// Means we're tabbing out of the end of the submenu.
							} else if ( window.kadence.islastFocusableElement( SUBMENUS[ i ], document.activeElement, focusSelector ) ) {
								window.kadence.toggleSubMenu( SUBMENUS[ i ].parentNode, false );
							}
						}
						// 27 is keymap for esc key.
						if ( e.keyCode === 27 ) {
							window.kadence.toggleSubMenu( SUBMENUS[ i ].parentNode, false );
						}
					} );
		
					SUBMENUS[ i ].parentNode.classList.add( 'menu-item--has-toggle' );
				}
			}
		},
		initEachNavToggleSubmenuInside: function( nav ) {
			// Get the submenus.
			var SUBMENUPARENTS = nav.querySelectorAll( '.menu-item-has-children' );
			
			// No point if no submenus.
			if ( ! SUBMENUPARENTS.length ) {
				return;
			}
		
			for ( let i = 0; i < SUBMENUPARENTS.length; i++ ) {		
				// Handle verifying if navigation will go offscreen
				SUBMENUPARENTS[ i ].addEventListener( 'mouseenter', function( e ) {
					if ( SUBMENUPARENTS[ i ].querySelector( 'ul.sub-menu' ) ) {
						var elm = SUBMENUPARENTS[ i ].querySelector( 'ul.sub-menu' );
						var off = window.kadence.getOffset( elm );
						var l = off.left;
						var w = elm.offsetWidth;
						var docW = window.innerWidth;
			
						var isEntirelyVisible = (l + w <= docW);
			
						if ( ! isEntirelyVisible ) {
							elm.classList.add( 'sub-menu-edge' );
						}
					}
				} );
			}
		},
		/**
		 * Toggle submenus open and closed, and tell screen readers what's going on.
		 * @param {Object} parentMenuItem Parent menu element.
		 * @param {boolean} forceToggle Force the menu toggle.
		 * @return {void}
		 */
		toggleSubMenu: function( parentMenuItem, forceToggle ) {
			var toggleButton = parentMenuItem.querySelector( '.dropdown-nav-special-toggle' ),
				subMenu = parentMenuItem.querySelector( 'ul' );
			let parentMenuItemToggled = parentMenuItem.classList.contains( 'menu-item--toggled-on' );
			var dropdown_label = parentMenuItem.querySelector( '.nav-drop-title-wrap' ).firstChild.textContent.trim();
			// Will be true if we want to force the toggle on, false if force toggle close.
			if ( undefined !== forceToggle && 'boolean' === ( typeof forceToggle ) ) {
				parentMenuItemToggled = ! forceToggle;
			}
			// Toggle aria-expanded status.
			toggleButton.setAttribute( 'aria-expanded', ( ! parentMenuItemToggled ).toString() );

			/*
			* Steps to handle during toggle:
			* - Let the parent menu item know we're toggled on/off.
			* - Toggle the ARIA label to let screen readers know will expand or collapse.
			*/
			if ( parentMenuItemToggled ) {
				// Toggle "off" the submenu.
				parentMenuItem.classList.remove( 'menu-item--toggled-on' );
				subMenu.classList.remove( 'toggle-show' );
				toggleButton.setAttribute( 'aria-label', ( dropdown_label ? kadenceConfig.screenReader.expandOf + ' ' + dropdown_label : kadenceConfig.screenReader.expand ) );

				// Make sure all children are closed.
				var subMenuItemsToggled = parentMenuItem.querySelectorAll( '.menu-item--toggled-on' );
				for ( let i = 0; i < subMenuItemsToggled.length; i++ ) {
					window.kadence.toggleSubMenu( subMenuItemsToggled[ i ], false );
				}
			} else {
				// Make sure siblings are closed.
				var parentMenuItemsToggled = parentMenuItem.parentNode.querySelectorAll( 'li.menu-item--toggled-on' );
				for ( let i = 0; i < parentMenuItemsToggled.length; i++ ) {
					window.kadence.toggleSubMenu( parentMenuItemsToggled[ i ], false );
				}

				// Toggle "on" the submenu.
				parentMenuItem.classList.add( 'menu-item--toggled-on' );
				subMenu.classList.add( 'toggle-show' );
				toggleButton.setAttribute( 'aria-label', ( dropdown_label ? kadenceConfig.screenReader.collapseOf + ' ' + dropdown_label : kadenceConfig.screenReader.collapse ) );
			}
		},
		/**
		 * Returns true if element is the
		 * first focusable element in the container.
		 * @param {Object} container
		 * @param {Object} element
		 * @param {string} focusSelector
		 * @return {boolean} whether or not the element is the first focusable element in the container
		 */
		isfirstFocusableElement: function( container, element, focusSelector ) {
			var focusableElements = container.querySelectorAll( focusSelector );
			if ( 0 < focusableElements.length ) {
				return element === focusableElements[ 0 ];
			}
			return false;
		},

		/**
		 * Returns true if element is the
		 * last focusable element in the container.
		 * @param {Object} container
		 * @param {Object} element
		 * @param {string} focusSelector
		 * @return {boolean} whether or not the element is the last focusable element in the container
		 */
		islastFocusableElement: function( container, element, focusSelector ) {
			var focusableElements = container.querySelectorAll( focusSelector );
			//console.log( focusableElements );
			if ( 0 < focusableElements.length ) {
				return element === focusableElements[ focusableElements.length - 1 ];
			}
			return false;
		},
		/**
		 * Initiate the script to process all drawer toggles.
		 */
		toggleDrawer: function( element, changeFocus ) {
			changeFocus = (typeof changeFocus !== 'undefined') ? changeFocus : true;
			var toggle = element;
			var target = document.querySelector( toggle.dataset.toggleTarget );
			if ( ! target ) {
				return;
			}
			var scrollBar = window.innerWidth - document.documentElement.clientWidth;
			var duration = ( toggle.dataset.toggleDuration ? toggle.dataset.toggleDuration : 250 );
			window.kadence.toggleAttribute( toggle, 'aria-expanded', 'true', 'false' );
			if ( target.classList.contains('show-drawer') ) {
				if ( toggle.dataset.toggleBodyClass ) {
					document.body.classList.remove( toggle.dataset.toggleBodyClass );
				}
				// Hide the drawer.
				target.classList.remove('active');
				target.classList.remove('pop-animated');
				document.body.classList.remove( 'kadence-scrollbar-fixer' );
				setTimeout(function () {
					target.classList.remove('show-drawer');
					var drawerEvent = new Event('kadence-drawer-closed');
					window.dispatchEvent(drawerEvent);
					if ( toggle.dataset.setFocus && changeFocus ) {
						var focusElement = document.querySelector(toggle.dataset.setFocus);
						if ( focusElement ) {
							focusElement.focus();
							if ( focusElement.hasAttribute( 'aria-expanded') ) {
								window.kadence.toggleAttribute( focusElement, 'aria-expanded', 'true', 'false' );
							}
						}
					}
				}, duration);
			} else {
				// Show the drawer.
				target.classList.add('show-drawer');
				// Toggle body class
				if ( toggle.dataset.toggleBodyClass ) {
					document.body.classList.toggle( toggle.dataset.toggleBodyClass );
					if ( toggle.dataset.toggleBodyClass.includes( 'showing-popup-drawer-' ) ) {
						document.body.style.setProperty('--scrollbar-offset', scrollBar + 'px' );
						document.body.classList.add( 'kadence-scrollbar-fixer' );
					}
				}
				setTimeout(function () {
					target.classList.add('active');
					var drawerEvent = new Event('kadence-drawer-opened');
					window.dispatchEvent(drawerEvent);
					if ( toggle.dataset.setFocus && changeFocus ) {
						var focusElement = document.querySelector(toggle.dataset.setFocus);

						if ( focusElement ) {
							if ( focusElement.hasAttribute( 'aria-expanded') ) {
								window.kadence.toggleAttribute( focusElement, 'aria-expanded', 'true', 'false' );
							}
							var searchTerm = focusElement.value;
							focusElement.value = '';
							focusElement.focus();
							focusElement.value = searchTerm;
						}
					}
				}, 10);
				setTimeout(function () {
					target.classList.add('pop-animated');
				}, duration);
				// Keep Focus in Modal
				if ( target.classList.contains('popup-drawer') ) {
					// add all the elements inside modal which you want to make focusable
					var focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
					var focusableContent = target.querySelectorAll( focusableElements );
					var firstFocusableElement = focusableContent[0]; // get first element to be focused inside modal
					var lastFocusableElement = focusableContent[ focusableContent.length - 1 ]; // get last element to be focused inside modal

					document.addEventListener( 'keydown', function(e) {
						let isTabPressed = e.key === 'Tab' || e.keyCode === 9;

						if ( ! isTabPressed ) {
							return;
						}

						if ( e.shiftKey ) { // if shift key pressed for shift + tab combination
							if ( document.activeElement === firstFocusableElement ) {
								lastFocusableElement.focus(); // add focus for the last focusable element
								e.preventDefault();
							}
						} else { // if tab key is pressed
							if ( document.activeElement === lastFocusableElement ) { // if focused has reached to last focusable element then focus first focusable element after pressing tab
								firstFocusableElement.focus(); // add focus for the first focusable element
								e.preventDefault();
							}
						}
					});
				}
			}
		},
		/**
		 * Initiate the script to process all
		 * navigation menus with small toggle enabled.
		 */
		initToggleDrawer: function() {
			var drawerTOGGLE = document.querySelectorAll( '.drawer-toggle' );

			// No point if no drawers.
			if ( ! drawerTOGGLE.length ) {
				return;
			}
			for ( let i = 0; i < drawerTOGGLE.length; i++ ) {
				drawerTOGGLE[ i ].addEventListener('click', function( event ) {
					event.preventDefault();
					window.kadence.toggleDrawer( drawerTOGGLE[ i ] );
				} );
			}
			// Close Drawer if esc is pressed.
			document.addEventListener( 'keyup', function (event) {
				// 27 is keymap for esc key.
				if ( event.keyCode === 27 ) {
					if ( document.querySelectorAll( '.popup-drawer.show-drawer.active' ) ) {
						event.preventDefault();
						document.querySelectorAll( '.popup-drawer.show-drawer.active' ).forEach(function ( element ) {
							window.kadence.toggleDrawer( document.querySelector('*[data-toggle-target="' + element.dataset.drawerTargetString + '"]' ) );
						} );
					}
				}
			  });
			// Close modal on outside click.
			document.addEventListener( 'click', function (event) {
				var target = event.target;
				var modal = document.querySelector( '.show-drawer.active .drawer-overlay' );
				if ( target === modal ) {
					window.kadence.toggleDrawer(document.querySelector('*[data-toggle-target="' + modal.dataset.drawerTargetString + '"]'));
				}
				var searchModal = document.querySelector( '#search-drawer.show-drawer.active .drawer-content' );
				var modal = document.querySelector( '#search-drawer.show-drawer.active .drawer-overlay' );
				if ( target === searchModal ) {
					window.kadence.toggleDrawer(document.querySelector('*[data-toggle-target="' + modal.dataset.drawerTargetString + '"]'));
				}
			} );
		},
		/**
		 * Initiate the script to process all
		 * navigation menus with small toggle enabled.
		 */
		initMobileToggleSub: function() {
			var modalMenus = document.querySelectorAll( '.has-collapse-sub-nav' );

			modalMenus.forEach( function( modalMenu ) {
				var activeMenuItem = modalMenu.querySelector( '.current-menu-item' );
				if ( activeMenuItem ) {
					window.kadence.findParents( activeMenuItem, 'li' ).forEach( function( element ) {
						var subMenuToggle = element.querySelector( '.drawer-sub-toggle' );
						if ( subMenuToggle ) {
							window.kadence.toggleDrawer( subMenuToggle, true );
						}
					} );
				}
			} );
			var drawerSubTOGGLE = document.querySelectorAll( '.drawer-sub-toggle' );
			// No point if no drawers.
			if ( ! drawerSubTOGGLE.length ) {
				return;
			}
		
			for ( let i = 0; i < drawerSubTOGGLE.length; i++ ) {
				drawerSubTOGGLE[ i ].addEventListener('click', function (event) {
					event.preventDefault();
					window.kadence.toggleDrawer( drawerSubTOGGLE[ i ] );
				} );
			}
		},
		/**
		 * Initiate the script to process all
		 * navigation menus check to close mobile.
		 */
		initMobileToggleAnchor: function() {
			var mobileModal = document.getElementById( 'mobile-drawer' );
			// No point if no drawers.
			if ( ! mobileModal ) {
				return;
			}
			var menuLink = mobileModal.querySelectorAll( 'a:not(.kt-tab-title)' );
			// No point if no links.
			if ( ! menuLink.length ) {
				return;
			}
			for ( let i = 0; i < menuLink.length; i++ ) {
				menuLink[ i ].addEventListener('click', function (event) {
					window.kadence.toggleDrawer( mobileModal.querySelector( '.menu-toggle-close' ), false );
				} );
			}
		},
		/**
		 * Initiate setting the top padding for hero title when page is transparent.
		 */
		initTransHeaderPadding: function() {
			if ( document.body.classList.contains( 'no-header' ) ) {
				return;
			}
			if ( ! document.body.classList.contains( 'transparent-header' ) || ! document.body.classList.contains( 'mobile-transparent-header' ) ) {
				return;
			}
			var titleHero = document.querySelector( '.entry-hero-container-inner' ),
				header =  document.querySelector( '#masthead' );
			var updateHeroPadding = function( e ) {
				header
				if ( kadenceConfig.breakPoints.desktop <= window.innerWidth ) {
					if ( ! document.body.classList.contains( 'transparent-header' ) ) {
						titleHero.style.paddingTop = 0;
					} else {
						titleHero.style.paddingTop = header.offsetHeight + 'px';
					}
				} else {
					if ( ! document.body.classList.contains( 'mobile-transparent-header' ) ) {
						titleHero.style.paddingTop = 0;
					} else {
						titleHero.style.paddingTop = header.offsetHeight + 'px';
					}
				}
			}
			if ( titleHero ) {
				window.addEventListener( 'resize', updateHeroPadding, false );
				window.addEventListener( 'scroll', updateHeroPadding, false );
				window.addEventListener( 'load', updateHeroPadding, false );
				updateHeroPadding();
			}
		},
		/**
		 * Initiate the script to stick the header.
		 * http://www.mattmorgante.com/technology/sticky-navigation-bar-javascript
		 */
		initStickyHeader: function() {
			var desktopSticky = document.querySelector( '#main-header .kadence-sticky-header' ),
				mobileSticky  = document.querySelector( '#mobile-header .kadence-sticky-header' ),
				wrapper = document.getElementById( 'wrapper' ),
				proSticky = document.querySelectorAll( '.kadence-pro-fixed-above' ),
				proElements = document.querySelectorAll( '.kadence-before-wrapper-item' ),
				activeSize = 'mobile',
				lastScrollTop = 0,
				activeOffsetTop = 0;
				if ( parseInt( kadenceConfig.breakPoints.desktop ) < window.innerWidth ) {
					activeSize = 'desktop';
					if ( desktopSticky ) {
						desktopSticky.style.position = 'static';
						activeOffsetTop = window.kadence.getOffset( desktopSticky ).top;
						desktopSticky.style.position = null;
					}
				} else {
					if ( mobileSticky ) {
						mobileSticky.style.position = 'static';
						activeOffsetTop = window.kadence.getOffset( mobileSticky ).top;
						mobileSticky.style.position = null;
					}
				}
				var updateSticky = function( e ) {
					var activeHeader;
					var offsetTop = window.kadence.getOffset( wrapper ).top;
					if ( document.body.classList.toString().includes( 'boom_bar-static-top' ) ) {
						var boomBar = document.querySelector( '.boom_bar' );
						offsetTop = window.kadence.getOffset( wrapper ).top - boomBar.offsetHeight;
					}
					if ( proElements.length ) {
						var proElementOffset = 0;
						for ( let i = 0; i < proElements.length; i++ ) {
							proElementOffset = proElementOffset + proElements[ i ].offsetHeight;
						}
						offsetTop = window.kadence.getOffset( wrapper ).top - proElementOffset;
					}
					if ( proSticky.length ) {
						var proOffset = 0;
						for ( let i = 0; i < proSticky.length; i++ ) {
							proOffset = proOffset + proSticky[ i ].offsetHeight;
						}
						offsetTop = window.kadence.getOffset( wrapper ).top + proOffset;
					}
					if ( kadenceConfig.breakPoints.desktop <= window.innerWidth ) {
						activeHeader = desktopSticky;
					} else {
						activeHeader = mobileSticky;
					}
					if ( ! activeHeader ) {
						return;
					}
					if ( kadenceConfig.breakPoints.desktop <= window.innerWidth ) {
						if ( activeSize === 'mobile' ) {
							activeOffsetTop = window.kadence.getOffset( activeHeader ).top;
							activeSize = 'desktop';
						} else if ( e && e === 'updateActive' ) {
							activeHeader.style.top = 'auto';
							activeOffsetTop = window.kadence.getOffset( activeHeader ).top;
							activeSize = 'desktop';
						}
					} else {
						if ( activeSize === 'desktop' ) {
							activeOffsetTop = window.kadence.getOffset( activeHeader ).top;
							activeSize = 'mobile';
						} else if ( e && e === 'updateActive' ) {
							activeHeader.style.top = 'auto';
							activeOffsetTop = window.kadence.getOffset( activeHeader ).top;
							activeSize = 'mobile';
						}
					}
					var parent = activeHeader.parentNode;
					var shrink = activeHeader.getAttribute( 'data-shrink' );
					var revealScroll = activeHeader.getAttribute( 'data-reveal-scroll-up' );
					var startHeight = parseInt( activeHeader.getAttribute( 'data-start-height' ) );
					if ( ! startHeight || ( e && undefined !== e.type && ( 'orientationchange' === e.type ) ) ) {
						activeHeader.setAttribute( 'data-start-height', activeHeader.offsetHeight );
						startHeight = activeHeader.offsetHeight;
						if ( parent.classList.contains( 'site-header-upper-inner-wrap' ) ) {
							parent.style.height = null;
							if ( e && undefined !== e.type && ( 'orientationchange' === e.type ) ) {
								if ( activeHeader.classList.contains( 'item-is-fixed' ) ) {
									setTimeout(function(){
										parent.style.height = Math.floor( parent.offsetHeight + activeHeader.offsetHeight ) + 'px';
									}, 21);
								} else {
									setTimeout(function(){ parent.style.height = parent.offsetHeight + 'px'; }, 21);
								}
							} else {
								parent.style.height = parent.offsetHeight + 'px';
							}
						} else if ( parent.classList.contains( 'site-header-inner-wrap' ) ) {
							parent.style.height = null;
							parent.style.height = parent.offsetHeight + 'px';
						} else {
							parent.style.height = activeHeader.offsetHeight + 'px';
						}
					}
					if ( 'true' === shrink ) {
						var shrinkHeight = activeHeader.getAttribute( 'data-shrink-height' );
						if ( shrinkHeight ) {
							if ( 'true' === revealScroll ) {
								if ( window.scrollY  > lastScrollTop ) {
									var totalOffsetDelay = Math.floor( ( Math.floor( activeOffsetTop ) - Math.floor( offsetTop ) ) + Math.floor( startHeight ) );
								} else {
									var totalOffsetDelay = Math.floor( activeOffsetTop - offsetTop );
								}
							} else {
								var totalOffsetDelay = Math.floor( activeOffsetTop - offsetTop );
							}
							var shrinkLogos = activeHeader.querySelectorAll( '.custom-logo' );
							var shrinkHeader = activeHeader.querySelector( '.site-main-header-inner-wrap' );
							var shrinkStartHeight = parseInt( shrinkHeader.getAttribute( 'data-start-height' ) );
							if ( ! shrinkStartHeight ) {
								shrinkHeader.setAttribute( 'data-start-height', shrinkHeader.offsetHeight );
								shrinkStartHeight = shrinkHeader.offsetHeight;
							}
							if ( window.scrollY <= totalOffsetDelay ) {
								shrinkHeader.style.height = shrinkStartHeight + 'px';
								shrinkHeader.style.minHeight = shrinkStartHeight + 'px';
								shrinkHeader.style.maxHeight = shrinkStartHeight + 'px';
								if ( shrinkLogos ) {
									for (let i = 0; i < shrinkLogos.length; i++) {
										let shrinkLogo = shrinkLogos[i];
										shrinkLogo.style.maxHeight = '100%';
									}
								}
							} else if ( window.scrollY > totalOffsetDelay ) {
								var shrinkingHeight = Math.max( shrinkHeight, shrinkStartHeight - ( window.scrollY - ( activeOffsetTop - offsetTop ) ) );
								shrinkHeader.style.height = shrinkingHeight + 'px';
								shrinkHeader.style.minHeight = shrinkingHeight + 'px';
								shrinkHeader.style.maxHeight = shrinkingHeight + 'px';
								if ( shrinkLogos ) {
									for (let i = 0; i < shrinkLogos.length; i++) {
										let shrinkLogo = shrinkLogos[i];
										shrinkLogo.style.maxHeight = shrinkingHeight + 'px';
									}
								}
							}
						}
					}
					if ( 'true' === revealScroll ) {
						var totalOffset = Math.floor( activeOffsetTop - offsetTop );
						var currScrollTop = window.scrollY;
						var elHeight		= activeHeader.offsetHeight;
						var wScrollDiff		= lastScrollTop - currScrollTop;
						var elTop			= window.getComputedStyle( activeHeader ).getPropertyValue( 'transform' ).match(/(-?[0-9\.]+)/g);
						if ( elTop && undefined !== elTop[5] && elTop[5] ) {
							var elTopOff = parseInt( elTop[5] ) + wScrollDiff;
						} else {
							var elTopOff = 0;
						}
						var isScrollingDown = currScrollTop > lastScrollTop;
						if ( currScrollTop <= totalOffset ) {
							activeHeader.style.transform = 'translateY(0px)';
                        } else if ( isScrollingDown ) {
							activeHeader.classList.add('item-hidden-above');
							activeHeader.style.transform = 'translateY(' + ( Math.abs( elTopOff ) > elHeight ? -elHeight : elTopOff ) + 'px)';
						} else {
							var totalOffset = Math.floor( activeOffsetTop - offsetTop );
							activeHeader.style.transform = 'translateY(' + ( elTopOff > 0 ? 0 : elTopOff ) + 'px)';
							activeHeader.classList.remove('item-hidden-above');
						}
						lastScrollTop = currScrollTop;
					} else {
						var totalOffset = Math.floor( activeOffsetTop - offsetTop );
					}
					if ( window.scrollY == totalOffset ) {
						activeHeader.style.top = offsetTop + 'px';
						activeHeader.classList.add('item-is-fixed');
						activeHeader.classList.add('item-at-start');
						activeHeader.classList.remove('item-is-stuck');
						parent.classList.add('child-is-fixed');
						document.body.classList.add('header-is-fixed');
					} else if ( window.scrollY > totalOffset ) {
						if ( 'true' === revealScroll ) {
							if ( window.scrollY < ( elHeight + 60 ) && activeHeader.classList.contains( 'item-at-start' ) ) {
								activeHeader.style.height = null;
                                activeHeader.style.top = offsetTop + 'px';
								activeHeader.classList.add('item-is-fixed');
								activeHeader.classList.add('item-is-stuck');
								parent.classList.add('child-is-fixed');
								document.body.classList.add('header-is-fixed');
							} else {
								activeHeader.style.top = offsetTop + 'px';
								activeHeader.classList.add('item-is-fixed');
								activeHeader.classList.add('item-is-stuck');
								activeHeader.classList.remove('item-at-start');
								parent.classList.add('child-is-fixed');
								document.body.classList.add('header-is-fixed');
							}
						} else {
							activeHeader.style.top = offsetTop + 'px';
							activeHeader.classList.add('item-is-fixed');
							activeHeader.classList.remove('item-at-start');
							activeHeader.classList.add('item-is-stuck');
							parent.classList.add('child-is-fixed');
							document.body.classList.add('header-is-fixed');
						}
					} else {
						if ( activeHeader.classList.contains( 'item-is-fixed' ) ) {
							activeHeader.classList.remove( 'item-is-fixed' );
							activeHeader.classList.remove('item-at-start');
							activeHeader.classList.remove('item-is-stuck');
							activeHeader.style.height = null;
							activeHeader.style.top = null;
							parent.classList.remove('child-is-fixed');
							document.body.classList.remove( 'header-is-fixed' );
						}
					}
				}
				if ( desktopSticky || mobileSticky ) {
					window.addEventListener( 'resize', updateSticky, false );
					window.addEventListener( 'scroll', updateSticky, false );
					window.addEventListener( 'load', updateSticky, false );
					window.addEventListener( 'orientationchange', updateSticky );
					if ( document.readyState === 'complete' ) {
						updateSticky( 'updateActive' );
					}
					if ( document.body.classList.contains( 'woocommerce-demo-store' ) && document.body.classList.contains( 'kadence-store-notice-placement-above' ) ) {
						var respondToVisibility = function(element, callback) {
							var options = {
							  root: document.documentElement
							}
						  
							var observer = new IntersectionObserver( (entries, observer) => {
							  entries.forEach(entry => {
								callback(entry.intersectionRatio > 0);
							  });
							}, options);
						  
							observer.observe(element);
						  }
						respondToVisibility( document.querySelector( '.woocommerce-store-notice'), visible => {
							updateSticky('updateActive');
						});
					}
				}
		},
		getTopOffset: function( event = 'scroll' ) {
			if ( event === 'load' ) {
				var desktopSticky = document.querySelector( '#main-header .kadence-sticky-header' ),
					mobileSticky  = document.querySelector( '#mobile-header .kadence-sticky-header' );
			} else {
				var desktopSticky = document.querySelector( '#main-header .kadence-sticky-header:not([data-reveal-scroll-up="true"])' ),
					mobileSticky  = document.querySelector( '#mobile-header .kadence-sticky-header:not([data-reveal-scroll-up="true"])' );
			}
			var activeScrollOffsetTop = 0,
				activeScrollAdminOffsetTop = 0;
			if ( kadenceConfig.breakPoints.desktop <= window.innerWidth ) {
				if ( desktopSticky ) {
					var shrink = desktopSticky.getAttribute( 'data-shrink' );
					if ( 'true' === shrink && ! desktopSticky.classList.contains( 'site-header-inner-wrap' ) ) {
						activeScrollOffsetTop = Math.floor( desktopSticky.getAttribute( 'data-shrink-height' ) );
					} else {
						activeScrollOffsetTop = Math.floor( desktopSticky.offsetHeight );
					}
				} else {
					activeScrollOffsetTop = 0;
				}
				if ( document.body.classList.contains( 'admin-bar' ) ) {
					activeScrollAdminOffsetTop = 32;
				}
			} else {
				if ( mobileSticky ) {
					var shrink = mobileSticky.getAttribute( 'data-shrink' );
					if ( 'true' === shrink ) {
						activeScrollOffsetTop = Math.floor( mobileSticky.getAttribute( 'data-shrink-height' ) );
					} else {
						activeScrollOffsetTop = Math.floor( mobileSticky.offsetHeight );
					}
				} else {
					activeScrollOffsetTop = 0;
				}
				if ( document.body.classList.contains( 'admin-bar' ) ) {
					activeScrollAdminOffsetTop = 46;
				}
			}
			return Math.floor( activeScrollOffsetTop + activeScrollAdminOffsetTop + Math.floor( kadenceConfig.scrollOffset ) );
		},
		scrollToElement: function( element, history, event = 'scroll' ) {
			history = (typeof history !== 'undefined') ? history : true;
			var offsetSticky = window.kadence.getTopOffset( event );
			var originalTop = Math.floor( element.getBoundingClientRect().top ) - offsetSticky;
			window.scrollBy( { top: originalTop, left: 0, behavior: 'smooth' } );
			element.tabIndex = '-1';
			element.focus({
				preventScroll: true
			});
			if ( element.classList.contains( 'kt-title-item' ) ) {
				element.firstElementChild.click();
			}
			if ( history ) {
				window.history.pushState('', '', '#' + element.id );
			}
		},
		anchorScrollToCheck: function( e, respond ) {
			respond = (typeof respond !== 'undefined') ?  respond : null;
			if ( e.target.getAttribute('href') ) {
				var targetLink = e.target;
			} else {
				var targetLink = e.target.closest('a');
				if ( ! targetLink ) {
					return;
				}
				if ( ! targetLink.getAttribute('href') ) {
					return;
				}
			}
			if ( targetLink.parentNode && targetLink.parentNode.hasAttribute('role') && targetLink.parentNode.getAttribute('role') === 'tab' ) {
				return;
			}
			var targetID;
			if ( respond ) {
				targetID = respond.getAttribute( 'href' ).substring( respond.getAttribute('href').indexOf('#') );
			} else {
				targetID = targetLink.getAttribute('href').substring( targetLink.getAttribute('href').indexOf('#') );
			}
			var targetAnchor = document.getElementById( targetID.replace( '#', '' ) );
			if ( ! targetAnchor ) {
				//window.location.href = targetLink.getAttribute('href');
				return;
			}
			e.preventDefault();
			window.kadence.scrollToElement( targetAnchor );
		},
		/**
		 * Initiate the sticky sidebar last width.
		 */
		initStickySidebarWidget: function() {
			if ( ! document.body.classList.contains( 'has-sticky-sidebar-widget' ) ) {
				return;
			}
			var offsetSticky = window.kadence.getTopOffset(),
			widget         = document.querySelector( '#secondary .sidebar-inner-wrap .widget:last-child' );
			if ( widget ) { 
				widget.style.top = Math.floor( offsetSticky + 20 ) + 'px';
				widget.style.maxHeight = 'calc( 100vh - ' + Math.floor( offsetSticky + 20 ) + 'px )';
			}
		},
		/**
		 * Initiate the sticky sidebar.
		 */
		initStickySidebar: function() {
			if ( ! document.body.classList.contains( 'has-sticky-sidebar' ) ) {
				return;
			}
			var offsetSticky = window.kadence.getTopOffset(),
			sidebar          = document.querySelector( '#secondary .sidebar-inner-wrap' );
			if ( sidebar ) { 
				sidebar.style.top = Math.floor( offsetSticky + 20 ) + 'px';
				sidebar.style.maxHeight = 'calc( 100vh - ' + Math.floor( offsetSticky + 20 ) + 'px )';
			}
		},
		/**
		 * Initiate the scroll to top.
		 */
		initAnchorScrollTo: function() {
			if ( document.body.classList.contains( 'no-anchor-scroll' ) ) {
				return;
			}
			window.onhashchange = function() {
				if ( window.location.hash === '' ) {
					window.scrollTo({top: 0, behavior: 'smooth'});
					document.activeElement.blur();
				}
			};
			if ( window.location.hash != '' ) {
				var id = location.hash.substring( 1 ),
					element;

				if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
					return;
				}
				element = document.getElementById( id );
				if ( element ) {
					window.setTimeout( function() {
						window.kadence.scrollToElement( element, false, 'load' );
					}, 100 );
				}
			}
			var foundLinks = document.querySelectorAll( 'a[href*=\\#]:not([href=\\#]):not(.scroll-ignore):not([data-tab]):not([data-toggle])' );
			if ( ! foundLinks.length ) {
				return;
			}
			foundLinks.forEach( function( element ) {
				try {
					var targetURL = new URL( element.href );
					if ( targetURL.pathname === window.location.pathname ) {
						element.addEventListener( 'click', function( e ) {
							window.kadence.anchorScrollToCheck( e );
						} );
					}
				} catch (error) {
					console.log('ClassList: ' + element.classList, 'Invalid URL' );
				}
			} );
		},
		/**
		 * Initiate the scroll to top.
		 */
		initScrollToTop: function() {
			var scrollBtn = document.getElementById( "kt-scroll-up" );
			if ( scrollBtn ) {
				var checkScrollVisiblity = function() {
					if ( window.scrollY > 100 ) {
						scrollBtn.classList.add( 'scroll-visible' );
					} else {
						scrollBtn.classList.remove( 'scroll-visible' );;
					}
				}
				window.addEventListener( 'scroll', checkScrollVisiblity );
				checkScrollVisiblity();
				// Toggle the Scroll to top on click.
				scrollBtn.addEventListener( 'click', function( e ) {
					e.preventDefault();
					//window.scrollBy( { top: 0, left: 0, behavior: 'smooth' } );
					window.scrollTo({top: 0, behavior: 'smooth'});
					document.activeElement.blur();
				} );
			}
			var scrollBtnReader = document.getElementById( "kt-scroll-up-reader" );
			if ( scrollBtnReader ) {
				scrollBtnReader.addEventListener( 'click', function( e ) {
					e.preventDefault();
					//window.scrollBy( { top: 0, left: 0, behavior: 'smooth' } );
					window.scrollTo({top: 0, behavior: 'smooth'});
					document.querySelector( '.skip-link' ).focus();
				} );
			}
		},
		// Initiate the menus when the DOM loads.
		init: function() {
			window.kadence.initNavToggleSubmenus();
			window.kadence.initToggleDrawer();
			window.kadence.initMobileToggleAnchor();
			window.kadence.initMobileToggleSub();
			window.kadence.initOutlineToggle();
			window.kadence.initStickyHeader();
			window.kadence.initStickySidebar();
			window.kadence.initStickySidebarWidget();
			window.kadence.initTransHeaderPadding();
			window.kadence.initAnchorScrollTo();
			window.kadence.initScrollToTop();
		}
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', window.kadence.init );
	} else {
		// The DOM has already been loaded.
		window.kadence.init();
	}
})();

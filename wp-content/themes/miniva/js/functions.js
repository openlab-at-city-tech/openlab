/**
 * File functions.js.
 *
 * - Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 *
 * - Helps with accessibility for keyboard only users. Learn more: https://git.io/vWdr2
 *
 * - Handles objectFit fallback for Internet Explorer and Edge
 *
 * - Handles fluid videos
 *
 * @package Miniva
 */

( function() {
	var container, button, menu, links, i, len, submenu, submenu_toggle, current_submenu;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = document.getElementsByClassName( 'menu-toggle' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	submenu = menu.getElementsByTagName( 'ul' );
	for ( i = 0, len = submenu.length; i < len; i++ ) {
		submenu[ i ].insertAdjacentHTML( 'beforebegin', '<button class="submenu-toggle"><svg aria-hidden="true" width="12" height="12" class="icon"><use xlink:href="#expand" /></svg><span class="screen-reader-text">' + miniva.expand_text + '</span></button>' );
	}

	submenu_toggle = menu.getElementsByClassName( 'submenu-toggle' );
	for ( i = 0, len = submenu_toggle.length; i < len; i++ ) {
		submenu_toggle[ i ].onclick = function() {
			current_submenu = this.nextElementSibling;
			if ( current_submenu.classList.contains( 'toggled' ) ) {
				this.classList.remove( 'toggled' );
				this.setAttribute( 'aria-expanded', 'false' );
				this.querySelector( 'span' ).innerHTML = miniva.expand_text;
				current_submenu.classList.remove( 'toggled' );
			} else {
				this.classList.add( 'toggled' );
				this.setAttribute( 'aria-expanded', 'true' );
				this.querySelector( 'span' ).innerHTML = miniva.collapse_text;
				current_submenu.classList.add( 'toggled' );
			}
		}
	}

	// Get all the link elements within the menu.
	links = menu.getElementsByTagName( 'a' );

	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( -1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	/**
	 * Toggles `focus` class to allow submenu access on tablets.
	 */
	( function( container ) {
		var touchStartFn, i,
			parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

		if ( 'ontouchstart' in window ) {
			touchStartFn = function( e ) {
				var menuItem = this.parentNode, i;

				if ( ! menuItem.classList.contains( 'focus' ) ) {
					e.preventDefault();
					var menuItemLength = menuItem.parentNode.children.length;
					for ( i = 0; i < menuItemLength; ++i ) {
						if ( menuItem === menuItem.parentNode.children[i] ) {
							continue;
						}
						menuItem.parentNode.children[i].classList.remove( 'focus' );
					}
					menuItem.classList.add( 'focus' );
				} else {
					menuItem.classList.remove( 'focus' );
				}
			};

			var parentLinkLength = parentLink.length;
			for ( i = 0; i < parentLinkLength; ++i ) {
				parentLink[i].addEventListener( 'touchstart', touchStartFn, false );
			}
		}
	}( container ) );

	var isIe = /(trident|msie)/i.test( navigator.userAgent );

	if ( isIe && document.getElementById && window.addEventListener ) {
		window.addEventListener( 'hashchange', function() {
			var id = location.hash.substring( 1 ),
				element;

			if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
				return;
			}

			element = document.getElementById( id );

			if ( element ) {
				if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
					element.tabIndex = -1;
				}

				element.focus();
			}
		}, false );
	}

	if ( 'objectFit' in document.documentElement.style === false ) {
		var img_parent = document.getElementsByClassName( 'img-cover' );
		var par_count  = img_parent.length;
		for ( var i = 0; i < par_count; i++ ) {
			var img = img_parent[i].querySelector( 'img' );
			if ( img !== null ) {
				img.style.display                      = 'none';
				img_parent[i].style.backgroundSize     = 'cover';
				img_parent[i].style.backgroundImage    = 'url( ' + img.src + ' )';
				img_parent[i].style.backgroundPosition = 'center center';
			}
		}
	}

	/**
	 * Fluid videos
	 */
	if ( miniva.fluidvids ) {
		var iframes = document.querySelectorAll( '.entry-content *:not(.wp-block-embed__wrapper) > iframe, .widget iframe' );
		for ( var i = 0; i < iframes.length; i++ ) {
			var iframe = iframes[i];
			if ( typeof iframe.src !== 'string' ) {
				continue;
			}
			if ( iframe.src.indexOf( 'youtube.com' ) === -1 && iframe.src.indexOf( 'player.vimeo.com' ) === -1 ) {
				continue;
			}

			var wrapper = document.createElement( 'div' );
			wrapper.className = 'fluid-video';
			var ratio = ( iframe.height / iframe.width ) * 100;
			if (ratio) {
				wrapper.style.paddingTop = ratio + '%';
			}

			iframe.parentNode.insertBefore( wrapper, iframe );
			wrapper.appendChild( iframe );
		}
	}
} )();

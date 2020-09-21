// ======================================================================= Namespace
var koji = koji || {},
	$ = jQuery;


// ======================================================================= Helper Functions

/* OUTPUT AJAX ERRORS */

function kojiAjaxErrors( jqXHR, exception ) {
	if ( jqXHR.status === 0 ) {
		alert( 'Not connect.n Verify Network.' );
	} else if ( jqXHR.status == 404 ) {
		alert( 'Requested page not found. [404]' );
	} else if ( jqXHR.status == 500 ) {
		alert( 'Internal Server Error [500].' );
	} else if ( exception === 'parsererror' ) {
		alert( 'Requested JSON parse failed.' );
	} else if ( exception === 'timeout' ) {
		alert( 'Time out error.' );
	} else if ( exception === 'abort' ) {
		alert( 'Ajax request aborted.' );
	} else {
		alert( 'Uncaught Error.n' + jqXHR.responseText );
	}
}

/* TOGGLE AN ATTRIBUTE */

function kojiToggleAttribute( $element, attribute, trueVal, falseVal ) {

	if ( typeof trueVal === 'undefined' ) { trueVal = true; }
	if ( typeof falseVal === 'undefined' ) { falseVal = false; }

	if ( $element.attr( attribute ) !== trueVal ) {
		$element.attr( attribute, trueVal );
	} else {
		$element.attr( attribute, falseVal );
	}
}


// ======================================================================= Interval Scroll
koji.intervalScroll = {

	init: function() {

		didScroll = false;

		// Check for the scroll event
		$( window ).on( 'scroll load', function() {
			didScroll = true;
		} );

		// Once every 100ms, check if we have scrolled, and if we have, do the intensive stuff
		setInterval( function() {
			if ( didScroll ) {
				didScroll = false;

				// When this triggers, we know that we have scrolled
				$( window ).triggerHandler( 'did-interval-scroll' );

			}

		}, 250 );

	},

} // koji.intervalScroll


// ======================================================================= Toggles
koji.toggles = {

	init: function() {

		// Do the toggle
		koji.toggles.toggle();

	},

	toggle: function() {

		$( '*[data-toggle-target]' ).on( 'click toggle', function( e ) {

			var $toggle = $( this );

			// Get our targets
			var targetString = $toggle.data( 'toggle-target' );

			if ( targetString == 'next' ) {
				var $target = $toggle.next();
			} else {
				var $target = $( targetString );
			}

			$target.trigger( 'will-be-toggled' );

			// Get the class to toggle, if specified
			var classToToggle = $toggle.data( 'class-to-toggle' ) ? $toggle.data( 'class-to-toggle' ) : 'active';

			// Toggle the target of the clicked toggle
			$target.toggleClass( classToToggle );

			if ( $toggle.data( 'toggle-type' ) == 'slidetoggle' ) {
				var duration = $toggle.data( 'toggle-duration' ) ? $toggle.data( 'toggle-duration' ) : '400';
				$target.slideToggle( duration );	
			}

			// Toggle aria-expanded
			kojiToggleAttribute( $target, 'aria-expanded' );

			// Toggle the toggles
			$( '*[data-toggle-target="' + targetString + '"]' ).each( function() {
				$( this ).toggleClass( 'active' );

				// Update ARIA values
				kojiToggleAttribute( $( this ), 'aria-pressed' );
			} );

			// Check whether to set focus
			if ( $toggle.is( '.active' ) && $toggle.data( 'set-focus' ) ) {
				var $focusElement = $( $toggle.data( 'set-focus' ) );

				if ( $focusElement.length ) {
					$focusElement.focus();
				}
			}

			// Check whether to lock the scroll
			if ( $toggle.data( 'lock-scroll' ) ) {
				koji.scrollLock.setTo( true );
			} else if ( $toggle.data( 'unlock-scroll' ) ) {
				koji.scrollLock.setTo( false );
			} else if ( $toggle.data( 'toggle-scroll-lock' ) ) {
				koji.scrollLock.setTo();
			}

			$target.trigger( 'toggled' );

			return false;

		} );
	},

} // koji.toggles


// ======================================================================= Search Modal
koji.searchModal = {

	init: function() {

		if ( $( '.search-overlay' ).length ) {

			// Check whether we need to lock scroll when the search modal is toggled
			koji.searchModal.conditionalScrollLockOnToggle();

			// When toggled, untoggle if visitor clicks outside of the search container
			koji.searchModal.outsideUntoggle();

			// Close the modal when the escape key is pressed
			koji.searchModal.closeOnEscape();

		}

	},

	conditionalScrollLockOnToggle: function() {

		$( '.search-overlay' ).on( 'toggled', function() {

			var winWidth = $( window ).width();

			if ( winWidth >= 1000 ) {
				koji.scrollLock.setTo();
			}

		} );

	},

	outsideUntoggle: function() {

		$( document ).on( 'click', function( e ) {

			var $target = $( e.target ),
				modal = '.search-overlay',
				modalActive = modal + '.active';

			if ( $( modalActive ).length && $target.not( $( modal ) ) && ! $target.parents( $( modal ) ) ) {
				$( '.search-untoggle' ).trigger( 'click' );
			}

		} );

	},

	closeOnEscape: function() {

		$( document ).keyup( function( e ) {
			if ( e.keyCode == 27 && $( '.search-overlay' ).hasClass( 'active' ) ) {
				$( '.search-untoggle' ).trigger( 'click' );
			}
		} );

	},

} // koji.searchModal


// ======================================================================= Element in View
koji.elementInView = {

	init: function() {

		$targets = $( '.do-spot' );
		koji.elementInView.run( $targets );

		// Rerun on AJAX content loaded
		$( window ).on( 'ajax-content-loaded', function() {
			$targets = $( '.do-spot' );
			koji.elementInView.run( $targets );
		} );

	},

	run: function( $targets ) {

		if ( $targets.length ) {

			// Add class indicating the elements will be spotted
			$targets.each( function() {
				$( this ).addClass( 'will-be-spotted' );
			} );

			koji.elementInView.handleFocus( $targets );
		}

	},

	handleFocus: function( $targets ) {

		winHeight = $( window ).height();

		// Get dimensions of window outside of scroll for performance
		$( window ).on( 'load resize orientationchange', function() {
			winHeight = $( window ).height();
		} );

		$( window ).on( 'resize orientationchange did-interval-scroll', function() {

			var winTop 		= $( window ).scrollTop();
				winBottom 	= winTop + winHeight;

			// Check for our targets
			$targets.each( function() {

				var $this = $( this );

				if ( koji.elementInView.isVisible( $this, checkAbove = true ) ) {
					$this.addClass( 'spotted' ).triggerHandler( 'spotted' );
				}

			} );

		} );

	},

	// Determine whether the element is in view
	isVisible: function( $elem, checkAbove ) {

		if ( typeof checkAbove === 'undefined' ) {
			checkAbove = false;
		}

		var winHeight 				= $( window ).height();

		var docViewTop 				= $( window ).scrollTop(),
			docViewBottom			= docViewTop + winHeight,
			docViewLimit 			= docViewBottom - 50;

		var elemTop 				= $elem.offset().top,
			elemBottom 				= $elem.offset().top + $elem.outerHeight();

		// If checkAbove is set to true, which is default, return true if the browser has already scrolled past the element
		if ( checkAbove && ( elemBottom <= docViewBottom ) ) {
			return true;
		}

		// If not, check whether the scroll limit exceeds the element top
		return ( docViewLimit >= elemTop );

	}

} // koji.elementInView


// =======================================================================  Mobile Menu
koji.mobileMenu = {

	init: function() {

		// On mobile menu toggle, scroll to the top
		koji.mobileMenu.onToggle();

		// On screen resize, check whether to unlock scroll and match the mobile menu wrapper padding to the site header
		koji.mobileMenu.resizeChecks();

		// If the user tabs out of the mobile menu, set focus to the navigation toggle
		koji.mobileMenu.focusLoop();

	},

	onToggle: function() {

		$( '.mobile-menu-wrapper' ).on( 'will-be-toggled', function() {
			window.scrollTo( 0, 0 );
		} );

	},

	resizeChecks: function() {

		$( window ).on( 'load resize orientationchange', function() {

			// Update the mobile menu wrapper top padding to match the height of the header
			var $siteHeader = $( '#site-header' ),
				headerHeight = $siteHeader.outerHeight(),
				$mobileMenuWrapper = $( '.mobile-menu-wrapper' );

			$mobileMenuWrapper.css( { 'padding-top': headerHeight + 'px' } );

			// Unlock the scroll if we pass the breakpoint for hiding the mobile menu
			if ( $( window ).width() >= 1000 && $( '.nav-toggle' ).hasClass( 'active' ) ) {
				$( '.nav-toggle' ).trigger( 'click' );
			}
		} );

	},

	focusLoop: function() {
		$( '*' ).on( 'focus', function() {
			if ( $( '.mobile-menu-wrapper' ).hasClass( 'active' ) ) {
				if ( $( this ).parents( '#site-content' ).length ) {
					$( '.nav-toggle' ).focus();
				}
			}
		} );
	}

} // koji.mobileMenu


// =======================================================================  Resize videos
koji.intrinsicRatioEmbeds = {

	init: function() {

		// Resize videos after their container
		var vidSelector = 'iframe, object, video';
		var resizeVideo = function( sSel ) {
			$( sSel ).each( function() {
				var $video = $( this ),
					$container = $video.parent(),
					iTargetWidth = $container.width();

				if ( ! $video.attr( 'data-origwidth' ) ) {
					$video.attr( 'data-origwidth', $video.attr( 'width' ) );
					$video.attr( 'data-origheight', $video.attr( 'height' ) );
				}

				var ratio = iTargetWidth / $video.attr( 'data-origwidth' );

				$video.css( 'width', iTargetWidth + 'px' );
				$video.css( 'height', ( $video.attr( 'data-origheight' ) * ratio ) + 'px' );
			});
		};

		resizeVideo( vidSelector );

		$( window ).resize( function() {
			resizeVideo( vidSelector );
		} );

	},

} // koji.intrinsicRatioEmbeds


// ======================================================================= Masonry
koji.masonry = {

	init: function() {

		$wrapper = $( '.posts' );

		if ( $wrapper.length ) {

			$grid = $wrapper.imagesLoaded( function() {

				$grid = $wrapper.masonry( {
					columnWidth: 		'.grid-sizer',
					itemSelector: 		'.preview',
					percentPosition: 	true,
					stagger: 			0,
					transitionDuration: 0,
				} );

			} );

			$grid.on( 'layoutComplete', function() {
				$( '.posts' ).css( 'opacity', 1 );
				$( window ).triggerHandler( 'scroll' );
			} );

		}

	}

} // koji.masonry


// =======================================================================  Smooth Scroll
koji.smoothScroll = {

	init: function() {

		// Smooth scroll to anchor links
		$( 'a[href*="#"]' )
		// Remove links that don't actually link to anything
		.not( '[href="#"]' )
		.not( '[href="#0"]' )
		.not( '.skip-link' )
		.click( function( event ) {
			// On-page links
			if ( location.pathname.replace( /^\//, '' ) == this.pathname.replace( /^\//, '' ) && location.hostname == this.hostname ) {
				// Figure out element to scroll to
				var target = $( this.hash );
				target = target.length ? target : $( '[name=' + this.hash.slice( 1 ) + ']' );
				// Does a scroll target exist?
				if ( target.length ) {
					// Only prevent default if animation is actually gonna happen
					event.preventDefault();
					$( 'html, body' ).animate({
						scrollTop: target.offset().top
					}, 1000 );
				}
			}
		});

	},

} // koji.smoothScroll


// =======================================================================  Scroll Lock
koji.scrollLock = {

	init: function() {

		// Init variables
		window.scrollLocked = false,
		window.prevScroll = {
			scrollLeft : $( window ).scrollLeft(),
			scrollTop  : $( window ).scrollTop()
		},
		window.prevLockStyles = {},
		window.lockStyles = {
			'overflow-y' 	: 'scroll',
			'position'   	: 'fixed',
			'width'      	: '100%'
		};

		// Instantiate cache in case someone tries to unlock before locking
		koji.scrollLock.saveStyles();

	},

	// Save context's inline styles in cache
	saveStyles: function() {

		var styleAttr = $( 'html' ).attr( 'style' ),
			styleStrs = [],
			styleHash = {};

		if ( ! styleAttr ) {
			return;
		}

		styleStrs = styleAttr.split( /;\s/ );

		$.each( styleStrs, function serializeStyleProp( styleString ) {
			if ( ! styleString ) {
				return;
			}

			var keyValue = styleString.split( /\s:\s/ );

			if ( keyValue.length < 2 ) {
				return;
			}

			styleHash[ keyValue[ 0 ] ] = keyValue[ 1 ];
		} );

		$.extend( prevLockStyles, styleHash );
	},

	// Lock the scroll (do not call this directly)
	lock: function() {

		var appliedLock = {};

		if ( scrollLocked ) {
			return;
		}

		// Save scroll state and styles
		prevScroll = {
			scrollLeft : $( window ).scrollLeft(),
			scrollTop  : $( window ).scrollTop()
		};

		koji.scrollLock.saveStyles();

		// Compose our applied CSS, with scroll state as styles
		$.extend( appliedLock, lockStyles, {
			'left' : - prevScroll.scrollLeft + 'px',
			'top'  : - prevScroll.scrollTop + 'px'
		} );

		// Then lock styles and state
		$( 'html' ).css( appliedLock ).addClass( 'html-locked' );
		$( window ).scrollLeft( 0 ).scrollTop( 0 );
		$( 'body' ).addClass( 'scroll-locked' );

		scrollLocked = true;
	},

	// Unlock the scroll (do not call this directly)
	unlock: function() {

		if ( ! scrollLocked ) {
			return;
		}

		// Revert styles and state
		$( 'html' ).attr( 'style', $( '<x>' ).css( prevLockStyles ).attr( 'style' ) || '' ).removeClass( 'html-locked' );
		$( window ).scrollLeft( prevScroll.scrollLeft ).scrollTop( prevScroll.scrollTop );
		$( 'body' ).removeClass( 'scroll-locked' );

		scrollLocked = false;
	},

	// Call this to lock or unlock the scroll
	setTo: function( on ) {

		// If an argument is passed, lock or unlock accordingly
		if ( arguments.length ) {
			if ( on ) {
				koji.scrollLock.lock();
			} else {
				koji.scrollLock.unlock();
			}
			// If not, toggle to the inverse state
		} else {
			if ( scrollLocked ) {
				koji.scrollLock.unlock();
			} else {
				koji.scrollLock.lock();
			}
		}

	},

} // koji.scrollLock


// ==================================================================== Load More
koji.loadMore = {

	init: function() {

		var $pagination = $( '#pagination' );

		// First, check that there's a pagination
		if ( $pagination.length ) {

			// Default values for variables
			window.loading = false;
			window.lastPage = false;

			koji.loadMore.prepare( $pagination );

		}

	},

	prepare: function( $pagination ) {

		// Get the query arguments from the pagination element
		var query_args = JSON.parse( $pagination.attr( 'data-query-args' ) );

		// If we're already at the last page, exit out here
		if ( query_args.paged == query_args.max_num_pages ) {
			$pagination.addClass( 'last-page' );
		} else {
			$pagination.removeClass( 'last-page' );
		}

		// Get the load more setting
		var loadMoreType = 'button';
		if ( $( 'body' ).hasClass( 'pagination-type-scroll' ) ) {
			loadMoreType = 'scroll';
		} else if ( $( 'body' ).hasClass( 'pagination-type-links' ) ) {
			// No JS needed – exit out
			return;
		}

		// Do the appropriate load more detection, depending on the type
		if ( loadMoreType == 'scroll' ) {
			koji.loadMore.detectScroll( $pagination, query_args );
		} else if ( loadMoreType == 'button' ) {
			koji.loadMore.detectButtonClick( $pagination, query_args );
		}

	},

	// Load more on scroll
	detectScroll: function( $pagination, query_args ) {

		$( window ).on( 'did-interval-scroll', function() {

			// If it's the last page, or we're already loading, we're done here
			if ( lastPage || loading ) {
				return;
			}

			var paginationOffset 	= $pagination.offset().top,
				winOffset 			= $( window ).scrollTop() + $( window ).outerHeight();

			// If the bottom of the window is below the top of the pagination, start loading
			if ( ( winOffset > paginationOffset ) ) {
				koji.loadMore.loadPosts( $pagination, query_args );
			}

		} );

	},

	// Load more on click
	detectButtonClick: function( $pagination, query_args ) {

		// Load on click
		$( '#load-more' ).on( 'click', function() {

			// Make sure we aren't already loading
			if ( loading ) {
				return;
			}

			koji.loadMore.loadPosts( $pagination, query_args );
			return false;
		} );

	},

	// Load the posts
	loadPosts: function( $pagination, query_args ) {

		// We're now loading
		loading = true;
		$pagination.addClass( 'loading' ).removeClass( 'last-page' );

		// Increment paged to indicate another page has been loaded
		query_args.paged++;

		// Prepare the query args for submission
		var json_query_args = JSON.stringify( query_args );

		$.ajax({
			url: koji_ajax_load_more.ajaxurl,
			type: 'post',
			data: {
				action: 'koji_ajax_load_more',
				json_data: json_query_args
			},
			success: function( result ) {

				// Get the results
				var $result = $( result ),
					$articleWrapper = $( $pagination.data( 'load-more-target' ) );

				// If there are no results, we're at the last page
				if ( ! $result.length ) {
					loading = false;
					$articleWrapper.addClass( 'no-results' );
					$pagination.addClass( 'last-page' ).removeClass( 'loading' );
				}

				if ( $result.length ) {

					$articleWrapper.removeClass( 'no-results' );

					// Add a class indicating the paged of the loaded posts
					$result.each( function() {
						$( this ).addClass( 'post-from-page-' + query_args.paged );
					});

					// Wait for the images to load
					$result.imagesLoaded( function() {

						// Append the results
						$articleWrapper.append( $result ).masonry( 'appended', $result );

						$( window ).triggerHandler( 'ajax-content-loaded' );
						$( window ).triggerHandler( 'did-interval-scroll' );

						// Update history
						koji.loadMore.updateHistory( query_args.paged );

						// We're now finished with the loading
						loading = false;
						$pagination.removeClass( 'loading' );

						// If that was the last page, make sure we don't check for any more
						if ( query_args.paged == query_args.max_num_pages ) {
							$pagination.addClass( 'last-page' );
							lastPage = true;
						} else {
							$pagination.removeClass( 'last-page' );
							lastPage = false;
						}

						// Set the focus to the first item of the loaded posts
						$( '.post-from-page-' + query_args.paged + ':first .preview-title a' ).focus();

					} );

				}

			},

			error: function( jqXHR, exception ) {
				kojiAjaxErrors( jqXHR, exception );
			}
		} );

	},

	// Update browser history
    updateHistory: function( paged ) {

		var newUrl,
			currentUrl = document.location.href;

		// If currentUrl doesn't end with a slash, append one
		if ( currentUrl.substr( currentUrl.length - 1 ) !== '/' ) {
			currentUrl += '/';
		}

		var hasPaginationRegexp = new RegExp( '^(.*/page)/[0-9]*/(.*$)' );

		if ( hasPaginationRegexp.test( currentUrl ) ) {
			newUrl = currentUrl.replace( hasPaginationRegexp, '$1/' + paged + '/$2' );
		} else {
			var beforeSearchReplaceRegexp = new RegExp( '^([^?]*)(\\??.*$)' );
			newUrl = currentUrl.replace( beforeSearchReplaceRegexp, '$1page/' + paged + '/$2' );
		}

		history.pushState( {}, '', newUrl );

	}

} // Load More


// ======================================================================= Function calls
$( document ).ready( function( ) {

	koji.intervalScroll.init();						// Interval scroll

	koji.toggles.init();							// Toggles

	koji.searchModal.init();						// Cover modal specifics

	koji.elementInView.init();						// Check for element in view

	koji.mobileMenu.init();							// Mobile menu

	koji.intrinsicRatioEmbeds.init();				// Resize embeds

	koji.masonry.init();							// Masonry grid

	koji.smoothScroll.init();						// Smooth scrolls to anchor links

	koji.loadMore.init();							// Load more posts

	koji.scrollLock.init();							// Handle locking of the scroll

} );

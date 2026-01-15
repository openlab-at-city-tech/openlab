jQuery(document).ready( function( $ ) {
	var options = window.OpenLabTOC;

	var app = {
		init: function() {
			// Handle "In Page TOC" - set initial state on toggle, not container
			var inPageToggle = $( '#ez-toc-container .ez-toc-toggle' );
			inPageToggle.attr( 'aria-expanded', ! options.hideByDefault );
			$( '#ez-toc-container' ).on( 'click', '.ez-toc-toggle', app.handleInPageToggle );

			var toggle = $( 'a.ez-toc-toggle' );
			toggle.css( 'display', 'inline' );

			// No need to continue if we don't have widget.
			if ( ! app.hasWidget() ) {
				return;
			}

			app.cache();
			app.bindEvents();
			app.setUpObserver();
			app.observeSections();

			// Hide by default - set on toggle button, not container
			if ( window.innerWidth < 1023 || options.hideByDefault ) {
				app.container.find('.ez-toc-toggle').attr( 'aria-expanded', 'false' );
				app.container.addClass( 'toc-collapsed' );
			} else {
				app.container.find('.ez-toc-toggle').attr( 'aria-expanded', 'true' );
				app.container.removeClass( 'toc-collapsed' );
			}

			/*
			 * If landing on a hash, navigate there.
			 *
			 * This is necessary because of some odd behavior where IntersectionObserver
			 * invokes the callback twice during the initialization process, once in the correct
			 * way and again in a way that incorrectly identifies an earlier element as
			 * being inside the viewport.
			 */
			if ( document.location.hash.length > 0 ) {
				setTimeout(
					function() {
						var hashEl = document.getElementById( document.location.hash.substr( 1 ) );
						if ( hashEl ) {
							hashEl.scrollIntoView();
						}
					},
					100
				);
			}
		},

		cache: function() {
			app.window = $( window );
			app.content = $('.hentry').length ? $('.hentry') : $('.entry');
			app.container = $( '.ez-toc' );
			app.containerTop = app.container.offset().top;
			app.previousSection = null;
			app.offset = app.getTopNavOffeset();
			app.footerCollapse = false;
			app.links = Array.from( document.querySelectorAll( '.ez-toc .ez-toc-list a' ) );
			app.sections = app.links.map( function( link ) {
				var id = link.getAttribute( 'href' );
				return document.querySelector( id );
			} ).filter( function( element ) {
				if ( ! element ) {
					return false;
				}

				return true;
			} );
		},

		bindEvents: function() {
			app.window.on( 'scroll', app.handleScroll );
			app.window.on( 'resize', app.handleResize );
			app.container.on( 'click', '.ez-toc-toggle', app.handleToggle );
		},

		hasWidget: function() {
			return !! $( '.ez-toc' ).length;
		},

		setUpObserver: function() {
			app.observer = new IntersectionObserver( app.handleObserver );
		},

		observeSections: function() {
			app.sections.forEach( function( section ) {
				app.observer.observe( section );
			} );
		},

		handleObserver: function( entries ) {
			entries.forEach( function( entry ) {
				var id = entry.target.getAttribute( 'id' );
				var element = document.querySelector( '.ez-toc li a[href="#' + id + '"]' );

				if ( entry.intersectionRatio > 0 ) {
					element.classList.add( 'is-visible' );
					app.previousSection = entry.target.getAttribute( 'id' );
				} else {
					element.classList.remove( 'is-visible' );
				}
			} );

			app.markFirstVisible();
		},

		/**
		 * Multiple heading can be visible at the same time,
		 * make sure to mark only first one as active.
		 */
		markFirstVisible: function() {
			var firstVisible = app.container.find( '.is-visible' ).first();

			app.links.forEach( function( link ) {
				link.classList.remove( 'is-active' );
			} );

			if ( firstVisible ) {
				firstVisible.addClass( 'is-active' );

				// Scroll to item in TOC.
				firstVisible.focus();
			}

			if ( ! firstVisible && app.previousSection ) {
				app.container.find(
					'a[href="#' + app.previousSection + '"]'
				).addClass( 'is-active' );
			}
		},

		handleScroll: function() {
			var scrollTop = app.window.scrollTop();
			var containerHeight = window.innerHeight - app.offset;
			var toggle = app.container.find('.ez-toc-toggle');
			var isExpanded = toggle.attr( 'aria-expanded' ) === 'true';

			if ( scrollTop >= app.containerTop ) {
				app.container.addClass( 'toc-fixed' );

				if ( isExpanded ) {
					app.container.css( 'height', containerHeight );
					app.setNavHeight();
				}
			} else if ( scrollTop < app.containerTop ) {
				app.container.removeClass( 'toc-fixed' );
				app.container.css( 'height', 'auto' );
			}

			app.maybeCollapse( containerHeight );
		},

		handleResize: function() {
			// Update cached offset
			app.offset = app.getTopNavOffeset();
			var toggle = app.container.find('.ez-toc-toggle');
			var isExpanded = toggle.attr( 'aria-expanded' ) === 'true';

			if ( isExpanded ) {
				app.container.css( 'height', window.innerHeight - app.offset );
				app.setNavHeight();
			}
		},

		setNavHeight: function() {
			var containerHeight = app.container.height();
			var headerHeight = app.container.find( '.widget-title' ).height();

			app.container.find( 'nav' ).height( containerHeight - headerHeight );
		},

		handleToggle: function( event ) {
			event.preventDefault();

			var toggle = $(this);
			var isExpanded = toggle.attr( 'aria-expanded' ) === 'true';

			toggle.attr( 'aria-expanded', ! isExpanded );

			// Add/remove visual class on container for styling
			if ( isExpanded ) {
				app.container.addClass( 'toc-collapsed' );
			} else {
				app.container.removeClass( 'toc-collapsed' );
			}

			app.container.css( 'height', 'auto' );
		},

		handleInPageToggle: function( event ) {
			event.preventDefault();

			var toggle = $(this);
			var container = toggle.closest( '#ez-toc-container' );
			var isExpanded = toggle.attr( 'aria-expanded' ) === 'true';

			toggle.attr( 'aria-expanded', ! isExpanded );

			// Add/remove visual class on container for styling
			if ( isExpanded ) {
				container.addClass( 'toc-collapsed' );
			} else {
				container.removeClass( 'toc-collapsed' );
			}
		},

		getTopNavOffeset: function() {
			var offset = 0;
			var hasAdminBar = $('body').hasClass( 'admin-bar' );
			var navPrimary = document.querySelector( '.nav-primary' );

			// Handles EduPro sticky nav menu.
			if ( navPrimary ) {
				offset += 48;
			}

			if ( hasAdminBar && window.innerWidth <= 1023 ) {
				offset += 50;
			}

			if ( window.innerWidth <= 767 ) {
				offset += 63;
			}

			return offset;
		},

		maybeCollapse: function( containerHeight ) {
			var containerOffset = app.container.offset().top + containerHeight;
			var contentOffset = app.content.offset().top + app.content.height();
			var toggle = app.container.find('.ez-toc-toggle');

			if ( containerOffset > contentOffset ) {
				toggle.attr( 'aria-expanded', 'false' );
				app.container.addClass( 'toc-collapsed' );
				app.container.css( 'height', 'auto' );

				// Set flag when collapse is triggered by footer.
				app.footerCollapse = true;
			}

			if ( containerOffset < contentOffset && app.footerCollapse ) {
				toggle.attr( 'aria-expanded', 'true' );
				app.container.removeClass( 'toc-collapsed' );
				app.container.css( 'height', containerHeight );

				app.footerCollapse = false;
			}
		},
	};

	app.init();
} );

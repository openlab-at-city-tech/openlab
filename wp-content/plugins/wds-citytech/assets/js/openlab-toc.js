jQuery(document).ready( function( $ ) {
	var app = {
		init: function() {
			// Handle "In Page TOC".
			$( '#ez-toc-container' ).attr( 'aria-expanded', 'true' );
			$( '#ez-toc-container' ).on( 'click', '.ez-toc-toggle', app.handleInPageToggle );

			// No need to continue if we don't have widget.
			if ( ! app.hasWidget() ) {
				return;
			}

			app.cache();
			app.bindEvents();
			app.setUpObserver();
			app.observeSections();

			// Hide by default on
			if ( window.innerWidth < 1023 ) {
				app.container.attr( 'aria-expanded', 'false' );
			} else {
				app.container.attr( 'aria-expanded', 'true' );
			}
		},

		cache: function() {
			app.window = $( window );
			app.content = $('.hentry').length ? $('.hentry') : $('.entry');
			app.container = $( '.ez-toc' ),
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
			var containerHeight = window.innerHeight - app.offset

			if ( scrollTop >= app.containerTop ) {
				app.container.addClass( 'toc-fixed' );

				if ( 'true' === app.container.attr( 'aria-expanded' ) ) {
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
			// Update cached offest
			app.offset = app.getTopNavOffeset();

			if ( 'true' === app.container.attr( 'aria-expanded' ) ) {
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
			
			var isExpanded = app.container.attr( 'aria-expanded' );
			app.container.attr( 'aria-expanded', ( isExpanded === 'true' ) ? 'false' : 'true' );
			app.container.css( 'height', 'auto' );
		},

		handleInPageToggle: function( event ) {
			event.preventDefault();

			var container = $(this).closest( '#ez-toc-container' );
			var isExpanded = container.attr( 'aria-expanded' );

			container.attr( 'aria-expanded', ( isExpanded === 'true' ) ? 'false' : 'true' );
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

			if ( containerOffset > contentOffset ) {
				app.container.attr( 'aria-expanded', 'false' );
				app.container.css( 'height', 'auto' );

				// Set flag when collapse is triggered by footer.
				app.footerCollapse = true;
			}
			
			if ( containerOffset < contentOffset && app.footerCollapse ) {
				app.container.attr( 'aria-expanded', 'true' );
				app.container.css( 'height', containerHeight );

				app.footerCollapse = false;
			}
		},
	};

	app.init();
} );

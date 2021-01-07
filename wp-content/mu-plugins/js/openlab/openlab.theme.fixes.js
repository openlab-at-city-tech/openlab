/**
 * Any client-side theme fixes go here (for group site themes; excludes OpenLab custom theme)
 */

/**
 * Twentyfourteen
 * Makes the header relative until scrolling, to fix issue with header going behind admin bar
 */

if (window.OpenLab === undefined) {
	var OpenLab = {};
}

OpenLab.fixes = (function ($) {
	return{
		init: function () {

			if ($( 'body' ).hasClass( 'masthead-fixed' )) {
				OpenLab.fixes.fixMasthead();
			}

			OpenLab.fixes.fixHemingwayEmptyButtons();
		},
		onLoad: function () {

			OpenLab.fixes.emptyHeaders();

		},
		fixMasthead: function () {

			//this is so that the on scroll function won't fire on themes that don't need it to
			if ( ! $( 'body' ).hasClass( 'masthead-fixing' )) {
				$( 'body' ).addClass( 'masthead-fixing' );
			}

			//get adminbar height
			var adminBar_h    = $( '#wpadminbar' ).outerHeight();
			var scrollTrigger = Math.ceil( adminBar_h / 2 );

			//if were below the scrollTrigger, remove the fixed class, otherwise make sure it's there
			if (OpenLab.fixes.getCurrentScroll() <= scrollTrigger) {
				$( 'body' ).removeClass( 'masthead-fixed' );
			} else {
				$( 'body' ).addClass( 'masthead-fixed' );
			}

		},
		getCurrentScroll: function () {
			var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

			return currentScroll;
		},
		/**
		 * If theme markup has header elements that could output as empty, but available filters only let you get inside the header tags,
		 * this fix can be applied by adding a span with class "empty-header"
		 * The span should be filled with some type of default tax in the event JS is disabled
		 */
		emptyHeaders: function () {

			if ($( '.empty-header' ).length === 0) {
				return false;
			}

			$( '.empty-header' ).each(
				function () {

					OpenLab.fixes.processEmptyHeader( $( this ) );

				}
			);
		},
		processEmptyHeader: function (thisElem) {

			var thisHeader = thisElem.closest( ':header' );

			if (thisHeader.length === 0) {
				return false;
			}

			/**
			 * The replacement span we're going to add we'll inherit all of the classes and ids
			 * from the empty header element in order to maintain vertical spacing
			 * A new class "empty-header-placeholder" will be added for additional style tweaking
			 */
			var headerClasses = thisHeader.attr( 'class' );

			if (typeof headerClasses === 'undefined') {
				headerClasses = '';
			} else {
				headerClasses = ' ';
			}

			headerClasses += 'empty-header-placeholder';

			var headerID = thisHeader.attr( 'id' );

			var replacement = $( '<span></span>' );
			replacement.attr( 'id', headerID );
			replacement.addClass( headerClasses );

			thisHeader.replaceWith( replacement[0].outerHTML );
		},
		fixHemingwayEmptyButtons: function() {
			$( '.navigation-inner.section-inner .toggle-container .nav-toggle' ).append( '<span class="sr-only">Toggle Navigation</span>' );
			$( '.navigation-inner.section-inner .toggle-container .search-toggle' ).append( '<span class="sr-only">Toggle Search</span>' );
		}
	}
})( jQuery, OpenLab );

(function ($) {

	$( document ).ready(
		function () {
			OpenLab.fixes.init();
		}
	);

	$( window ).on( 'load',
		function () {
			OpenLab.fixes.onLoad();
		}
	);

	$( window ).scroll(
		function () {

			if ($( 'body' ).hasClass( 'masthead-fixing' )) {
				OpenLab.fixes.fixMasthead();
			}
		}
	);

})( jQuery );

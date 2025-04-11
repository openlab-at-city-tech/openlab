/*
 * 	Character Count Plugin - jQuery plugin
 * 	Dynamic character count for text areas and input fields
 *	written by Alen Grakalic	
 *	http://cssglobe.com/
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */

(function ($) {

    $.fn.charCount = function (options) {
        // default configuration properties
        var defaults = {
            allowed: 140,
			x_limit: 280,
			mastodon_limit: 500,
			bluesky_limit: 300,
            warning: 25,
            css: 'counter',
            counterElement: 'span',
            cssWarning: 'warning',
            cssExceeded: 'exceeded',
            counterText: ''
        };
        var options = $.extend(defaults, options);

        function calculate(obj) {
            var count   = $(obj).val().length;
			console.log( count );
			var allowed = options.allowed;
			// Service specific limits.
			var xAllowed        = options.x_limit;
			var mastodonAllowed = options.mastodon_limit;
			var blueskyAllowed  = options.bluesky_limit;
			// Set allowed length to highest of available options.
			allowed = Math.max( allowed, xAllowed, mastodonAllowed, blueskyAllowed );
			var minimum = Math.min( allowed, xAllowed, mastodonAllowed, blueskyAllowed );
            // supported shortcodes
            var urlcount     = $(obj).val().indexOf('#url#') > -1 ? 18 : 0;
            var longurlcount = $(obj).val().indexOf('#longurl#') > -1 ? 14 : 0;
			if ( $( '#title' ).length ) {
				var titlecount = $(obj).val().indexOf('#title#') > - 1 ? ( $('#title').val().length - 7 ) : 0;
			} else {
				var titlecount = 0;
			}
            var namecount = $(obj).val().indexOf('#blog#') > -1 ? ($('#wp-admin-bar-site-name a').val().length - 6) : 0;
			var length    = ( count + urlcount + longurlcount + titlecount + namecount )
            var available = allowed - length;

			if ( length >= xAllowed ) {
				$( '.x-notification' ).show();
				$( '.x-notification span' ).text( xAllowed );
			} else {
				$( '.x-notification' ).hide();
			}
			if ( length >= mastodonAllowed ) {
				$( '.mastodon-notification' ).show();
				$( '.mastodon-notification span' ).text( mastodonAllowed );
			} else {
				$( '.mastodon-notification' ).hide();
			}
			if ( length >= blueskyAllowed ) {
				$( '.bluesky-notification' ).show();
				$( '.bluesky-notification span' ).text( blueskyAllowed );
			} else {
				$( '.bluesky-notification' ).hide();
			}
			// Add aria-live when approaching first benchmark.
			if ( length >= ( minimum - options.warning ) ) {
				$(obj).next().attr( 'aria-live', 'polite' );
			} else {
				$(obj).next().removeAttr( 'aria-live', 'polite' );
			}
            if ( available <= options.warning && available >= 0 ) {
                $(obj).next().addClass(options.cssWarning);
            } else {
                $(obj).next().removeClass(options.cssWarning);
            }
            if ( available < 0 ) {
                $(obj).next().addClass(options.cssExceeded);
            } else {
                $(obj).next().removeClass(options.cssExceeded);
            }
            $(obj).next().html(options.counterText + available);
        };

        this.each(function () {
            $(this).after('<' + options.counterElement + ' aria-atomic="true" class="' + options.css + '">' + options.counterText + '</' + options.counterElement + '>');
			$(this).on( 'keyup', function() {
				setTimeout( calculate(this), 200 );
			});
			$(this).on( 'change', function() {
				setTimeout( calculate(this), 200 );
			});
        });

    };

})(jQuery);
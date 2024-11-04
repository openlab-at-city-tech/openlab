(function ($) {
	$('#wpt_custom_tweet, #wpt_retweet_0, #wpt_retweet_1, #wpt_retweet_3').charCount({
		allowed: wptSettings.allowed,
		counterText: wptSettings.text
	});
	// add custom retweets
	$('.wp-to-twitter .expandable').hide();
	$('.wp-to-twitter .tweet-toggle').on('click', function (e) {
		e.preventDefault();
		if ( $( '.wp-to-twitter .expandable' ).is( ':visible' ) ) {
			$( '.wp-to-twitter .tweet-toggle span ').addClass( 'dashicons-plus' );
			$( '.wp-to-twitter .tweet-toggle span' ).removeClass( 'dashicons-minus' );
		} else {
			$( '.wp-to-twitter .tweet-toggle span ').removeClass( 'dashicons-plus' );
			$( '.wp-to-twitter .tweet-toggle span' ).addClass( 'dashicons-minus' );
		}
		$('.wp-to-twitter .expandable').toggle('slow');
	});
	// tweet history log
	$('.wp-to-twitter .history').hide();
	$('.wp-to-twitter .history-toggle').on('click', function (e) {
		e.preventDefault();
		if ( $( '.wp-to-twitter .history' ).is( ':visible' ) ) {
			$( '.wp-to-twitter .history-toggle span ').addClass( 'dashicons-plus' );
			$( '.wp-to-twitter .history-toggle span' ).removeClass( 'dashicons-minus' );
		} else {
			$( '.wp-to-twitter .history-toggle span ').removeClass( 'dashicons-plus' );
			$( '.wp-to-twitter .history-toggle span' ).addClass( 'dashicons-minus' );
		}
		$('.wp-to-twitter .history').toggle('slow');
	});

	const templateTags = document.querySelectorAll( '#wp2t .inline-list button' );
	let   custom       = document.getElementById( 'wpt_custom_tweet' );
	let   template     = document.querySelector( '#wp2t .wpt-template code' );
	let   customText   = custom.value;
	let   templateText = template.innerText;
	templateTags.forEach((el) => {
		el.addEventListener( 'click', function(e) {
			let pressed  = el.getAttribute( 'aria-pressed' );
			let tag      = el.innerText;
			templateText = ( customText ) ? customText : templateText;
			if ( 'true' === pressed ) {
				let newText  = templateText.replace( tag, '' ).trim();
				templateText = newText;
				custom.value = newText;
				el.setAttribute( 'aria-pressed', 'false' );
			} else {
				templateText = templateText + ' ' + tag;
				custom.value = templateText;
				el.setAttribute( 'aria-pressed', 'true' );			
			}
			wp.a11y.speak( wptSettings.updated );
		});
	});
}(jQuery));
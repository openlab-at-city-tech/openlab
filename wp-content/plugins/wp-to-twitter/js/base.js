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
}(jQuery));
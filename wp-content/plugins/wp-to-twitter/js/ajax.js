(function ($) {
	$(function () {
		$('.wpt_log, #wpt_set_tweet_time').hide();
		$('button.time').on("click", function (e) {
				e.preventDefault();
				if ($('#wpt_set_tweet_time').is(":visible")) {
					$('#wpt_set_tweet_time').hide(250);
					$('button.schedule').attr('disabled', 'disabled');
				} else {
					$('#wpt_set_tweet_time').show(250);
					$('#wpt_date').focus();
					$('button.schedule').removeAttr('disabled');
				}
			}
		);
		$('button.tweet').on('click', function (e) {
			visible = $( '.wpt_log' ).is( ':visible' );
			if ( visible ) {
				$( '.wpt_log' ).hide( 200 );
			}
			e.preventDefault();
			var text   = $('#wpt_custom_tweet').val();
			var date   = $('#wpt_set_tweet_time .date').val();
			var time   = $('#wpt_set_tweet_time .time').val();
			var auth   = $('#wpt_authorized_users').val();
			
			var upload = $('input:radio[name=_wpt_image]:checked').val();
			var tweet_action = ( $(this).attr('data-action') === 'tweet' ) ? 'tweet' : 'schedule'
			var data = {
				'action': wpt_data.action,
				'tweet_post_id': wpt_data.post_ID,
				'tweet_text': text,
				'tweet_schedule': date + ' ' + time,
				'tweet_action': tweet_action,
				'tweet_auth': auth,
				'tweet_upload': upload,
				'security': wpt_data.security
			};
			$.post(ajaxurl, data, function (response) {
				$('.wpt_log').text(response).show(500);
			});
		});
	});
}(jQuery));
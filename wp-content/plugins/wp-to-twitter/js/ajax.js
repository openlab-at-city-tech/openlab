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
			let visible = $( '.wpt_log' ).is( ':visible' );
			if ( visible ) {
				$( '.wpt_log' ).hide( 200 );
			}
			let text = $('#wpt_custom_tweet').val();
			let date = $('#wpt_set_tweet_time .date');
			console.log( date );
				date = ( 0 !== date.length ) ? date.val() : '';
			let time = $('#wpt_set_tweet_time .time');
				time = ( 0 !== time.length ) ? time.val() : '';
			let auth = $('#wpt_authorized_users');
				auth = ( 0 !== auth.length ) ? auth.val() : '';

			let upload   = $('input:radio[name=_wpt_image]:checked');
				upload   = ( 0 !== upload.length ) ? upload.val() : '';
			let image_id = $( 'input[name=_wpt_custom_image]');
				image_id = ( 0 !== image_id.length ) ? image_id.val() : '';

			let omit_service_input = $( 'input[name="_wpt_omit_services[]"]:checked' );
			let omit_services      = new Array();
			if ( 0 !== omit_service_input.length ) {
				$.each( omit_service_input, function() {
					omit_services.push( $(this).val() );
				});
			} else {
				// If only one service enabled, this is a hidden input instead.
				omit_services.push( $( 'input[name="_wpt_omit_services[]"]' ).val() );
			}
			let custom_x_update = $( '#wpt_custom_tweet_x' ).val();
			let custom_mastodon_update = $( '#wpt_custom_tweet_mastodon' ).val();
			let custom_bluesky_update = $( '#wpt_custom_tweet_bluesky' ).val();
			let tweet_action = ( $(this).attr('data-action') === 'tweet' ) ? 'tweet' : 'schedule'
			let data = {
				'action': wpt_data.action,
				'tweet_post_id': wpt_data.post_ID,
				'tweet_text': text,
				'x_text': custom_x_update,
				'mastodon_text': custom_mastodon_update,
				'bluesky_text': custom_bluesky_update,
				'tweet_schedule': date + ' ' + time,
				'tweet_action': tweet_action,
				'tweet_auth': auth,
				'tweet_upload': upload,
				'image_id': image_id,
				'omit': omit_services,
				'security': wpt_data.security
			};
			console.log( data );
			$.post(ajaxurl, data, function (response) {
				$('.wpt_log').text(response).show(300);
			});
		});
	});
}(jQuery));
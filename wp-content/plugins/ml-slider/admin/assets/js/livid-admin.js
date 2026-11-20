/**
 * Small admin script for the Livid slide type - wires up the "duplicate slide"
 * button and the video URL field, since both actions are registered
 * per-slide-type (metaslider_livid) rather than through the shared admin.js
 * handler used by image slides.
 */
(function ($) {
	'use strict'

	function notify(method, id, message) {
		var APP = window.metaslider && window.metaslider.app && window.metaslider.app.MetaSlider
		APP && APP[method](id, message, true)
	}

	$(document).ready(function () {
		// Refresh the cached embed and the slide thumbnail as soon as the URL
		// changes, rather than waiting for the slideshow to be saved - same flow
		// as Pro's YouTube slide type.
		$(document).on('change', 'tr.slide.livid input.livid_url', function (event) {
			var $field = $(event.target)
			var url = $.trim($field.val())

			if (!url) {
				return
			}

			// The server re-validates the host; this only avoids a pointless
			// round trip on an obviously wrong link.
			if (url.indexOf('livid.com') === -1) {
				notify('notifyError', 'metaslider/livid-embed-not-updated', metaslider_livid.error_invalid_url)
				return
			}

			notify('notifyInfo', 'metaslider/updating-livid-embed', metaslider_livid.updating_embed)

			$.post(metaslider_livid.ajaxurl, {
				action: 'update_livid_embed',
				_wpnonce: metaslider_livid.update_embed_nonce,
				slide_id: $field.data('slide-id'),
				url: url
			}).done(function (response) {
				if (!response || !response.success) {
					var message = response && response.data && response.data.message
					notify('notifyError', 'metaslider/livid-embed-not-updated', message || metaslider_livid.error_update_failed)
					return
				}

				// No thumbnail in the response just means Livid gave us none -
				// the embed itself still updated.
				if (response.data.thumbnail_url_small) {
					var $thumb = $('#slide-' + response.data.slide_id + ' .thumb').find('img')
					$thumb.attr(
						'srcset',
						response.data.thumbnail_url_large + ' 1024w, ' +
							response.data.thumbnail_url_medium + ' 768w, ' +
							response.data.thumbnail_url_small + ' 240w'
					)
					$thumb.attr('src', response.data.thumbnail_url_small)
				}

				notify('notifySuccess', 'metaslider/livid-embed-updated', metaslider_livid.embed_updated)
			}).fail(function () {
				notify('notifyError', 'metaslider/livid-embed-not-updated', metaslider_livid.error_update_failed)
			})
		})

		$('.metaslider').on('click', '.duplicate-slide-livid', function (event) {
			event.preventDefault()
			var $this = $(this)
			var data = {
				action: 'duplicate_livid_slide',
				_wpnonce: metaslider_livid.duplicate_slide_nonce,
				slide_id: $this.data('slide-id'),
				slider_id: window.parent.metaslider_slider_id
			}

			$.ajax({
				url: metaslider_livid.ajaxurl,
				data: data,
				type: 'POST',
				error: function (error) {
					var message = (error && error.responseJSON && typeof error.responseJSON.data === 'string' && error.responseJSON.data) || metaslider_livid.error_duplicate_failed
					window.metaslider && window.metaslider.app && window.metaslider.app.MetaSlider.notifyError('metaslider/slide-duplicate-failed', message, true)
				},
				success: function (response) {
					window.metaslider.app.mountNewSlides([response.data])
					window.metaslider.app.scrollToSlide(response.data.slide_id)
				}
			})
		})
	})
})(window.jQuery)

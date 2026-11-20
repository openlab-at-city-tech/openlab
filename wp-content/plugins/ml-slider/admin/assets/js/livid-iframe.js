/**
 * Runs inside the "Livid" tab's wp_iframe in the Add Slide media manager.
 * Validates the pasted link, shows a live preview of the embed (fetched from
 * the server, since - unlike Vimeo/YouTube - we don't hardcode Livid's
 * iframe URL pattern), then creates the slide and hands the rendered row
 * back to the main admin page through metaslider.after_adding_slide_success().
 */
(function ($) {
	'use strict'

	var PREVIEW_DEBOUNCE_MS = 600

	/**
	 * Is this a livid.com video link? The server re-checks the host before it
	 * fetches anything, so this is only here to give quick feedback.
	 *
	 * @param {string} url URL
	 * @return {boolean}
	 */
	function isLividUrl(url) {
		if (!url || !url.trim()) {
			return false
		}

		var host
		try {
			host = new URL(url).hostname.toLowerCase()
		} catch (e) {
			return false
		}

		return 'livid.com' === host || 'www.livid.com' === host
	}

	$(function () {
		var previewTimer = null
		var lastPreviewedUrl = null

		var showSpinner = function () {
			$('.spinner').show()
		}

		var hideSpinner = function () {
			$('.spinner').hide()
		}

		// An <a> ignores disabled, so aria-disabled is what screen readers and the click guard rely on.
		var setAddButtonEnabled = function (enabled) {
			if (enabled) {
				$('.media-button').removeAttr('disabled').attr('aria-disabled', 'false')
			} else {
				$('.media-button').attr({ disabled: 'disabled', 'aria-disabled': 'true' })
			}
		}

		/**
		 * Put the tab back the way it loaded. The media modal keeps this iframe
		 * around between opens, so without this the last URL added is still in
		 * the field the next time the tab is shown.
		 */
		var resetForm = function () {
			clearTimeout(previewTimer)
			lastPreviewedUrl = null
			hideSpinner()
			$('.livid_url').val('')
			$('.embed-link-settings').html('')
			setAddButtonEnabled(false)
		}

		var showError = function (message) {
			$('.embed-link-settings').html('<div class="ms-invalid-input-error">' + message + '</div>')
			setAddButtonEnabled(false)
		}

		var requestPreview = function (url) {
			showSpinner()

			$.post(ajaxurl, {
				action: 'livid_preview_embed',
				url: url,
				_wpnonce: metaslider_livid_iframe.nonce
			}).done(function (response) {
				hideSpinner()

				// Ignore a stale response if the field has since changed
				if (url !== $('.livid_url').val()) {
					return
				}

				$('.embed-link-settings').html('<div class="livid-preview">' + response.data.html + '</div>')
				setAddButtonEnabled(true)
			}).fail(function (xhr) {
				hideSpinner()

				if (url !== $('.livid_url').val()) {
					return
				}

				showError((xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) || metaslider_livid_iframe.error_invalid_url)
			})
		}

		$('.livid_url').on('propertychange keyup input paste', function () {
			var value = $(this).val()

			// Keys that leave the URL as is (arrows, Shift, Tab) must not disable a ready button.
			if (value === lastPreviewedUrl) {
				return
			}

			clearTimeout(previewTimer)
			setAddButtonEnabled(false)

			if (!value.length) {
				hideSpinner()
				$('.embed-link-settings').html('')
				return
			}

			if (!isLividUrl(value)) {
				hideSpinner()
				showError(metaslider_livid_iframe.error_invalid_url)
				return
			}

			previewTimer = setTimeout(function () {
				lastPreviewedUrl = value
				requestPreview(value)
			}, PREVIEW_DEBOUNCE_MS)
		})

		$('body').on('click', '.media-button', function (event) {
			event.preventDefault()

			var url = $('.livid_url').val()
			if (!isLividUrl(url) || $(this).attr('aria-disabled') === 'true') {
				return
			}

			var APP = window.parent.metaslider.app.MetaSlider
			APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
				APP.__('Preparing %s slide...', 'ml-slider'),
				'1'
			), true)

			setAddButtonEnabled(false)

			$.post(ajaxurl, {
				action: 'create_livid_slide',
				url: url,
				slider_id: window.parent.metaslider_slider_id,
				_wpnonce: metaslider_livid_iframe.nonce
			}).done(function (response) {
				resetForm()
				window.parent.metaslider.after_adding_slide_success(response.data)
			}).fail(function (xhr) {
				var error = xhr.responseJSON && xhr.responseJSON.data
				// A refused slideshow sends a plain message, other errors send { message }
				showError((typeof error === 'string' && error) || (error && error.message) || metaslider_livid_iframe.error_invalid_url)
				// showError() disables the button, but the URL is still valid so allow a retry.
				setAddButtonEnabled(true)
			})
		})
	})
})(jQuery)

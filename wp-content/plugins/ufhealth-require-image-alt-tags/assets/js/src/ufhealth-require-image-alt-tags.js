/**
 * UF Health Require Image Alt Tags
 * https://ufhealth.org/
 *
 * Copyright (c) 2017 UF Health
 * Licensed under the GPLv2+ license.
 */
jQuery(document).ready(function ($) {
	'use strict';

	/**
	 * Checks the media form for proper ALT text
	 *
	 * @since 1.0
	 *
	 * @param showNotice
	 *
	 * @returns {boolean}
	 */
	var checkForAlt = function (showNotice) {

		var notice         = ('undefined' !== typeof showNotice) ? showNotice : false,
		    $parent        = $('.media-frame-toolbar .media-toolbar-primary'),
		    selectedImages = $('.selection-view ul.attachments li'),
		    canProceed     = true,
		    badImages      = [];

		if (0 === selectedImages.length) {

			var $image = $('.attachment-details').attr('data-id'),
			    altText;

			// Handle image uploads if there is a multi-select box (normal image insertion.
			if ('undefined' !== typeof $image) {

				var image = wp.media.model.Attachment.get($image);

				altText = image.get('alt');

			} else { // Handle featured image, replace image, etc.

				// Different forms have different markup so attempt to address accordingly.
				var hasLabel = $('.media-modal-content label[data-setting="alt"] input'),
				    noLabel  = $('.media-frame-content input[data-setting="alt"]');

				if (hasLabel.length && 0 < hasLabel.length) {

					altText = hasLabel.val();

				} else {

					altText = noLabel.val();

				}
			}

			if ('undefined' !== typeof altText && altText.length && 0 < altText.length) {

				$parent.addClass('ufh-has-alt-text');

				return true;

			}

			// Remove the mask that allows the button to be pushed.
			$parent.removeClass('ufh-has-alt-text');

			if (notice) {
				alert(ufhTagsCopy.editTxt);
			}

			return false;

		} else {

			selectedImages.each(function (idx, li) {

				var $image  = $(li),
				    imageId = $image.attr('data-id'),
				    image   = wp.media.model.Attachment.get(imageId),
				    altText = image.get('alt');

				if (('undefined' !== typeof altText && altText.length) || 'image' !== image.get('type')) {

					$parent.addClass('ufh-has-alt-text');
					$image.removeClass('ufh-needs-alt-text');

				} else {

					$image.addClass('ufh-needs-alt-text');

					badImages.push(image.get('title'));

					canProceed = false;

				}
			});

			if (false === canProceed) {

				$parent.removeClass('ufh-has-alt-text');

				if (notice) {

					var imageList = '\n\n';

					for (var i = 0, l = badImages.length; i < l; i++) {
						imageList = imageList + badImages[i] + '\n\n';
					}

					alert(ufhTagsCopy.disclaimer + '\n\n' + ufhTagsCopy.txt + ':' + imageList);

				}

				return false;

			}

			return true;

		}
	};

	var body = $('body');

	// Bind to keyup.
	body.on('keyup', '.media-modal-content label[data-setting="alt"] input, .media-frame-content input[data-setting="alt"]', function () {
		checkForAlt();
	});

	// Bind to the 'Insert into post' button.
	body.on('mouseenter mouseleave click', '.media-frame-toolbar .media-toolbar-primary', function (e) {
		checkForAlt(e.type === 'click');
	});

});

/* global document, confirm */
/* jshint unused:false, newcap: false */
jQuery(document).ready(function ($) {

	// Delete multiple images from slider
	$('a.soliloquy-slides-delete').click(function (e) {

		//Prevent Default
		e.preventDefault();

		// Bail out if the user does not actually want to remove the image.
		var confirm_delete = confirm(soliloquy_metabox_local.remove_multiple);
		if (!confirm_delete) {
			return false;
		}


		// Build array of image attachment IDs
		var attach_ids = [];

		//Get all selectd Images
		$('ul#soliloquy-output > li.selected').each(function () {

			attach_ids.push($(this).attr('id'));

		});

		// Prepare our data to be sent via Ajax.
		var remove = {
			action: 'soliloquy_remove_slides',
			attachment_ids: attach_ids,
			post_id: soliloquy_metabox_local.id,
			nonce: soliloquy_metabox_local.remove_nonce
		};

		// Process the Ajax response and output all the necessary data.
		$.post(
			soliloquy_metabox_local.ajax,
			remove,
			function (response) {

				// Remove each image
				$('ul#soliloquy-output > li.selected').remove();

				//Hide Bulk Actions
				$('.soliloquy-bulk-actions').fadeOut();

				//Uncheck the checkbox
				$('.soliloquy-select-all').prop('checked', false);

				// Refresh the modal view to ensure no items are still checked if they have been removed.
				$('.soliloquy-load-library').attr('data-soliloquy-offset', 0).addClass('has-search').trigger('click');

				// Repopulate the Soliloquy Slide Collection
				SoliloquySlidesUpdate(false);

				//Get Slide Count
				var list = $('#soliloquy-output li').length;

				//Update the slide count text
				$('.soliloquy-count').text(list.toString());

				//If there are no slides
				if (list === 0) {

					//Make sure bulk actions are out of view
					$('.soliloquy-bulk-actions').fadeOut();

					//Fade out Settings header
					$('.soliloquy-slide-header').fadeOut().addClass('soliloquy-hidden');

					//Add Empty Slider Content
					$('#soliloquy-empty-slider').removeClass('soliloquy-hidden').fadeIn();

				}

			},
			'json'
		);
	});

	// Process image removal from a gallery.
	$('#soliloquy-settings-content ').on('click', '.soliloquy-remove-slide', function (e) {
		e.preventDefault();

		// Bail out if the user does not actually want to remove the image.
		var confirm_delete = confirm(soliloquy_metabox_local.remove);
		if (!confirm_delete) {

			return;
		}

		// Prepare our data to be sent via Ajax.
		var attach_id = $(this).parent().attr('id'),
			remove = {
				action: 'soliloquy_remove_slide',
				attachment_id: attach_id,
				post_id: soliloquy_metabox_local.id,
				nonce: soliloquy_metabox_local.remove_nonce
			};

		// Process the Ajax response and output all the necessary data.
		$.post(
			soliloquy_metabox_local.ajax,
			remove,
			function (response) {
				$('#' + attach_id).fadeOut('normal', function () {
					$(this).remove();

					// Refresh the modal view to ensure no items are still checked if they have been removed.
					$('.soliloquy-load-library').attr('data-soliloquy-offset', 0).addClass('has-search').trigger('click');

					// Repopulate the Soliloquy Image Collection
					SoliloquySlidesUpdate(false);

					//Get the slide count
					var list = $('#soliloquy-output li').length;

					//Update Slide Count
					$('.soliloquy-count').text(list.toString());

					if (list === 0) {

						//Make sure bulk actions are out of view
						$('.soliloquy-bulk-actions').fadeOut();

						//Fade out Settings header
						$('.soliloquy-slide-header').fadeOut().addClass('soliloquy-hidden');

						//Add Empty Slider Content
						$('#soliloquy-empty-slider').removeClass('soliloquy-hidden').fadeIn();
					}

				});
			},
			'json'
		);
	});
});
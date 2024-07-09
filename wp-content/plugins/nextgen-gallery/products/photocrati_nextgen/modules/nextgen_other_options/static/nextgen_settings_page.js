jQuery(function($) {

	$('select.select2').select2();
	$('label.tooltip, span.tooltip').tooltip();

	let watermark_source = $('#watermark_source');
	let watermark_text = $('#watermark_text');
	let watermark_image_url = $('#watermark_image_url');
	let watermark_customization = $('#watermark_customization');
	let refresh_button = $('#nextgen_settings_preview_refresh');
	let ngg_errors_in_tab = $('#ngg_errors_in_tab');

	let checkWatermarkText = function() {
		if (watermark_source.val() === 'text') {
			watermark_customization.show();
			watermark_text.attr('required', 'required'	);
			watermark_image_url.removeAttr('required');
			if ($.trim(watermark_text.val()).length > 0) {
				watermark_text.css({
					'border-color': '',
					'border-width': ''
				});
				ngg_errors_in_tab.val('');
				refresh_button.prop('disabled', false).html('Refresh preview image');
			} else {
				watermark_text.css({
					'border-color': 'red',
					'border-width': '1px'
				});
				refresh_button.prop('disabled', true).html('Enter watermark text...');
				ngg_errors_in_tab.val('Watermark text is a required field.');
			}
		}
	}

	let checkWatermarkImage = function() {
		if (watermark_source.val() === 'image') {
			watermark_customization.show();
			watermark_image_url.attr('required', 'required'	);
			watermark_text.removeAttr('required');
			// check if the value is not empty and a valid file path or url.
			if (watermark_image_url.val() !== ''){
				refresh_button.prop('disabled', false).html('Refresh preview image');
				watermark_image_url.css({
					'border-color': '',
					'border-width': ''
				});
				ngg_errors_in_tab.val('');
			} else {
				refresh_button.prop('disabled', true).html('Enter watermark image URL...');
				watermark_image_url.css({
					'border-color': 'red',
					'border-width': '1px'
				});
				ngg_errors_in_tab.val('Watermark URL is a required field.');
			}
		}
	}

	/**** LIGHTBOX EFFECT TAB ****/
	$('#lightbox_library').on('change', function() {
		var value = $(this).find(':selected').val();
		var id = 'lightbox_library_' + value;
		$('.lightbox_library_settings').each(function() {
			if ($(this).attr('id') != id) $(this).fadeOut('fast');
		});
		$('#' + id).fadeIn();
	}).trigger('change');

	/**** WATERMARK TAB ****/

	// Configure the watermark customization link
	watermark_customization.attr('rel', 'watermark_' + watermark_source.val() + '_source');

	// Configure the button to switch from watermark text to image
	watermark_source.on('change', function() {
		$('#' + watermark_customization.attr('rel')).css('display', '').addClass('hidden');
		if (!$('#' + $(this).val()).hasClass('hidden')) {
			$('#' + $(this).val()).removeClass('hidden');
		}
		watermark_customization.attr('rel', 'watermark_' + watermark_source.val() + '_source').trigger('click');
	});

	let showWatermarkFields = function(type) {
		$('.watermark_field').each(function() {
			type === 'show' ? $(this).fadeIn().removeClass('hidden') : $(this).fadeOut().addClass('hidden');
		});
	}

	// Don't show any Watermark fields unless Watermarks are enabled
	watermark_source.on('change', function() {
		var value = $(this).val();

		switch(value) {
			case 'text':
				showWatermarkFields('show');
				checkWatermarkText();
				break;
			case 'image':
				showWatermarkFields('show');
				checkWatermarkImage();
				break;
			default:
				showWatermarkFields('hide');
		}

	}).trigger('change');



	watermark_text.on('blur', function() {
		checkWatermarkText();
	});

	watermark_image_url.on('blur', function() {
		checkWatermarkImage();
	});



	// sends the current settings to a special ajax endpoint which saves them, regenerates the url, and then reverts
	// to the old settings. this submits the form and forces a refresh of the image through the time parameter
	refresh_button.on('click', function(event) {
		event.preventDefault();

		var form = $(this).parents('form:first');
		var self = $(this);
		var orig_html = $(self).html();

		$(self).attr('disabled', 'disabled').html('Processing...');
		$('body').css('cursor', 'wait');

		$.ajax({
			type: form.attr('method'),
			url: $(this).data('refresh-url'),
			data: form.serialize() + "&action=get_watermark_preview_url",
			dataType: 'json',
			success: function(data) {
				var img = self.prev();
				var src = data.thumbnail_url;
				queryPos = src.indexOf('?');
				if (queryPos != -1) {
					src = src.substring(0, queryPos);
				}

				img.attr('src', src + '?' + new Date().getTime());
				$(self).prop('disabled', false).html(orig_html);
				$('body').css('cursor', 'default');
			},
			error: function(xob, err, code) {
				$(self).prop('disabled', false).html(orig_html);
				$('body').css('cursor', 'default');
			}
		});

		return false;
	});

});

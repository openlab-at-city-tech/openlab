/**
 * Hooks into the global Plupload instance ('uploader'), which is set when includes/admin/metaboxes.php calls media_form()
 * We hook into this global instance and apply our own changes during and after the upload.
 *
 * @since 1.3.1.3
 */
(function ($, window, document, soliloquy_media_uploader) {
	$(function () {
		if (typeof uploader !== 'undefined') {
			function isHeicUploadable(file) {
				if (file.name.toLowerCase().endsWith('.heic') && !soliloquy_media_uploader.is_imagick_enabled) {
					return false
				}
				return true
			}

			//soliloquy_media_uploader.uploader_files_computer
			$('input#plupload-browse-button').val(
				soliloquy_media_uploader.uploader_files_computer,
			);
			$('.drag-drop-info').text(
				soliloquy_media_uploader.uploader_info_text,
			);

			// Set a custom progress bar
			$('#soliloquy .drag-drop-inside').append(
				'<div class="soliloquy-progress-bar"><div></div></div>',
			);
			var soliloquy_bar = $('#soliloquy .soliloquy-progress-bar'),
				soliloquy_progress = $(
					'#soliloquy .soliloquy-progress-bar div',
				),
				soliloquy_output = $('#soliloquy-output');

			// Files Added for Uploading
			uploader.bind('FilesAdded', function (up, files) {
				up.files = files;
				$(soliloquy_bar).fadeIn();
			});
			uploader.bind('BeforeUpload', function (up, file) {
				if (!isHeicUploadable(file)) {
					$('#soliloquy-upload-error').html(
						`<div class="error fade"><p>${soliloquy_media_uploader.heic_error_text}</p></div>`
					);
					return false
				}
			});

			// File Uploading - show progress bar
			uploader.bind('UploadProgress', function (up, file) {
				$(soliloquy_progress).css({
					width: up.total.percent + '%',
				});
			});

			// File Uploaded - AJAX call to process image and add to screen.
			uploader.bind('FileUploaded', function (up, file, info) {
				// AJAX call to soliloquy to store the newly uploaded image in the meta against this Gallery
				$.post(
					soliloquy_media_uploader.ajax,
					{
						action: 'soliloquy_load_image',
						nonce: soliloquy_media_uploader.load_image,
						id: info.response,
						post_id: soliloquy_media_uploader.id,
					},
					function (res) {
						// Prepend or append the new image to the existing grid of images,
						// depending on the media_position setting
						switch (soliloquy_media_uploader.media_position) {
							case 'before':
								$(soliloquy_output).prepend(res);
								break;
							case 'after':
							default:
								$(soliloquy_output).append(res);
								break;
						}

						$(document).trigger('soliloquyUploaded');

						$(res)
							.find('.wp-editor-container')
							.each(function (i, el) {
								const id = $(el).attr('id').split('-')[4];
								quicktags({
									id: 'soliloquy-caption-' + id,
									buttons: 'strong,em,link,ul,ol,li,close',
								});
								QTags._buttonsInit(); // Force buttons to initialize.
							});


						//How many slides are inserted into slider
						const list = $('#soliloquy-output li').length;

						//update the count value
						$('.soliloquy-count').text(list.toString());

						//Hides empty slider screen
						if (list > 0) {

							$('#soliloquy-empty-slider').fadeOut().addClass('soliloquy-hidden');
							$('.soliloquy-slide-header').removeClass('soliloquy-hidden').fadeIn();

						}
					},
					'json',
				);
			});

			// Files Uploaded
			uploader.bind('UploadComplete', function () {
				// Hide Progress Bar
				$(soliloquy_bar).fadeOut();
			});

			// File Upload Error
			uploader.bind('Error', function (up, err) {
				// Show message
				$('#soliloquy-upload-error').html(
					'<div class="error fade"><p>' +
					err.file.name +
					': ' +
					err.message +
					'</p></div>',
				);
				up.refresh();
			});
		}
	});
})(jQuery, window, document, soliloquy_media_uploader);

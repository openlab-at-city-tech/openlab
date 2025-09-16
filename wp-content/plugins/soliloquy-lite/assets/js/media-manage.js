/**
* Handles Mangement functions, deselection and sorting of media in an Soliloquy slider
*/
; (function ($, window, document, soliloquy_metabox_local) {

	"use strict";

	var soliloquy_manage = {

		//Init, Triggers all functions, used as callback on JS events.
		init: function () {

			this.select_all();

			this.sortable();

			this.select();

			this.display();

			this.chosen();

			this.slide_size();

			this.uploadImage();

			this.toggleStatus();

			this.tooltip();

			this.clear_selected();

			//Init the Clipboard
			new Clipboard('.soliloquy-clipboard');

			//Prevent Default Action on check
			$('ul#soliloquy-output').on('click', 'a.check', function (e) {

				e.preventDefault();

			});

			//Prevent Default on Clipboard
			$('.soliloquy-clipboard').on('click', function (e) {
				e.preventDefault();
			});

			//How many slides are in the list
			var list = $('#soliloquy-output li').length;

			//Set the Count
			$('.soliloquy-count').text(list.toString());

			// Initialise conditional fields
			$('input,select').conditional();

		},

		//Toggles slides status
		toggleStatus: function () {

			$('#soliloquy-settings-content').on('click.soliloquyStatus', '.soliloquy-slide-status', function (e) {

				var $parent = '',
					$status = '';

				// Prevent default action
				e.preventDefault();
				if ($(this).hasClass('list-status')) {
					$parent = $(this).parent().parent().parent();
				} else {
					$parent = $(this).parent();
				}

				var $this = $(this),
					$data = $this.data('status'),
					$list_view = $parent.find('.soliloquy-slide-status.list-status'),
					$grid_view = $parent.find('.soliloquy-slide-status.grid-status'),
					$view = $this.parent().parent().data('view'),
					id = $this.data('id'),
					$icon = $grid_view.find('span.dashicons'),
					$text = $list_view.find('span'),
					$tooltip = $this.data('soliloquy-tooltip');

				//Set the slider sta
				if ($data === 'active') {
					$status = 'pending';
				} else {
					$status = 'active';

				}

				var opts = {
					url: soliloquy_metabox_local.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'soliloquy_change_slide_status',
						post_id: soliloquy_metabox_local.id,
						slide_id: id,
						status: $status,
						nonce: soliloquy_metabox_local.save_nonce
					},
					success: function (response) {

						if ($status === 'active') {
							//Toggle Classes
							$grid_view.removeClass('soliloquy-draft-slide').addClass('soliloquy-active-slide');
							$list_view.removeClass('soliloquy-draft-slide').addClass('soliloquy-active-slide');

							//Set the proper icons
							$icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');

							//Set the Text
							$text.text(soliloquy_metabox_local.active);
							$grid_view.attr('data-soliloquy-tooltip', soliloquy_metabox_local.active);

							//Set the Data
							$list_view.data('status', 'active');
							$grid_view.data('status', 'active');

						} else {

							//Toggle Classes
							$grid_view.removeClass('soliloquy-active-slide').addClass('soliloquy-draft-slide');
							$list_view.removeClass('soliloquy-active-slide').addClass('soliloquy-draft-slide');

							//Set the proper icons
							$icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');

							//Set the text
							$text.text(soliloquy_metabox_local.draft);
							//Set the Data
							$list_view.data('status', 'pending');
							$grid_view.data('status', 'pending');
							$grid_view.attr('data-soliloquy-tooltip', soliloquy_metabox_local.draft);

						}

					},
					error: function (xhr, textStatus, e) {
						return;
					}
				};
				$.ajax(opts);


			});
		},
		tooltip: function () {
			$('[data-soliloquy-tooltip]').on('mouseover', function (e) {
				e.preventDefault();
				var $this = $(this),
					$data = $this.data('soliloquy-tooltip');


			});
		},
		//Select all slides
		select_all: function () {

			//Select all
			$('.soliloquy-select-all').change(function () {

				var checked = this.checked;

				if (checked) {
					$('ul#soliloquy-output li').addClass('selected');
					$('.soliloquy-bulk-actions').fadeIn();

					var selected = $('ul#soliloquy-output li.selected').length;

					$('.select-all').text(soliloquy_metabox_local.selected);
					$('.soliloquy-count').text(selected.toString());
					$('.soliloquy-clear-selected').fadeIn();


				} else {

					$('ul#soliloquy-output li').removeClass('selected');
					$('.soliloquy-bulk-actions').fadeOut();

					var list = $('ul#soliloquy-output li').length;

					$('.select-all').text(soliloquy_metabox_local.select_all);
					$('.soliloquy-count').text(list.toString());
					$('.soliloquy-clear-selected').fadeOut();

				}

			});
		},

		//Makes Slides Sortable
		sortable: function () {
			// Make slider items sortable.
			var slider = $('#soliloquy-output');

			slider.sortable({
				containment: '#soliloquy-slider-main',
				items: 'li',
				cursor: 'move',
				forcePlaceholderSize: true,
				placeholder: 'dropzone',
				update: function (event, ui) {
					// Make ajax request to sort out items.
					var opts = {
						url: soliloquy_metabox_local.ajax,
						type: 'post',
						async: true,
						cache: false,
						dataType: 'json',
						data: {
							action: 'soliloquy_sort_images',
							order: slider.sortable('toArray').toString(),
							post_id: soliloquy_metabox_local.id,
							nonce: soliloquy_metabox_local.sort
						},
						success: function (response) {
							// Repopulate the Soliloquy slider Slide Collection
							SoliloquySlidesUpdate();

							return;
						},
						error: function (xhr, textStatus, e) {
							return;
						}
					};
					$.ajax(opts);
				}
			});
		},

		//Select a slide
		select: function () {

			// Select / deselect images
			var soliloquy_shift_key_pressed = false,
				soliloquy_last_selected_image = false;

			$('li.soliloquy-slide .soliloquy-item-content, .soliloquy-list li a.check').on('click', function (e) {

				var $this = $(this),
					slider_item = $(this).parent(),
					selected;

				e.preventDefault();

				if ($(slider_item).hasClass('selected')) {

					$(slider_item).removeClass('selected');

					soliloquy_last_selected_image = false;

					selected = $('ul#soliloquy-output li.selected').length;

					if (selected !== 0) {

						$('.select-all').text(soliloquy_metabox_local.selected);
						$('.soliloquy-count').text(selected.toString());
						$('.soliloquy-clear-selected').fadeIn();


					} else {

						var list = $('ul#soliloquy-output li').length;

						$('.select-all').text(soliloquy_metabox_local.select_all);
						$('.soliloquy-count').text(list.toString());
						$('.soliloquy-clear-selected').fadeOut();

					}

				} else {

					// If the shift key is being held down, and there's another image selected, select every image between this clicked image
					// and the other selected image
					if (soliloquy_shift_key_pressed && soliloquy_last_selected_image !== false) {

						// Get index of the selected image and the last image
						var start_index = $('ul#soliloquy-output li').index($(soliloquy_last_selected_image)),
							end_index = $('ul#soliloquy-output li').index($(slider_item)),
							i = 0;

						// Select images within the range
						if (start_index < end_index) {
							for (i = start_index; i <= end_index; i++) {
								$('ul#soliloquy-output li:eq( ' + i + ')').addClass('selected');
							}
						} else {
							for (i = end_index; i <= start_index; i++) {
								$('ul#soliloquy-output li:eq( ' + i + ')').addClass('selected');
							}
						}
					}

					// Select the clicked image
					$(slider_item).addClass('selected');
					soliloquy_last_selected_image = $(slider_item);

					selected = $('ul#soliloquy-output li.selected').length;
					$('.soliloquy-clear-selected').fadeIn();

					$('.select-all').text(soliloquy_metabox_local.selected);
					$('.soliloquy-count').text(selected.toString());
				}

				// Show/hide 'Deleted Selected Images from Slider' button depending on whether
				// any slides have been selected
				if ($('ul#soliloquy-output > li.selected').length > 0) {

					$('.soliloquy-bulk-actions').fadeIn();

				} else {

					$('.soliloquy-bulk-actions').fadeOut();
				}

			});

			// Determine whether the shift key is pressed or not
			$(document).on('keyup keydown', function (e) {

				soliloquy_shift_key_pressed = e.shiftKey;

			});
		},

		//Updates slider dimenisons on Config screen
		slide_size: function () {
			// Set size of slider dimension fields when changing size type.
			$(document).on('change', '#soliloquy-config-slider-size', function () {
				var $this = $(this),
					value = $this.val(),
					width = $this.find(':selected').data('soliloquy-width'),
					height = $this.find(':selected').data('soliloquy-height');

				// Do nothing if the default value is the new value.
				if ('default' == value) {

					$('#soliloquy-config-slider-width').val(soliloquy_metabox_local.slide_width);
					$('#soliloquy-config-slider-height').val(soliloquy_metabox_local.slide_height);
				}

				// Otherwise, attempt to grab width/height data and apply it to dimensions.
				if (width) {
					$('#soliloquy-config-slider-width').val(width);
				}

				if (height) {
					$('#soliloquy-config-slider-height').val(height);
				}
			});
		},
		//Clear Selected Slides
		clear_selected: function () {

			$('.soliloquy-clear-selected').on('click', function (e) {

				e.preventDefault();

				var list = $('#soliloquy-output li').length;

				$('ul#soliloquy-output li').removeClass('selected');

				$('.select-all').text(soliloquy_metabox_local.select_all);
				$('.soliloquy-count').text(list.toString());
				$('.soliloquy-select-all').prop('checked', false);
				$('.soliloquy-bulk-actions').fadeOut();

				$(this).fadeOut();

			});
		},

		//Change ul#soliloquy-output display type. Uses ajax to store data for each slider
		display: function () {

			//Toggle Grid/List Display
			$('a.soliloquy-display').on('click', function (e) {

				//Prevent Default
				e.preventDefault();

				//Don't do anything is its already active.
				if ($(this).hasClass('active-display')) {
					return;
				}

				var $this = $(this),
					$view = $this.data('soliloquy-display'),
					$output = $('#soliloquy-output'),
					opts = {
						url: soliloquy_metabox_local.ajax,
						type: 'post',
						async: true,
						cache: false,
						dataType: 'json',
						data: {
							action: 'soliloquy_slider_view',
							post_id: soliloquy_metabox_local.id,
							view: $view,
							nonce: soliloquy_metabox_local.save_nonce
						},
						success: function (response) {

						}
					};

				$.ajax(opts);


				//Find the current active button and remove class
				$('.soliloquy-display-toggle').find('.active-display').removeClass('active-display');

				//Add the active class to $this
				$this.addClass('active-display');

				if ($view === 'grid') {

					$output.removeClass('soliloquy-list').addClass('soliloquy-grid');

				} else if ($view === 'list') {

					$output.removeClass('soliloquy-grid').addClass('soliloquy-list');

				}

			});

		},

		//Chosen Select Boxes Init
		chosen: function () {

			//Create the Select boxes
			$('.soliloquy-chosen').each(function () {

				//Get the options from the data.
				var data_options = $(this).data('soliloquy-chosen-options');

				$(this).chosen(data_options);

			});

		},

		//Upload Image functioned Used in Woo and FC addons fallback.
		uploadImage: function () {

			$('.soliloquy-insert-image').on('click', function (e) {

				var soliloquy_image_frame;

				e.preventDefault();

				var $button = $(event.currentTarget),
					input_box = $button.parent().find('input');
				if (soliloquy_image_frame) {

					soliloquy_image_frame.open();

					return;

				}

				soliloquy_image_frame = wp.media.frames.soliloquy_image_frame = wp.media({

					frame: 'select',
					library: {
						type: 'image'
					},
					title: soliloquy_metabox_local.insert_image,
					button: {
						text: soliloquy_metabox_local.insert_image,
					},
					contentUserSetting: false,
					multiple: false
				});

				soliloquy_image_frame.on('select', function () {

					var attachment = soliloquy_image_frame.state().get('selection').first().toJSON();

					input_box.val(attachment.url);

				});

				soliloquy_image_frame.open();

			});
		}
	};

	//DOM ready
	$(function () {

		soliloquy_manage.init();

	});

	//Re init on type change
	$(document).on('soliloquyType', function () {

		soliloquy_manage.init();

	});

})(jQuery, window, document, soliloquy_metabox_local);
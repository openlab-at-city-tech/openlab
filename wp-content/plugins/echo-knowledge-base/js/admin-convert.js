/******************************************************************************************************************************************************************************************
 *
 *                CONVERT ARTICLES
 *
 *****************************************************************************************************************************************************************************************/

jQuery(document).ready(function($) {

	// data store for convert.
	let epkb_convert_data = {
		kb_id: 1,
		step: 1,
		selected_array: [],
		$wrap: [],
		post_type: 'post',
		convert_terms_mode: 'copy_terms',
		selected_count: 0,
		category_taxonomy: '',
		tags_taxonomy: '',
	};

	/***** Buttons Actions *********/

	// Back button will go out from first step or go to previous step
	$('.epkb-convert-button-back').on('click', function(){
		if ( epkb_convert_data.step < 2 ) {
			$(document).epkb('tools/hide_panels');
		} else if ( epkb_convert_data.step < 5 ) {
			epkb_show_step('prev');
		} else {
			epkb_show_step();
		}
	});

	// Press Next Step
	$('.epkb-convert-button-next').on('click', function(){

		// set convert data
		if ( epkb_convert_data.step == 1 ) {
			epkb_convert_data = {
				kb_id: $(this).data('kb_id'),
				step: 1,
				selected_array: [],
				$wrap: $(this).closest('.epkb-form-wrap'),
				post_type: $(this).closest('.epkb-form-wrap').find('[name=epkb_convert_post_type]').val(),
				convert_terms_mode: 'copy_terms',
				selected_count: 0,
				category_taxonomy: '',
				tags_taxonomy: '',
			};
		}

		// Check and show errors if exist
		if ( ! epkb_convert_validate_step() ) {
			return false;
		}

		epkb_show_step('next');
	});

	// Press Start Converting
	$('.epkb-convert-button-start_convert').on('click', function(){

		// Check and show errors if exist
		if ( ! epkb_convert_validate_step() ) {
			return false;
		}

		epkb_show_step('next');
	});

	// Press Cancel
	$('.epkb-convert-button-cancel').on('click', function(){
		$(this).addClass('epkb-hidden');
		// Show first step and return from ajax handlers
		epkb_show_step();
	});

	// Press Exit
	$('.epkb-convert-button-exit').on('click', function(){
		// Reset convert panel
		epkb_show_step();
		// Hide panels
		$(document).epkb('tools/hide_panels');
	});

	// select category
	$(document).on( 'change keyup', '.epkb-convert-form--posts .epkb-convert-categories-select select, .epkb-convert-form--posts .epkb-convert-categories-filters--name-filter input', function(){

		// deselect all
		$('[name=row_id]').prop('checked', false);
		$('#check_all_convert').prop('checked', false);

		epkb_convert_data.$wrap.find('.epkb-dsl__article-list__body .epkb-admin-row').each(function(){

			let active = true;
			let row = $(this);

			epkb_convert_data.$wrap.find('.epkb-convert-categories-select select').each(function(){
				if ( $(this).val() == '' ) {
					return true;
				}

				let taxonomy = $(this).data('taxonomy-name');

				if ( row.find('[data-kb-import-tax='+ taxonomy +']').length == 0 ) {
					active = false;
					return false;
				}

				if ( row.find('[data-kb-import-tax='+ taxonomy +']').find('[data-kb-import-cat-id='+ $(this).val() +']').length == 0 ) {
					active = false;
					return false;
				}
			});

			// text search
			let search = epkb_convert_data.$wrap.find('.epkb-convert-categories-filters--name-filter input').val().toLowerCase();

			if ( search && row.find('.title').text().toLowerCase().indexOf(search) == -1 ) {
				active = false;
			}

			if ( active ) {
				$(this).removeClass('hidden');
			} else {
				$(this).addClass('hidden');
			}
		});

	} );

	// select all script
	$('.epkb-convert-form--posts').on('change', '#check_all_convert', function(){

		if ( ! $(this).prop('checked') ) {
			epkb_convert_data.$wrap.find('.epkb-dsl__article-list__body .epkb-admin-row [name=row_id]').prop('checked', false);
			return;
		}

		epkb_convert_data.$wrap.find('.epkb-dsl__article-list__body .epkb-admin-row').each(function(){

			if ( ! epkb_convert_data.current_category || $(this).find( '[data-kb-import-cat-id='+epkb_convert_data.current_category+']' ).length ) {
				$(this).find('[name=row_id]').prop('checked', 'checked');
			} else {
				$(this).find('[name=row_id]').prop('checked', false);
			}
		});
	});

	// Validate current step
	// Return true/false and show notice with the reason
	function epkb_convert_validate_step() {

		// something went very wrong
		if ( epkb_convert_data.$wrap.length == 0 ) {
			$(document).epkb('notice/show', {
				message: epkb_vars.reload_try_again,
				type: 'error'
			});
			return false;
		}

		// Step 1.
		if ( epkb_convert_data.step == 1 ) {

			// check if checkbox exist and selected if yes
			if ( epkb_convert_data.$wrap.find('[name=epkb_convert_post]') && epkb_convert_data.$wrap.find('[name=epkb_convert_post]').first().prop('checked') == false ) {
				$(document).epkb('notice/show', {
					message: epkb_vars.msg_confirm_kb,
					type: 'error'
				});

				return false;
			}

			if ( epkb_convert_data.$wrap.find('[name=epkb_convert_backup]') && epkb_convert_data.$wrap.find('[name=epkb_convert_backup]').first().prop('checked') == false ) {
				$(document).epkb('notice/show', {
					message: epkb_vars.msg_confirm_backup,
					type: 'error'
				});

				return false;
			}

			if ( epkb_convert_data.$wrap.find('[name=epkb_convert_post_type]') && ! epkb_convert_data.$wrap.find('[name=epkb_convert_post_type]').val() ) {
				$(document).epkb('notice/show', {
					message: epkb_vars.msg_empty_post_type,
					type: 'error'
				});

				return false;
			}

			return true;
		}

		// Step 2. for XML
		if ( epkb_convert_data.step == 2 ) {

			// check if user can convert at least one article
			if ( epkb_convert_data.$wrap.find('[name=row_id]').length == 0 ) {

				$(document).epkb('notice/show', {
					message: epkb_vars.msg_nothing_to_convert,
					type: 'error'
				});

				return false;
			}

			// Check if user selected at least one article
			if ( epkb_convert_data.$wrap.find('[name=row_id]:checked').length == 0 ) {
				$(document).epkb('notice/show', {
					message: epkb_vars.msg_select_article,
					type: 'error'
				});

				return false;
			}

			return true;
		}

		// Step 3. for XML. Nothing to validate
		if ( epkb_convert_data.step == 3 ) {
			return true;
		}

		// Wrong step number
		$(document).epkb('notice/show', {
			message: epkb_vars.reload_try_again,
			type: 'error'
		});

		return false;
	}

	// Toggle step
	// newStep: prev, next, number б default - first step
	function epkb_show_step( newStep = 1 ) {

		// change step
		if ( newStep == 'next' ) {
			epkb_convert_data.step++;
		} else if ( newStep == 'prev' ) {
			epkb_convert_data.step--;
		} else {
			epkb_convert_data.step = newStep;
		}

		// color steps header
		let i = 1;
		while ( i < 5 ) {

			if ( epkb_convert_data.step >= i ) {
				epkb_convert_data.$wrap.find('.epkb-import-step--' + i).addClass('epkb-import-step--done');
			} else {
				epkb_convert_data.$wrap.find('.epkb-import-step--' + i).removeClass('epkb-import-step--done');
			}

			i++;
		}

		// Show step body
		epkb_convert_data.$wrap.find('.epkb-import-step').addClass('epkb-hidden');
		epkb_convert_data.$wrap.find('.epkb-import-step--' + epkb_convert_data.step).removeClass('epkb-hidden');

		// Steps special actions

		// Toggle buttons
		if ( epkb_convert_data.step == 1 ) {

			epkb_convert_data.$wrap.find('.epkb-convert-button-back').removeClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-exit').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-cancel').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-next').removeClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-start_convert').addClass('epkb-hidden');

			// clear checkbox and file input
			epkb_convert_data.$wrap.find('.convert-kb-name-checkbox').prop( 'checked', false );
		}

		if ( epkb_convert_data.step == 2 ) {

			epkb_convert_data.$wrap.find('.epkb-convert-button-back').removeClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-exit').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-cancel').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-next').removeClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-start_convert').addClass('epkb-hidden');

			epkb_load_articles_list();
		}

		// Toggle buttons
		if ( epkb_convert_data.step > 1 ) {
			epkb_convert_data.$wrap.find('.epkb-convert-button-start').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-next').removeClass('epkb-hidden');
		}

		if ( epkb_convert_data.step == 3 ) {
			epkb_convert_data.$wrap.find('.epkb-convert-button-next').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-start_convert').removeClass('epkb-hidden');
		}

		// Run convert
		if ( epkb_convert_data.step == 4 ) {
			epkb_start_convert_process();
		}

		// Hide cancel button if no needs
		if ( epkb_convert_data.step < 4 ) {
			epkb_convert_data.$wrap.find('.epkb-convert-button-cancel').addClass('epkb-hidden');
			epkb_convert_data.$wrap.find('.epkb-convert-button-exit').addClass('epkb-hidden');
		}
	}

	// We are on 2 step, show progress bar, send ajax to get articles
	function epkb_load_articles_list() {

		let $bar = epkb_convert_data.$wrap.find('.epkb-import-step--2 .epkb-progress');

		// Clear step content
		epkb_convert_data.$wrap.find('.epkb-dsl__article-list-container, .epkb-convert-categories-filters').remove();

		// Start progress bar
		$bar.epkb('progress/clear_log');
		$bar.epkb('progress/set', 0);
		$bar.epkb('progress/add_log', { message: epkb_vars.msg_reading_posts });

		let postData = {
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			action: 'epkb_load_articles_list',
			kb_id: epkb_convert_data.kb_id,
			epkb_convert_step: epkb_convert_data.step,
			post_type: epkb_convert_data.post_type,
			categories: []
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
		}).done(function (response) {

			if ( typeof response.success == 'object' ) {

				for ( let message in response.success ) {

					$bar.epkb('progress/add_log', { message: response.success[message], type: 'success' });
				}

				$bar.epkb('progress/set', 100);
			}

			if ( typeof response.response_html_1 != 'undefined' ) {
				epkb_convert_data.$wrap.find('.epkb-import-step.epkb-import-step--2').append(response.response_html_1);
			}

			if ( typeof response.response_html_2 != 'undefined' ) {
				epkb_convert_data.$wrap.find('.epkb-import-step.epkb-import-step--3').html(response.response_html_2);
			}

			if ( typeof response.error != 'undefined' ) {
				$(document).epkb('ajax/error_message', response.message);

				// show step 1
				epkb_show_step();
				return false;
			}

		}).fail(function (response, textStatus, error) {
			$(document).epkb('ajax/error_message', epkb_vars.msg_admin_error_l012);

			// show step 1
			epkb_show_step();
		});

	}

	// Last step starting of the convert
	function epkb_start_convert_process() {

		let $bar = epkb_convert_data.$wrap.find('.epkb-import-step--4 .epkb-progress');

		// read articles that should be converted
		epkb_convert_data.selected_array = [];
		epkb_convert_data.$wrap.find('input[name=row_id]:checked').each(function(){
			epkb_convert_data.selected_array.push($(this).val());
		});

		epkb_convert_data.selected_count = epkb_convert_data.selected_array.length;

		// read settings
		if ( epkb_convert_data.$wrap.find('input[name="convert_terms_mode"]:checked').length ) {
			epkb_convert_data.convert_terms_mode = epkb_convert_data.$wrap.find('input[name="convert_terms_mode"]:checked').val();
		}

		if ( epkb_convert_data.$wrap.find('[name=categories_taxonomy]').length ) {
			epkb_convert_data.category_taxonomy = epkb_convert_data.$wrap.find('[name=categories_taxonomy]').val();
		}

		if ( epkb_convert_data.$wrap.find('[name=tags_taxonomy]').length ) {
			epkb_convert_data.tags_taxonomy = epkb_convert_data.$wrap.find('[name=tags_taxonomy]').val();
		}

		// Hide next button
		epkb_convert_data.$wrap.find('.epkb-convert-button-next').addClass('epkb-hidden');

		// Hide back button
		epkb_convert_data.$wrap.find('.epkb-convert-button-back').addClass('epkb-hidden');

		// Hide start convert button
		epkb_convert_data.$wrap.find('.epkb-convert-button-start_convert').addClass('epkb-hidden');

		// Show cancel button
		epkb_convert_data.$wrap.find('.epkb-convert-button-cancel').removeClass('epkb-hidden');

		// clear attachments links message
		epkb_convert_data.$wrap.find('.epkb-form-field-non_attachments-message').html('');

		// Show progress bar
		$bar.epkb('progress/clear_log');
		$bar.epkb('progress/set', 0);
		$bar.epkb('progress/add_log', { message: epkb_vars.msg_converting });

		// Start convert (recursion)
		convert_batch();
	}

	// recursion function to convert batch of the posts
	function convert_batch() {

		let $bar = epkb_convert_data.$wrap.find('.epkb-import-step--4 .epkb-progress');
		let current_progress = parseInt( 100 * ( epkb_convert_data.selected_count - epkb_convert_data.selected_array.length ) / epkb_convert_data.selected_count );

		$bar.epkb('progress/set', current_progress );

		let current_batch = epkb_convert_data.selected_array.splice(0, 5 );

		let postData = {
			action: 'epkb_convert_kb_content',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			epkb_kb_id: epkb_convert_data.kb_id,
			epkb_convert_post_type: epkb_convert_data.post_type,
			selected_rows: JSON.stringify( current_batch ),
			convert_terms_mode: epkb_convert_data.convert_terms_mode,
			epkb_convert_step: epkb_convert_data.step,
			category_taxonomy: epkb_convert_data.category_taxonomy,
			tags_taxonomy: epkb_convert_data.tags_taxonomy
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
		}).done(function (response) {

			// user pressed cancel button
			if ( epkb_convert_data.step < 4 ) {
				return false;
			}

			// error_die
			if ( typeof response.error != 'undefined' ) {
				$(document).epkb('ajax/error_message', response.message);

				// show step 1
				epkb_show_step();
				return false;
			}

			// Error messages that are not stopping convert
			if ( typeof response.process_errors == 'object' ) {
				for ( let error in response.process_errors ) {
					$bar.epkb('progress/add_log', { message: response.process_errors[error], type: 'error' });
				}
			}

			// Has more steps
			if ( typeof response.success != 'undefined' && epkb_convert_data.selected_array.length ) {
				convert_batch();
				return;
			}

			// Has no more steps. convert complete
			if ( typeof response.success != 'undefined' ) {
				$bar.epkb('progress/add_log', { message: response.success, type: 'success' });
				$bar.epkb('progress/add_log', { message: epkb_convert_data.selected_count - epkb_convert_data.selected_array.length + ' ' + epkb_vars.msg_articles_converted, type: 'success' });

				$bar.epkb('progress/set', 100);
				epkb_convert_data.$wrap.find('.epkb-convert-button-cancel').addClass('epkb-hidden');
				epkb_convert_data.$wrap.find('.epkb-convert-button-exit').removeClass('epkb-hidden');
			}
		});
	}

	// Don't allow choose the same taxonomy for categories map
	$(document).on( 'change', '.epkb-author__curr_auth [name=categories_taxonomy]', function(){
		if ( $(this).val() == $('.epkb-author__curr_auth [name=tags_taxonomy]').val() ) {
			$('.epkb-author__curr_auth [name=tags_taxonomy]').val('')
		}
	});

	$(document).on( 'change', '.epkb-author__curr_auth [name=tags_taxonomy]', function(){
		if ( $(this).val() == $('.epkb-author__curr_auth [name=categories_taxonomy]').val() ) {
			$('.epkb-author__curr_auth [name=categories_taxonomy]').val('')
		}
	});
});

jQuery(document).ready(function($) {

	let wizard = $( '#epkb-config-wizard-content, .epkb-config-wizard-content' );
	let need_to_apply_theme = true;
	
	// If the Wizard is not detected don't run scripts.
	if ( wizard.length <= 0 ) {
		return;
	}

	/**
	 * Highlight all completed steps in status bar.
	 */
	function wizard_status_bar_highlight_completed_steps( nextStep, current_wizard ){

		// Clear Completed Classes
		current_wizard.find( '.epkb-wizard-status-bar .epkb-wsb-step' ).removeClass( 'epkb-wsb-step--completed' );

		current_wizard.find( '.epkb-wizard-status-bar .epkb-wsb-step' ).each( function(){

			// Get each Step ID
			id = $( this ).attr( 'id' );

			// Get last character the number of each ID
			let lastChar = id[id.length -1];

			// If the ID is less than the current step then add completed class.
			if( lastChar < nextStep ){
				$( this ).addClass( 'epkb-wsb-step--completed' );
			}
		});
	}

	/**
	 * Change Next button to apply button on last step.
	 * Remove Class for all steps other than the first: epkb-wizard-button-container--first-step
	 */
	function wizard_change_buttons( current_wizard ){
		current_wizard.find( '.epkb-wizard-button-container' ).removeClass( 'epkb-wizard-button-container--final-step' );
		current_wizard.find( '.epkb-wizard-button-container' ).removeClass( 'epkb-wizard-button-container--first-step' );
		let id = current_wizard.find( '.epkb-wsb-step--active' ).attr( 'id' );

		// Get last character the number of each ID
		let lastChar = Number(id[id.length - 1]);
		let stepLength = current_wizard.find('.epkb-wizard-status-bar li.epkb-wsb-step').length;
		if ( current_wizard.find('.epkb-wizard-ordering-ordering-preview').length ) {
			//stepLength = stepLength - 1;
		}
		if( lastChar === 1 ){
			current_wizard.find( '.epkb-wizard-button-container' ).addClass( 'epkb-wizard-button-container--first-step' );
		}
		
		if( lastChar === stepLength ){
			current_wizard.find( '.epkb-wizard-button-container' ).addClass( 'epkb-wizard-button-container--final-step' );
		}
	}

	/**
	 * Change the Top Description based on step.
	 */
	function wizard_show_step_description( current_wizard ){

		// Get the current active step ID
		let id = $( '.epkb-wsb-step--active' ).attr( 'id' );

		// Get last character the number of each ID
		let lastChar = Number(id[id.length -1]);

		// Clear all active classes
		current_wizard.find( '.epkb-wizard-header__desc__step' ).removeClass( 'epkb-wizard-desc-active' );

		// Set the Active class based on ID
		current_wizard.find( '#epkb-wizard-desc-step-' + lastChar ).addClass( 'epkb-wizard-desc-active' );

	}

	/**
	 * Quickly scroll the user back to the top.
	 */
	function wizard_scroll_to_top(){
		$("html, body").animate({ scrollTop: 0 }, 0);
	}

	/**
	 * Button JS for next Step.
	 *
	 */
	wizard.find( '#epkb-wizard-button-next' ).on( 'click' , function(e){
		e.preventDefault();

		// Get currently active wizard
		let current_wizard = $( this ).closest( '#epkb-config-wizard-content, .epkb-config-wizard-content' );

		// Get the Step values
		let nextStep = Number( current_wizard.find( '#epkb-wizard-button-next' ).val() );
		let prevStep = Number( current_wizard.find( '#epkb-wizard-button-prev' ).val() );

		// Remove all Active Step classes in Step Status Bar.
		current_wizard.find( '.epkb-wsb-step' ).removeClass( 'epkb-wsb-step--active' );

		// Add Active class to next Step in Status Bar.
		current_wizard.find( '#epkb-wsb-step-' + nextStep ).addClass( 'epkb-wsb-step--active' );

		// Remove all active class from panels.
		current_wizard.find( '.epkb-wc-step-panel' ).removeClass( 'epkb-wc-step-panel--active' );

		// Add Active class to next panel in the steps.
		current_wizard.find( '#epkb-wsb-step-' + nextStep + '-panel' ).addClass( 'epkb-wc-step-panel--active' );

		// Update the Previous and Next Data values.
		current_wizard.find( '#epkb-wizard-button-prev' ).val( prevStep + 1 );
		current_wizard.find( '#epkb-wizard-button-next' ).val( nextStep + 1 );

		wizard_status_bar_highlight_completed_steps( nextStep, current_wizard );
		wizard_change_buttons( current_wizard );
		wizard_show_step_description( current_wizard );
		wizard_scroll_to_top();
	});

	/**
	 * Button JS for prev Step.
	 *
	 */
	wizard.find( '#epkb-wizard-button-prev' ).on( 'click' , function(e){
		e.preventDefault();

		// Get currently active wizard
		let current_wizard = $( this ).closest( '#epkb-config-wizard-content, .epkb-config-wizard-content' );

		// Get the Step values
		let nextStep = Number( current_wizard.find( '#epkb-wizard-button-next' ).val() );
		let prevStep = Number( current_wizard.find( '#epkb-wizard-button-prev' ).val() );

		// Remove all Active Step classes in Step Status Bar.
		current_wizard.find( '.epkb-wsb-step' ).removeClass( 'epkb-wsb-step--active' );

		// Add Active class to next Step in Status Bar.
		current_wizard.find( '#epkb-wsb-step-' + prevStep ).addClass( 'epkb-wsb-step--active' );

		// Remove all active class from panels.
		current_wizard.find( '.epkb-wc-step-panel' ).removeClass( 'epkb-wc-step-panel--active' );

		// Add Active class to next panel in the steps.
		current_wizard.find( '#epkb-wsb-step-' + prevStep + '-panel' ).addClass( 'epkb-wc-step-panel--active' );

		// Update the Previous and Next Data values.
		current_wizard.find( '#epkb-wizard-button-prev' ).val( prevStep - 1 );
		current_wizard.find( '#epkb-wizard-button-next' ).val( nextStep - 1 );

		wizard_status_bar_highlight_completed_steps( prevStep, current_wizard );
		wizard_change_buttons( current_wizard );
		wizard_show_step_description( current_wizard );
		wizard_scroll_to_top();
	});

	/**
	 * Theme Toggle JS
	 *
	 */
	wizard.find( '.epkb-wt-tab' ).on( 'click' , function(e){

		// Get Tab ID Value and Template ID
		let tab = $( this );
		let id = tab.attr( 'id' );
		let panel = $( '#'+id+'-panel' );

		// Remove all Active Tab classes
		$( '.epkb-wt-tab' ).removeClass( 'epkb-wt--active' );
		
		
		// Add Active class to click on theme.
		tab.addClass( 'epkb-wt--active' );
		
		
		// Remove all active class from panels.
		$( '.epkb-wt-panel' ).removeClass( ' epkb-wt-panel--active' ).css({'opacity' : '0'});
		// Add Active class to panel with the same id
		panel.addClass( 'epkb-wt-panel--active' );
		panel.animate({'opacity' : '1'}, 200);
		// change styles for the tabs 
		let styles = JSON.parse(panel.find('.theme-values').val());
		
		if (styles) {

			$('#epkb-public-styles-inline-css').html(`
				#epkb-content-container .epkb-nav-tabs .active:after {
					border-top-color: ${styles.tab_nav_active_background_color}!important
				}
				#epkb-content-container .epkb-nav-tabs .active {
					background-color: ${styles.tab_nav_active_background_color}!important
				}
				#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1,
				#epkb-content-container .epkb-nav-tabs .active p {
					color: ${styles.tab_nav_active_font_color}!important
				}
				#epkb-content-container .epkb-nav-tabs .active:before {
					border-top-color: ${styles.tab_nav_border_color}!important
				}		
			`);
			original_styles = $('#epkb-public-styles-inline-css').html();
		}
		
		// set value true to change color pickers on "next" button click
		need_to_apply_theme = true;
	});

	/**
	 * SHOW INFO MESSAGES
	 */
	function epkb_admin_notification( $title, $message , $type ) {

		clear_message_after_set_time();

		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? $message : '') +
			'</span>' +
			'</div>' +
			'</div>';
	}
	
	/**
	 * Check KB Url and show notice if bad 
	 */
	function eckb_wizard_check_slug() {

		// Get currently active wizard
		let current_wizard = $( this ).closest( '#epkb-config-wizard-content, .epkb-config-wizard-content' );

		let input = current_wizard.find( '.epkb-wizard-slug input' );
		if ( input.length == 0 ) {
			return true;
		}
		
		let val = input.val();
		let isValid = true;

		if ( val.startsWith("http") ) {
			isValid = false;
		}

		if ( val.startsWith("www") ) {
			isValid = false;
		}

		if ( val.endsWith(".") ) {
			isValid = false;
		}

		if ( val.endsWith(".com") ) {
			isValid = false;
		}

		if ( val.endsWith(".org") ) {
			isValid = false;
		}

		if ( isValid ) {
			current_wizard.find( '#epkb-wizard-slug-error' ).hide();
			current_wizard.find( '#epkb-wizard-button-next' ).prop('disabled', false);
			input.removeClass('epkb-wizard-input-error');
		} else {
			current_wizard.find( '#epkb-wizard-slug-error' ).show();
			current_wizard.find( '#epkb-wizard-button-next' ).prop('disabled', 'disabled');
			input.addClass('epkb-wizard-input-error');
		}
	}
	
	$( '.epkb-wizard-slug input' ).on( 'change keyup paste', eckb_wizard_check_slug );
	$( '.epkb-wizard-slug input' ).trigger( 'change' );
	


	/********************************************************************
	 *
	 *                      ORDERING WIZARD
	 *
	 ********************************************************************/

	/** Initial Settings */

	let wizard_ordering_preview = $( '.epkb-wizard-ordering-ordering-preview' );
	if ( wizard_ordering_preview.length ) {
		$( '#eckb-wizard-ordering__page' ).on( 'change', 'input', epkb_wizard_update_ordering_view );
	}

	$( '.epkb-admin__secondary-panel__order-articles' ).on( 'click', function() {
		if ( wizard_ordering_preview.hasClass( 'epkb-wizard-ordering-ordering-preview--init' ) ) {
			return;
		}
		wizard_ordering_preview.addClass( 'epkb-wizard-ordering-ordering-preview--init' );
		epkb_wizard_update_ordering_view();
	});

	$('#eckb-wizard-ordering__page #epkb-wizard-button-next').on('click', function(){
		epkb_wizard_update_ordering_view( false );
	});

	function epkb_wizard_update_ordering_view( $silent = true) {

		let current_wizard = $( '#eckb-wizard-ordering__page' );

		// get current config 
		let sequence_settings = {
			categories_display_sequence : current_wizard.find( 'input[name=categories_display_sequence]:checked' ).val(),
			articles_display_sequence : current_wizard.find( 'input[name=articles_display_sequence]:checked' ).val(),
			show_articles_before_categories : current_wizard.find( 'input[name=show_articles_before_categories]:checked' ).val(),
			sidebar_show_articles_before_categories : current_wizard.find( 'input[name=show_articles_before_categories]:checked' ).val(),
		};
		
		let postData = {
			action: 'epkb_wizard_update_order_view',
			_wpnonce_epkb_ajax_action: current_wizard.find( '#_wpnonce_epkb_ajax_action' ).val(),
			sequence_settings: sequence_settings,
			epkb_kb_id: current_wizard.find( '#epkb_wizard_kb_id' ).val()
		};
		
		epkb_send_ajax( current_wizard, postData, function( response ){
			if ( typeof response.html !== 'undefined' ) {
				
				let preview = '';
				
				if ( response.message.length ) {
					preview += '<h1 class="eckb-wisard-ordering-title">' + response.message + '</h1>';
				}
				
				preview += response.html;

				current_wizard.find( '.epkb-wizard-ordering-ordering-preview' ).html( preview );
				
				epkb_enable_custom_ordering( ( sequence_settings.articles_display_sequence == 'user-sequenced' ), ( sequence_settings.categories_display_sequence == 'user-sequenced' ) );
			}
		}, false, '', $silent );
	}

	function epkb_enable_custom_ordering( articles, categories ) {
		if ( ! articles && ! categories ) {
			return false;
		} 
		
		if ( categories ) {
			// Order Top Categories for Tabs layout
			$('.epkb-wizard-ordering-ordering-preview .epkb-top-categories-list').sortable({
				axis: 'x',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				// handle: '.epkb-sortable-articles',
				opacity: 0.8,
				placeholder: 'epkb-sortable-placeholder',
			});
			
			// Order Categories
			$('.epkb-wizard-ordering-ordering-preview .eckb-categories-list, .epkb-wizard-ordering-ordering-preview .elay-sidebar__cat-container').sortable({
				axis: 'x,y',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				// handle: '.epkb-sortable-articles',
				opacity: 0.8,
				placeholder: 'epkb-sortable-placeholder',
			});
			
			// Order Sub-categories
			$('.epkb-wizard-ordering-ordering-preview .eckb-sub-category-ordering').sortable({
				axis: 'x,y',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				// handle: '.epkb-sortable-articles',
				opacity: 0.8,
				placeholder: 'epkb-sortable-placeholder',
			});

			// Order Sub-sub-categories
			$('.epkb-wizard-ordering-ordering-preview .eckb-sub-sub-category-ordering').sortable({
				axis: 'x,y',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				// handle: '.epkb-sortable-articles',
				opacity: 0.8,
				placeholder: 'epkb-sortable-placeholder',
			});
		}

		if ( articles ) {
			// Order Articles
			$('.epkb-wizard-ordering-ordering-preview .epkb-articles').sortable({
				axis: 'y',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				// handle: '.epkb-sortable-articles',
				opacity: 0.8,
				placeholder: 'epkb-sortable-placeholder',
			});
		}
		
		$('.epkb-wizard-ordering-ordering-preview').find( 'a' ).css('cursor', 'move', 'important');

		$('.epkb-wizard-ordering-ordering-preview .epkb-category-level-2-3').on('click', function(){
			$(this).parent().children('ul').toggleClass('active');
		});
	}

	
	/********************************************************************
	 *
	 *                      GLOBAL WIZARD
	 *
	 ********************************************************************/

	let wizard_global = $( '.eckb-wizard-global-page' );
	
	/** Initial Settings */
	
	if ( wizard_global.length ) {
		// Open panel with the settings because we have only 1 accordion item 
		wizard_global.find( '#epkb-wsb-step-1-panel .eckb-wizard-option-heading' ).trigger( 'click' );
		$( '.notice-epkb_changed_slug' ).remove();
		
		if ( $( '.notice-epkb-no-main-pages' ).length ) {
			$( '.notice-epkb-no-main-pages' ).appendTo( $( '#epkb-wsb-step-1-panel' ) );
			$( '.notice-epkb-no-main-pages' ).find( '.epkb-notice-dismiss' ).remove();
		}
	}
	
	if ( wizard_global.find( '.eckb_slug' ).length ) {
		let eckb_slug_checked = false;
		let current_path = wizard_global.find( '#kb_articles_common_path' ).val();

		wizard_global.find( '.eckb_slug' ).each( function() {
			if ( $( this ).data( 'path' ) == current_path ) {
				$( this ).prop( 'checked', 'checked' );
				eckb_slug_checked = true;
			}
		});

		if ( ! eckb_slug_checked ) {
			wizard_global.find( '.eckb_slug' ).eq( 0 ).prop( 'checked', 'checked' );
			$( '#kb_articles_common_path' ).val( wizard_global.find( '.eckb_slug' ).eq( 0 ).data( 'path' ) );
		}

		wizard_global.find( '.eckb_slug' ).on( 'change', function() {
			$( '#kb_articles_common_path' ).val( $( this ).data( 'path' ) );
			wizard_global.data( 'kb-main-page-id', $( this ).data( 'kb-main-page-id' ) );
		});
	}

	$( document.body ).on( 'click', '#categories_in_url_enabled__toggle input', function (){
		if ( $( this ).is( ':checked' ) ) {
			wizard_global.find( '#categories_in_url_enabled' ).val( 'on' );
			wizard_global.find( '.epkb-wso-with-category__category' ).removeClass( 'epkb-wso-with-category__category--off' );
		} else {
			wizard_global.find( '#categories_in_url_enabled' ).val( 'off' );
			wizard_global.find( '.epkb-wso-with-category__category' ).addClass( 'epkb-wso-with-category__category--off' );
		}
	});
	
	/** ***********************************************************************************************
	 *
	 *          AJAX calls
	 *
	 * **********************************************************************************************/


	// generic AJAX call handler
	function epkb_send_ajax( current_wizard, postData, refreshCallback, reload, loaderMessage, silent_mode = false ) {

		let errorMsg;
		let theResponse;
		refreshCallback = (typeof refreshCallback === 'undefined') ? 'epkb_callback_noop' : refreshCallback;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				if ( ! silent_mode ) {
					epkb_loading_Dialog( 'show', loaderMessage );
				}
			}
		}).done(function (response)        {
			theResponse = ( response ? response : '' );
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				errorMsg = theResponse.message ? theResponse.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
			}

		}).fail( function ( response, textStatus, error )        {
			//noinspection JSUnresolvedVariable
			errorMsg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			errorMsg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, errorMsg, 'error');
		}).always(function ()        {
			if ( ! silent_mode ) {

				epkb_loading_Dialog( 'remove', '' );

			}

			if ( errorMsg ) {
				current_wizard.find( '.eckb-bottom-notice-message' ).replaceWith( errorMsg );
				$("html, body").animate({scrollTop: 0}, "slow");

			} else {
				if ( ! silent_mode ) {
					if ( ! theResponse.error && typeof theResponse.message !== 'undefined' && theResponse.message ) {

						current_wizard.find( '.eckb-bottom-notice-message' ).replaceWith(
							epkb_admin_notification('', theResponse.message, 'success')
						);
					}
				}
				
				if ( typeof refreshCallback === "function" ) {
					theResponse = (typeof theResponse === 'undefined') ? '' : theResponse;
					refreshCallback(theResponse);
				} else {
					if ( reload ) {
						location.reload();
					}
				}
			}
		});
	}

	/* Dialogs --------------------------------------------------------------------*/

	/**
	  * Displays a Center Dialog box with a loading icon and text.
	  *
	  * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	  * This code is used in these files, any changes here must be done to the following files.
	  *   - admin-plugin-pages.js
	  *   - admin-kb-config-scripts.js
	  *   - admin-kb-wizard-script.js
	  *
	  * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	  * @param  {string}    message         Optional    Message output from database or settings.
	  *
	  * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	  *
	  */
	function epkb_loading_Dialog( displayType, message ){

		if( displayType === 'show' ){

			let output =
				'<div class="epkb-admin-dialog-box-loading">' +

				//<-- Header -->
				'<div class="epkb-admin-dbl__header">' +
				'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
				(message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epkb-admin-dialog-box-overlay"></div>';

			//Add message output at the end of Body Tag
			$( 'body' ).append( output );
		}else if( displayType === 'remove' ){

			// Remove loading dialogs.
			$( '.epkb-admin-dialog-box-loading' ).remove();
			$( '.epkb-admin-dialog-box-overlay' ).remove();
		}

	}

	/** Disable submit forms inside preview  */
	$('.eprf-leave-feedback-form').on( 'submit', false);

	function clear_bottom_notifications() {
		var bottom_message = $('body').find('.eckb-bottom-notice-message');
		if ( bottom_message.length ) {
			bottom_message.addClass( 'fadeOutDown' ).html( '' );
		}
	}

	function clear_message_after_set_time(){

		var epkb_timeout;
		if( $('.eckb-bottom-notice-message' ).length > 0 ) {
			clearTimeout(epkb_timeout);

			//Add fadeout class to notice after set amount of time has passed.
			epkb_timeout = setTimeout(function () {
				clear_bottom_notifications();
			} , 10000);
		}
	}



	/** ----------------- WIZARDS INTEGRATED IN ADMIN PAGES -------------------- */

	function epkb_get_top_category_seq() {

		let top_cat_sequence = '';
		let use_top_sequence = $('#use_top_sequence').val() === 'yes';

		if ( ! use_top_sequence || typeof $('.epkb-wizard-ordering-ordering-preview .epkb-top-categories-list') === 'undefined' ) {
			return top_cat_sequence;
		}

		$('.epkb-wizard-ordering-ordering-preview .epkb-top-categories-list').children().each(function(i, obj) {
			let top_cat_id = $(this).find('[data-kb-category-id]').data('kb-category-id');
			if ( top_cat_id ) {
				top_cat_sequence += 'xx' + top_cat_id;
			}
		});

		return top_cat_sequence;
	}

	function epkb_get_new_main_page_sequence() {
		let new_sequence = '';

		// make virtual tree and sort articles when artiles on the top of the categories
		if ($('.epkb-wizard-ordering-ordering-preview').find('#epkb-content-container').length) {
			// not sidebar template
			$('.epkb-wizard-ordering-ordering-preview').append('<div class="epkb-virtual-articles" style="display:none!important;">' + $('.epkb-wizard-ordering-ordering-preview').find('#epkb-content-container').html() + '</div>');

			$('.epkb-virtual-articles').find('ul.epkb-articles').each(function(){
				// check if we have articles on the top - move them to the bottom
				if ($(this).next().length && $(this).next().prop('tagName') == 'UL') {
					let wrap = $(this).parent();

					$(this).appendTo(wrap);
				}
			});
		}

		// handle Elegant Layouts with Sidebar
		if ($('.epkb-wizard-ordering-ordering-preview #el'+'ay-sidebar-layout-page-container').find('.el'+'ay-sidebar').length) {
			// sidebar template
			$('.epkb-wizard-ordering-ordering-preview #el'+'ay-sidebar-layout-page-container').append('<div class="epkb-virtual-articles" style="display:none!important;">' + $('.epkb-wizard-ordering-ordering-preview #el'+'ay-sidebar-layout-page-container').find('.el'+'ay-sidebar').html() + '</div>');

			$('.epkb-wizard-ordering-ordering-preview .epkb-virtual-articles').find('ul.el'+'ay-articles').each(function(){
				// check if we have articles on the top - move them to the bottom
				if ($(this).next().length && $(this).next().prop('tagName') == 'UL') {
					let wrap = $(this).parent();

					$(this).appendTo(wrap);
				}
			});
		}

		// handle Elegant Layouts with Grid
		if ( $('.epkb-wizard-ordering-ordering-preview #el'+'ay-grid-layout-page-container').length ) {
			$('.epkb-wizard-ordering-ordering-preview').find('[data-kb-type]').each(function (i, obj) {

				// some layouts like Tabs Layout has top categories and sub-categories "disconnected". Connect them here
				let top_cat_id = $(this).data('kb-top-category-id') ? $(this).data('kb-top-category-id') : '';
				if (top_cat_id) {
					new_sequence += 'xx' + top_cat_id + 'x' + 'category';
				}

				if (typeof $(this).attr("data-kb-type") !== 'undefined' && $(this).attr("data-kb-type") == 'top-category-no-articles') {
					return true;
				}

				// add sub-category or articles
				let category_id = typeof $(this).data('kb-category-id') === 'undefined' ? $(this).data('kb-article-id') : $(this).data('kb-category-id');
				if (typeof category_id !== 'undefined') {
					new_sequence += 'xx' + category_id + 'x' + $(this).attr("data-kb-type");
				}
			});

			return new_sequence;
		}

		// for v2
		if ($('.epkb-wizard-ordering-ordering-preview #elay-sidebar-container-v2').length) {
			// Wsidebar template
			$('.epkb-wizard-ordering-ordering-preview #elay-sidebar-container-v2').append('<div class="epkb-virtual-articles" style="display:none!important;">' + $('.epkb-wizard-ordering-ordering-preview #elay-sidebar-container-v2').html() + '</div>');

			$('.epkb-wizard-ordering-ordering-preview .epkb-virtual-articles').find('ul.el'+'ay-articles').each(function(){
				// check if we have articles on the top - move them to the bottom
				if ($(this).next().length && $(this).next().prop('tagName') == 'UL') {
					let wrap = $(this).parent();

					$(this).appendTo(wrap);
				}
			});
		}

		// handle the rest
		$('.epkb-wizard-ordering-ordering-preview').find('.epkb-virtual-articles [data-kb-type]').each(function(i, obj) {

			// some layouts like Tabs Layout has top categories and sub-categories "disconnected". Connect them here
			let top_cat_id = $(this).data('kb-top-category-id') ? $(this).data('kb-top-category-id') : '';
			if ( top_cat_id ) {
				new_sequence += 'xx' + top_cat_id + 'x' + 'category';
			}

			if ( typeof $(this).attr("data-kb-type") !== 'undefined' && $(this).attr("data-kb-type") == 'top-category-no-articles' ) {
				return true;
			}

			// add sub-category or articles
			let category_id = typeof $(this).data('kb-category-id') === 'undefined' ? $(this).data('kb-article-id') : $(this).data('kb-category-id');
			if ( typeof category_id !== 'undefined' ) {
				new_sequence += 'xx' + category_id + 'x' + $(this).attr("data-kb-type");
			}
		});

		$('.epkb-wizard-ordering-ordering-preview .epkb-virtual-articles').remove();
		return new_sequence;
	}

	/**
	 * Handle Apply Button
	 */
	$( '.epkb-wizard-button-apply' ).on( 'click' , function(e){

		// Get currently active wizard
		let current_wizard = $( this ).closest( '.epkb-admin__section-wrap' );

		let wizard_type = $(this).data('wizard-type');
		let kb_config = {};
		let menu_ids = [];

		let postData = {
			wizard_type: wizard_type,
			action: 'epkb_apply_wizard_changes',
			_wpnonce_epkb_ajax_action: current_wizard.find( '#_wpnonce_epkb_ajax_action' ).val(),
			epkb_wizard_kb_id: current_wizard.find( '#epkb_wizard_kb_id' ).val(),
			kb_main_page_id: current_wizard.find( '.epkb-config-wizard-content' ).data( 'kb-main-page-id' ),
		};

		if ( wizard_type == 'ordering' ) {
			// Get Tab ID Value and Template ID

			current_wizard.find( '.epkb-radio-buttons-container input[type=radio]:checked' ).each(function(){
				kb_config[$(this).attr('name')] = $(this).val();
			});

			if ( typeof kb_config.show_articles_before_categories === 'undefined' ) {
				kb_config.show_articles_before_categories = current_wizard.find( '#original_show_articles_before_categories' ).val();
			}

			kb_config.sidebar_show_articles_before_categories = kb_config.show_articles_before_categories;

			// If we have at least one options is set to user-sequenced - second step is required to load article sequence, otherwise we can't save
			if ( $('.epkb-wizard-ordering-ordering-preview').find('*').length == 0 && ( kb_config.articles_display_sequence == 'user-sequenced' || kb_config.categories_display_sequence == 'user-sequenced' ) ) {
				$('#epkb-wizard-button-next').trigger('click');
				return false;
			}
			// Sequence
			postData.epkb_new_sequence = epkb_get_new_main_page_sequence();
			postData.top_cat_sequence = epkb_get_top_category_seq();

		} else if ( wizard_type == 'global' ) {

			// Get Tab ID Value and Template ID
			current_wizard.find( '.eckb-wizard-single-text input, input[name=kb_articles_common_path], input[name=categories_in_url_enabled]' ).each(function(){
				if (kb_config[$(this).attr('name')] !== 'undefined') {
					kb_config[$(this).attr('name')] = $(this).val();
				}
			});

			current_wizard.find( '.eckb-wizard-single-radio input[type=radio]:checked, .radio-buttons-vertical  input[type=radio]:checked, .radio-buttons-horizontal input[type=radio]:checked' ).each(function(){
				if (kb_config[$(this).attr('name')] !== 'undefined') {
					kb_config[$(this).attr('name')] = $(this).val();
				}
			});

			current_wizard.find( '.eckb-wizard-single-checkbox input' ).each(function(){

				if (kb_config[$(this).attr('name')] !== 'undefined') {

					if ($(this).prop('checked')) {
						kb_config[$(this).attr('name')] = 'on';
					} else {
						kb_config[$(this).attr('name')] = 'off';
					}

				}
			});
		}

		postData.kb_config = kb_config;

		epkb_send_ajax ( current_wizard, postData, function( response ) {

			if ( wizard_type == 'ordering' ) {  // for Ordering  Wizard
				if ( ! response.error && typeof response.message !== 'undefined' ) {

				}
			}  else if ( wizard_type == 'global' ) {  // for Global  Wizard
				if ( ! response.error && typeof response.message !== 'undefined' ) {

					current_wizard.find( '#epkb-wsb-step-2-panel' ).removeClass( 'epkb-wc-step-panel--active' );
					current_wizard.find( '#epkb-wsb-step-3-panel' ).addClass( 'epkb-wc-step-panel--active' ).show();

					// refresh page
					location.reload();
				}
			}

		}, false, epkb_vars.save_config);
	});

	/** Remove loader, should be last function in this file */
	// We need timeout to skip all start accordion animations
	setTimeout(function(){
		$( '.epkb-admin-dialog-box-loading' ).remove();
		$( '.epkb-admin-dialog-box-overlay' ).remove();
	}, 500);
});
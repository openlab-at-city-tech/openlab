jQuery(document).ready(function($) {

	var epkb = $( '#ekb-admin-page-wrap' );

	// Set special CSS class to #wpwrap for only KB admin pages
	if ( $( epkb ).find( '.epkb-admin__content' ).length > 0 ) {
		$( '#wpwrap' ).addClass( 'epkb-admin__wpwrap' );
	}

	let remove_message_timeout = false;
	let $confirmation_dialog = $( '#epkb-admin-page-reload-confirmation' );


	/*************************************************************************************************
	 *
	 *          KB CONFIGURATION PAGE
	 *
	 ************************************************************************************************/

	// KBs DROPDOWN - reload on change
	$( '#epkb-list-of-kbs' ).on( 'change', function(e) {

		let selected_option = $( this ).find( 'option:selected' );

		// Do nothing for options added by hook (they should execute their own JS)
		if ( selected_option.attr( 'data-plugin' ) !== 'core' ) {
			return;
		}

		// Redirect if user does not have access for the current page in the selected KB
		if ( selected_option.val() === 'closed' ) {
			window.location = selected_option.attr( 'data-target' );
			return;
		}

		let current_location_href = window.location.href;

		// Handle archived KBs page
		if ( $( this ).val() === 'archived' ) {
			let location_parts = window.location.href.split( '#' );
			window.location = location_parts[0] + '&archived-kbs=on';
			return;
		} else {
			current_location_href = current_location_href.replaceAll( '&archived-kbs=on', '' ).replaceAll( '&epkb_after_kb_setup', '' );
		}

		// Handle external link - Open link in new tab and stay on the previous item selected in the dropdown
		let data_link = selected_option.attr( 'data-link' );
		if ( typeof data_link !== 'undefined' && data_link.length > 0 ) {
			window.open( data_link, '_blank' );
			$( this ).val( $( this ).attr( 'data-active-kb-id' ) ).trigger( 'change' );
			return;
		}

		let prev_kb_id = $( this ).attr( 'data-active-kb-id' );
		let kb_id = $( this ).val();
		if ( kb_id ) {
			$( this ).attr( 'data-active-kb-id', kb_id );

			// Set cookie for KB id when user changes KB from the dropdown
			const d = new Date();
			d.setTime( d.getTime() + ( 24 * 60 * 60 * 1000 ) );
			document.cookie = 'eckb_kb_id=' + kb_id + ';' + 'expires=' + d.toUTCString(); + ';path=/;samesite:strict';

			window.location.href = current_location_href.replaceAll( 'epkb_post_type_' + prev_kb_id, 'epkb_post_type_' + kb_id );
		}
	});

	// Save Access Control settings
	$( '#epkb_save_access_control' ).on( 'click', function() {
		epkb_send_ajax(
			{
				action: 'epkb_save_access_control',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				epkb_kb_id: $( '#epkb-list-of-kbs' ).val(),
				admin_eckb_access_need_help_read: $( '#admin_eckb_access_need_help_read input[type="radio"]:checked' ).val(),
				admin_eckb_access_search_analytics_read: $( '#admin_eckb_access_search_analytics_read input[type="radio"]:checked' ).val(),
				admin_eckb_access_addons_news_read: $( '#admin_eckb_access_addons_news_read input[type="radio"]:checked' ).val(),
				admin_eckb_access_order_articles_write: $( '#admin_eckb_access_order_articles_write input[type="radio"]:checked' ).val(),
				admin_eckb_access_frontend_editor_write: $( '#admin_eckb_access_frontend_editor_write input[type="radio"]:checked' ).val(),
				admin_eckb_access_faqs_write: $( '#admin_eckb_access_faqs_write input[type="radio"]:checked' ).val()
			},
			function( response ) {
				$( '.eckb-top-notice-message' ).remove();
				if ( typeof response.message !== 'undefined' ) {
					clear_bottom_notifications();
					$( 'body' ).append( response.message );
				}
				clear_message_after_set_time();
			}
		);
	});

	// open panel
	$('#epkb-admin__boxes-list__tools .epkb-kbnh__feature-links .epkb-primary-btn').on('click', function(){

		let id = $(this).prop('id');

		if ( id == 'epkb_core_export' ) {
			$('form.epkb-export-kbs').submit();
			return false;
		}

		if ( $('.epkb-kbnh__feature-panel-container--' + id).length == 0 ) {
			return false;
		}

		$(this).closest('.epkb-setting-box__list').find('.epkb-kbnh__feature-container').css({'display' : 'none'});
		$(this).closest('.epkb-setting-box__list').find('.epkb-kbnh__feature-panel-container--' + id).css({'display' : 'block'});

		return false;
	});

	// back button
	$(document).on( 'epkb_hide_export_import_panels', function(){
		$('#epkb-admin__boxes-list__tools .epkb-setting-box__list>.epkb-kbnh__feature-container').css({'display' : 'flex'});
		$('#epkb-admin__boxes-list__tools .epkb-setting-box__list>.epkb-kbnh__feature-panel-container').css({'display' : 'none'});
	} );

	$('.epkb-kbnh-back-btn').on('click', function(){
		$(document).trigger('epkb_hide_export_import_panels');
		return false;
	});

	/*************************************************************************************************
	 *
	 *          ADMIN PAGES
	 *
	 ************************************************************************************************/

	/* Admin Top Panel Items -----------------------------------------------------*/
	$( '.epkb-admin__top-panel__item' ).on( 'click', function() {

		let active_top_panel_item_class = 'epkb-admin__top-panel__item--active';
		let active_boxes_list_class = 'epkb-admin__boxes-list--active';
		let active_secondary_panel_class = 'epkb-admin__secondary-panel--active';
		let active_secondary_item_class = 'epkb-admin__secondary-panel__item--active';

		// Do nothing for already active item, only trigger secondary item to make sure we have correct hash in URL
		if ( $( this ).hasClass( active_top_panel_item_class ) ) {
			let active_secondary_item = $( active_secondary_panel_class ).find( '.' + active_secondary_item_class ).length
				? $( active_secondary_panel_class ).find( '.' + active_secondary_item_class )
				: $( $( active_secondary_panel_class ).find( '.epkb-admin__secondary-panel__item' )[0] );
			setTimeout( function () { active_secondary_item.trigger( 'click' ); }, 100 );
			return;
		}

		let list_key = $( this ).attr( 'data-target' );

		// Change class for active Top Panel item
		$( '.epkb-admin__top-panel__item' ).removeClass( active_top_panel_item_class );
		$( this ).addClass( active_top_panel_item_class );

		// Change class for active Boxes List
		$( '.epkb-admin__boxes-list' ).removeClass( active_boxes_list_class );
		$( '#epkb-admin__boxes-list__' + list_key ).addClass( active_boxes_list_class );

		// Change class for active Secondary Panel and trigger click event on active secondary tab to initialize JS and AJAX loading content
		$( '.epkb-admin__secondary-panel' ).removeClass( active_secondary_panel_class );
		let active_secondary_panel = $( '#epkb-admin__secondary-panel__' + list_key ).addClass( active_secondary_panel_class );
		let active_secondary_item = active_secondary_panel.find( '.' + active_secondary_item_class ).length
			? active_secondary_panel.find( '.' + active_secondary_item_class )
			: $( active_secondary_panel.find( '.epkb-admin__secondary-panel__item' )[0] );
		setTimeout( function () { active_secondary_item.trigger( 'click' ); }, 100 );

		// Licenses tab on Add-ons page - support for existing add-ons JS handlers
		let active_top_panel_item = this;
		setTimeout( function () {
			if ( $( active_top_panel_item ).attr( 'id' ) === 'eckb_license_tab' ) {
				$( '#eckb_license_tab' ).trigger( 'click' );
			}
		}, 100 );

		// track event if user visited 'Features' tab first time
		if ( list_key === 'features' && ! $( this ).hasClass( 'epkb-admin__flag--visited' ) ) {
			$.ajax( {
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'epkb_features_tab_visited',
					_wpnonce_epkb_ajax_action: epkb_vars.nonce
				},
				url: ajaxurl
			} ).done( function() {
				$( '#epkb-admin__step-cta-box__features .epkb-admin__step-cta-box__header' ).after( '<span class="epkb-admin__step-cta-box__content__icon epkbfa epkbfa-check-circle"></span>' );
			});
		}

		// Update anchor
		window.location.hash = '#' + list_key;
	});

	// Open iframe with editor
	$( document ).on('click', '.epkb-main-page-editor-link a, .epkb-article-page-editor-link a, .epkb-archive-page-editor-link a, .epkb-search-page-editor-link a', function( e ){
		if ( $('[name=editor_backend_mode]:checked').length == 0 || $('[name=editor_backend_mode]:checked').val() == 0 ) {
			return true;
		}

		let link_href = $( this ).prop( 'href' );
		$( 'body' ).append(`
			<div class="epkb-editor-popup" id="epkb-editor-popup">
				<iframe src="${link_href}" ></iframe>
			</div>
		`);

		e.stopPropagation();
		return false;
	} );

	// Do not close Editor backend mode when click inside it
	$( 'body' ).on('click', '.epkb-editor-popup', function(e){
		e.stopPropagation();
	} );

	// Let tabs open via triggering their click events before initialize close event for Editor backend mode
	setTimeout( function () {
		// Close Editor backend mode when click outside it
		$( 'body' ).on( 'click', function() {
			$( '.epkb-editor-popup' ).remove();
		} );
	}, 3000 );

	// Set correct active tab after the page reloading
	(function(){
		let url_parts = window.location.href.split( '#' );

		// Set first item as active if there is no any anchor
		if ( url_parts.length === 1 ) {
			$( $( '.epkb-admin__top-panel__item' )[0] ).trigger( 'click' );
			return;
		}

		let target_keys = url_parts[1].split( '__' );
		if ( target_keys.length === 0 ) {
			return;
		}

		let target_main_items = $( '.epkb-admin__top-panel__item[data-target="' + target_keys[0] + '"]' );

		// If no target items was found, then set the first item as active
		if ( target_main_items.length === 0 ) {
			$( $( '.epkb-admin__top-panel__item' )[0] ).trigger( 'click' );
			return;
		}

		// Change class for active item
		$( target_main_items[0] ).trigger( 'click' );

		// Key for vertical tabs on settings panel
		let admin_form_tab = target_keys[0] === 'settings' && target_keys.length > 1 ? $( '.epkb-admin__form-tab[data-target="' + target_keys[1] + '"]' ) : '';
		if ( admin_form_tab.length ) {
			switch_admin_form_tab( admin_form_tab );

			// Key for vertical sub-tabs on settings panel
			let admin_form_sub_tab = target_keys.length > 2 ? $( '.epkb-admin__form-sub-tab[data-target="' + target_keys[2] + '"]' ) : '';
			if ( admin_form_sub_tab.length ) {
				switch_admin_form_sub_tab( admin_form_sub_tab );
			}

			// Key for row with certain module
			if ( target_keys.length > 2 && target_keys[2].indexOf( 'module--' ) > -1 ) {
				let module_name = target_keys[2].replace( 'module--', '' );
				admin_form_sub_tab = $( '.epkb-admin__form-sub-tab[data-selected-module="' + module_name + '"]' );
				if ( admin_form_sub_tab.length ) {
					switch_admin_form_sub_tab( admin_form_sub_tab );
				}
			}

			// Move to box and highlight background
			if ( target_keys.length > 3 && target_keys[3].length ) {
				let target_box_keys = target_keys[3].split( '--' );
				let target_boxes_selector = '';
				let active_tab_level_class = $( '.epkb-admin__form-sub-tab-wrap--active' ).length ? '.epkb-admin__form-sub-tab-wrap--active' : '.epkb-admin__form-tab-wrap--active';
				$.each( target_box_keys, function( index, value ) {
					target_boxes_selector += active_tab_level_class + ' ' + '.epkb-admin__form-tab-content[data-target="' + value + '"]' + ',' + ' ';
				} );
				target_boxes_selector = target_boxes_selector.slice( 0, -2 );
				let $target_boxes = $( target_boxes_selector );
				if ( $target_boxes.length ) {
					setTimeout(function(){
						$( [document.documentElement, document.body] ).animate({
							scrollTop: $( $target_boxes[0] ).offset().top - 50
						}, 700);
						$target_boxes.addClass( 'epkb-highlighted_config_box' );
					}, 200 );
				}
			}

			// Trigger target link if defined
			if ( target_keys.length > 4 ) {
				$( '.epkb-' + target_keys[4] + '-link a' ).trigger( 'click' );
			}

			return;
		}

		// Key for Secondary item was specified and it is not empty otherwise take the first Secondary item
		let target_secondary_item_selector = '.epkb-admin__secondary-panel__item[data-target="' + url_parts[1] + '"]';
		let target_secondary_item = target_keys.length > 1 && target_keys[1].length && $( target_secondary_item_selector ).length
			? $( target_secondary_item_selector )
			: $( '.epkb-admin__secondary-panel--active' ).find( '.epkb-admin__secondary-panel__item' )[0];

		// Change class for active item
		setTimeout( function() { $( target_secondary_item ).trigger( 'click' ); }, 100 );
	})();

	/* Admin Secondary Panel Items -----------------------------------------------*/
	$( '.epkb-admin__secondary-panel__item' ).on( 'click', function() {

		let active_secondary_panel_item_class = 'epkb-admin__secondary-panel__item--active';
		let active_secondary_boxes_list_class = 'epkb-setting-box__list--active';

		// Do nothing for already active item, only make sure we have correct hash in URL
		if ( $( this ).hasClass( active_secondary_panel_item_class ) ) {
			window.location.hash = '#' + $( this ).attr( 'data-target' );
			return;
		}

		let list_key = $( this ).attr( 'data-target' );
		let parent_list_key = list_key.split( '__' )[0];

		// Change class for active Top Panel item
		$( '#epkb-admin__secondary-panel__' + parent_list_key ).find( '.epkb-admin__secondary-panel__item' ).removeClass( active_secondary_panel_item_class );
		$( this ).addClass( active_secondary_panel_item_class );

		// Change class for active Boxes List
		$( '#epkb-admin__boxes-list__' + parent_list_key ).find( '.epkb-setting-box__list' ).removeClass( active_secondary_boxes_list_class );
		$( '#epkb-setting-box__list-' + list_key ).addClass( active_secondary_boxes_list_class );

		// Update anchor
		window.location.hash = '#' + list_key;
	});

	/* Tabs ----------------------------------------------------------------------*/
	(function(){

		/**
		 * Toggles Tabs
		 *
		 * The HTML Structure for this is as follows:
		 * 1. tab_nav_container must be the main ID or class element for the navigation tabs containing the tabs.
		 *    Those nav items must have a class of nav_tab.
		 *
		 * 2. tab_panel_container must be the main ID or class element for the panels. Those panel items must have
		 *    a class of ekb-admin-page-tab-panel
		 *
		 * @param tab_nav_container  ( ID/class containing the Navs )
		 * @param tab_panel_container ( ID/class containing the Panels
		 */
		(function(){
			function tab_toggle( tab_nav_container, tab_panel_container ){

				epkb.find( tab_nav_container+ ' > .nav_tab' ).on( 'click', function(){

					//Remove all Active class from Nav tabs
					epkb.find(tab_nav_container + ' > .nav_tab').removeClass('active');

					//Add Active class to clicked Nav
					$(this).addClass('active');

					//Remove Class from the tab panels
					epkb.find(tab_panel_container + ' > .ekb-admin-page-tab-panel').removeClass('active');

					//Set Panel active
					var number = $(this).index() + 1;
					epkb.find(tab_panel_container + ' > .ekb-admin-page-tab-panel:nth-child( ' + number + ' ) ').addClass('active');
				});
			}

			tab_toggle( '.add_on_container .epkb-main-nav > .epkb-admin-pages-nav-tabs', '#add_on_panels' );
			tab_toggle( '.epkb-main-nav > .epkb-admin-pages-nav-tabs', '#main_panels' );
			tab_toggle( '#help_tabs_nav', '#help_tab_panel' );
			tab_toggle( '#new_features_tabs_nav', '#new_features_tab_panel' );
		})();

	})();

	/* Toggle admin tabs  ----------------------------------------------------------------------*/
	$('.epkb-header__tab').on('click',function(e){

		let id = $( this ).attr( 'id' );

		// Clear all active classes
		$( '.epkb-header__tab' ).removeClass( 'epkb-header__tab--active' );
		$( '.epkb-content__tab' ).removeClass( 'epkb-content__tab--active' );
		$( this ).addClass( 'epkb-header__tab--active' );

		// Add Class to clicked on tab
		$( '#'+id+'_content' ).addClass( 'epkb-content__tab--active' );

	});

	/* Misc ----------------------------------------------------------------------*/
	(function(){

		// Delete All KBs Data
		epkb.find( '#epkb-delete-all-data__form' ).on( 'submit', function( e ) {
			e.preventDefault();

			$('#epkb-editor-delete-warning').addClass('epkb-dialog-box-form--active');
		});

		$('#epkb-editor-delete-warning .epkb-dbf__footer__accept').on('click', function(){

			$('#epkb-editor-delete-warning').removeClass('epkb-dialog-box-form--active');

			let form = $( '#epkb-delete-all-data__form' );
			let postData = {
				action: 'epkb_delete_all_kb_data',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				delete_text: form.find( 'input[name="epkb_delete_text"]' ).val(),
			};

			epkb_send_ajax( postData, function( response ) {

				if ( ! response.error && typeof response.message != 'undefined' ) {
					epkb_show_success_notification( response.message );
					epkb.find( '.epkb-delete-all-data__message' ).show();
					form.hide();
				}
			} );
		});

		// TOGGLE DEBUG
		epkb.find( '#epkb_toggle_debug' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			let postData = {
				action: 'epkb_toggle_debug',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// SHOW LOGS
		epkb.find( '#epkb_show_logs' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			let postData = {
				action: 'epkb_show_logs',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// RESET LOGS
		epkb.find( '#epkb_reset_logs' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			let postData = {
				action: 'epkb_reset_logs',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// TOGGLE ADVANCED SEARCH DEBUG
		epkb.find( '#epkb_enable_advanced_search_debug' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			let postData = {
				action: 'epkb_enable_advanced_search_debug',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// RESET SEQUENCE
		epkb.find( '#epkb_reset_sequence' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html( '' );

			let postData = {
				action: 'epkb_reset_sequence',
				epkb_kb_id: $( '#epkb-list-of-kbs' ).val(),
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function( response ) {
				$( '.eckb-top-notice-message' ).remove();
				if ( typeof response.message !== 'undefined' ) {
					clear_bottom_notifications();
					$( 'body' ).append( response.message );
				}
				clear_message_after_set_time();
			} );
		} );

		// SHOW SEQUENCE
		epkb.find( '#epkb_show_sequence' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html( '' );
			$('.epkb-show-sequence-wrap').html( '' );

			let postData = {
				action: 'epkb_show_sequence',
				epkb_kb_id: $( '#epkb-list-of-kbs' ).val(),
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function( response ) {
				$( '.eckb-top-notice-message' ).remove();
				if ( typeof response.message !== 'undefined' ) {
					clear_bottom_notifications();
					$( 'body' ).append( response.message );
					clear_message_after_set_time();
				}

				if ( typeof response.html !== 'undefined' ) {
					$('.epkb-show-sequence-wrap').html( response.html );
				}
			} );

			return false;
		} );

		// ADD-ON PLUGINS + OUR OTHER PLUGINS - PREVIEW POPUP
		 (function(){
			//Open Popup larger Image
			epkb.find( '.featured_img' ).on( 'click', function( e ){

				e.preventDefault();
				e.stopPropagation();

				epkb.find( '.image_zoom' ).remove();

				var img_src;
				var img_tag = $( this ).find( 'img' );
				if ( img_tag.length > 1 ) {
					img_src = $(img_tag[0]).is(':visible') ? $(img_tag[0]).attr('src') :
							( $(img_tag[1]).is(':visible') ? $(img_tag[1]).attr('src') : $(img_tag[2]).attr('src') );

				} else {
					img_src = $( this ).find( 'img' ).attr( 'src' );
				}

				$( this ).after('' +
					'<div id="epkb_image_zoom" class="image_zoom">' +
					'<img src="' + img_src + '" class="image_zoom">' +
					'<span class="close icon_close"></span>'+
					'</div>' + '');

				//Close Plugin Preview Popup
				$('html, body').on('click.epkb', function(){
					$( '#epkb_image_zoom' ).remove();
					$('html, body').off('click.epkb');
				});
			});
		})();

		// Info Icon for Licenses
		$( '#add_on_panels' ).on( 'click', '.ep_font_icon_info', function(){
			$( this ).parent().find( '.ep_font_icon_info_content').toggle();
		});

		// KB Search Query Parameter
		$( '#search_query_param' ).on( 'keyup', function( e ) {
			let val = $( this ).val();
			// allow only letters, numbers, dash, underscore
			if ( ! val.match( /^[a-zA-Z0-9-_]*$/ ) ) {
				$( this ).val( val.replace( /[^a-zA-Z0-9-_]/g, '' ) );
			}
		});

		$( '#epkb-search-query-parameter__form' ).on( 'submit', function( e ) {
			let form = $( this );
			let postData = {
				action: 'eckb_update_query_parameter',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				search_query_param: form.find( 'input[name="search_query_param"]' ).val(),
				epkb_kb_id: $( '#epkb-list-of-kbs' ).val(),
			};

			epkb_send_ajax( postData, function( response ) {
				$( '.eckb-top-notice-message' ).remove();
				if ( typeof response.message !== 'undefined' ) {
					clear_bottom_notifications();
					$( 'body' ).append( response.message );
				}
				clear_message_after_set_time();
			} );

			return false;
		});
	})();

	// Copy to clipboard button
	$( '.epkb-copy-to-clipboard-box-container .epkb-ctc__copy-button' ).on( 'click', function( e ){
		e.preventDefault();
		let textarea = document.createElement( 'textarea' );
		let $container = $( this ).closest( '.epkb-copy-to-clipboard-box-container' );
		textarea.value = $container.find( '.epkb-ctc__embed-code' ).text();
		textarea.style.position = 'fixed';
		document.body.appendChild( textarea );
		textarea.focus();
		textarea.select();
		document.execCommand( 'copy' );
		textarea.remove();

		$container.find( '.epkb-ctc__embed-code' ).css( { 'opacity': 0 } );
		$container.find( '.epkb-ctc__embed-notification' ).fadeIn( 200 );

		setTimeout( function() {
			$container.find( '.epkb-ctc__embed-code' ).css( { 'opacity': 1 } );
			$container.find( '.epkb-ctc__embed-notification' ).hide();
		}, 1500 );
	});

	/*************************************************************************************************
	 *
	 *          ANALYTICS PAGE
	 *
	 ************************************************************************************************/
	var analytics_container = $( '.epkb-analytics-page-container' );

	//When Top Nav is clicked on show it's content.
	analytics_container.find( '.page-icon' ).on( 'click', function(){

		// Do nothing for already active page icon
		if ( $( this ).closest( '.eckb-nav-section' ).hasClass( 'epkb-active-nav' ) ) {
			return;
		}

		//Reset ( Hide all content, remove all active classes )
		analytics_container.find( '.eckb-config-content' ).removeClass( 'epkb-active-content' );
		analytics_container.find( '.eckb-nav-section' ).removeClass( 'epkb-active-nav' );

		//Get ID of Icon
		var id = $( this ).attr( 'id' );

		//Target Content from icon ID
		analytics_container.find( '#' + id + '-content').addClass( 'epkb-active-content' );

		//Set this Nav to be active
		analytics_container.find( this ).parents( '.eckb-nav-section' ).addClass( 'epkb-active-nav' )

	});


	/*************************************************************************************************
	 *
	 *          FAQS PAGE
	 *
	 ************************************************************************************************/
	let epkb_editor_update_timer = false;

	let faq_question_form = {
		faq_id: 0,
		title: '',
		content: '',
	};

	// Create new FAQ Group
	$( document ).on( 'click', '#epkb-faq-create-group', function() {

		let faqs_group_form = $( '#epkb-faq-group-form' );

		// Load default FAQ Group to form
		let group_id = faqs_group_form.data( 'default-faq-group-id' );
		let group_name = faqs_group_form.data( 'default-faq-group-name');
		faqs_group_form.data( 'faq-group-id', group_id );
		faqs_group_form.find( '.epkb-faq-group-form-head__title' ).html( group_name );
		faqs_group_form.find( '[name="faq-group-name"]' ).val( group_name );
		faqs_group_form.find( '.epkb-faq-questions-list' ).html( '' );
		//faqs_group_form.find( '[name="faq-group-status"]' ).prop( 'checked', false );

		// Show FAQ Group form and Available Questions list
		faqs_group_form.addClass( 'epkb-faq-group-form--active' );
		$( '#epkb-available-questions-container' ).addClass( 'epkb-available-questions-container--active' );

		// Hide FAQ Groups list
		$( '#epkb-faq-groups-list' ).addClass( 'epkb-faq-groups-list--hide' );
	});

	// Delete FAQ Group
	$( document ).on( 'click', '#epkb-faq-group-form .epkb-delete-icon', function () {

		let faqs_group_form = $( '#epkb-faq-group-form' );

		let postData = {
			action: 'epkb_delete_faq_group',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce
		};

		// Set FAQ Group data in the way that allows to sanitize fields separately
		let faqs_group_id = faqs_group_form.data( 'faq-group-id' );
		postData.faq_group_id = faqs_group_id;

		epkb_send_ajax( postData, function( response ) {

			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );

				// Delete current FAQ Group Container in FAQ Groups tab
				$( '#epkb-admin__boxes-list__faqs-groups .epkb-faq-group-container--' + faqs_group_id ).remove();

				// Delete current FAQ Group Container in FAQ Shortcodes tab
				$( '#epkb-admin__boxes-list__faq-shortcodes .epkb-faq-group--' + faqs_group_id ).remove();

				sort_faq_groups_in_all_lists();
				update_faq_shortcode_preview();
			}

			// Hide FAQ Group form and Available Questions list
			faqs_group_form.removeClass( 'epkb-faq-group-form--active' );
			$( '#epkb-available-questions-container' ).removeClass( 'epkb-available-questions-container--active' );

			// Show FAQ Groups list
			$( '#epkb-faq-groups-list' ).removeClass( 'epkb-faq-groups-list--hide' );
		} );
	} );

	// Edit FAQ Group
	$( document ).on( 'click', '#epkb-kb-faqs-page-container .epkb-faq-group-container .epkb-faq-group-head__edit', function () {

		let faqs_group_form = $( '#epkb-faq-group-form' );
		let available_faqs_container = $( '#epkb-available-questions-container' );

		// Get the current FAQ Group Container
		let current_faq_group_container = $( this ).closest( '.epkb-faq-group-container' );

		// Load the current FAQ Group to form
		let faq_group_id = current_faq_group_container.data( 'faq-group-id' );
		let faq_group_name = current_faq_group_container.find( '.epkb-faq-group-head__title' ).html();
		//let faq_group_status = current_faq_group_container.data( 'faq-group-status' );
		faqs_group_form.data( 'faq-group-id', faq_group_id );
		faqs_group_form.find( '.epkb-faq-group-form-head__title' ).html( faq_group_name );
		// faq_group_name = faq_group_name.replace("[Draft]", "");
		faqs_group_form.find( '[name="faq-group-name"]' ).val( faq_group_name );
		//faqs_group_form.find( '[name="faq-group-status"]' ).prop( 'checked', faq_group_status === 'publish' );

		// Load FAQs of the current Group to form and show only excluded FAQs in available list
		faqs_group_form.find( '.epkb-faq-questions-list' ).html( '' );
		current_faq_group_container.find( '.epkb-faq-question' ).each( function() {
			let faq_id = $( this ).data( 'faq-id' );
			let faq = available_faqs_container.find( '.epkb-faq-question--' + faq_id ).clone();
			available_faqs_container.find( '.epkb-faq-question--' + faq_id ).addClass( 'epkb-faq-question--hide' );
			faq.find( '.epkb-faq-question__action-include' ).remove();
			faqs_group_form.find( '.epkb-faq-questions-list' ).append( faq );
		} );

		check_no_faqs_message();

		// Show FAQ Group form and Available Questions list
		faqs_group_form.addClass( 'epkb-faq-group-form--active' );
		available_faqs_container.addClass( 'epkb-available-questions-container--active' );

		// Hide FAQ Groups list
		$( '#epkb-faq-groups-list' ).addClass( 'epkb-faq-groups-list--hide' );
	} );

	// Close FAQs Group form
	$( document ).on( 'click', '#epkb-faq-group-form .epkb-faq-group-form-head__close', function () {

		// Hide FAQ Group form and Available Questions list
		$( '#epkb-faq-group-form' ).removeClass( 'epkb-faq-group-form--active' );
		$( '#epkb-available-questions-container' ).removeClass( 'epkb-available-questions-container--active' );

		// Restore visibility of FAQs in Available Questions list
		$( '#epkb-available-questions-container .epkb-faq-question' ).removeClass( 'epkb-faq-question--hide' );

		// Show FAQ Groups list
		$( '#epkb-faq-groups-list' ).removeClass( 'epkb-faq-groups-list--hide' );
	} );

	// Save FAQs Group form
	$( document ).on( 'click', '#epkb-faq-group-form .epkb-faq-group-form-head__save', function () {

		let faqs_group_form = $( '#epkb-faq-group-form' );

		let postData = {
			action: 'epkb_save_faq_group',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce
		};

		// Set FAQ Group data in the way that allows to sanitize fields separately
		postData.faq_group_id = faqs_group_form.data( 'faq-group-id' );
		postData.faq_group_name = faqs_group_form.find( '[name="faq-group-name"]' ).val();
		//postData.faq_group_status = faqs_group_form.find( '[name="faq-group-status"]' ).prop( 'checked' ) ? 'publish' : 'draft';

		// Set FAQs in the current Group
		postData.faqs_order_sequence = [];
		faqs_group_form.find( '.epkb-faq-question' ).each( function() {
			postData.faqs_order_sequence.push( $( this ).data( 'faq-id' ) );
		} );

		epkb_send_ajax( postData, function( response ) {

			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );
			}

			if ( ! response.faq_group_id ) {
				return;
			}

			// Update existing add new FAQ Group in FAQ Groups tab
			if ( $( '#epkb-admin__boxes-list__faqs-groups .epkb-faq-group-container--' + response.faq_group_id ).length ) {
				$( '#epkb-admin__boxes-list__faqs-groups .epkb-faq-group-container--' + response.faq_group_id ).replaceWith( response.faq_group_html );
			} else {
				$( '#epkb-admin__boxes-list__faqs-groups .epkb-body-col--right' ).append( response.faq_group_html );
			}

			// Update existing add new FAQ Group in FAQ Shortcodes tab
			if ( $( '#epkb-admin__boxes-list__faq-shortcodes .epkb-faq-group--' + response.faq_group_id ).length ) {
				$( '#epkb-admin__boxes-list__faq-shortcodes .epkb-faq-group--' + response.faq_group_id ).replaceWith( response.shortcode_group_html );
			} else {
				$( '#epkb-admin__boxes-list__faq-shortcodes .epkb-body-col--right' ).append( response.shortcode_group_html );
			}

			// Update FAQ Group form
			faqs_group_form.data( 'faq-group-id', response.faq_group_id );
			faqs_group_form.find( '[name="faq-group-name"]' ).val( response.faq_group_name );
			faqs_group_form.find( '.epkb-faq-group-form-head__title' ).html( response.faq_group_name );

			sort_faq_groups_in_all_lists();
			update_faq_shortcode_preview();
		} );
	} );

	// Include FAQ to Group
	$( document ).on( 'click', '#epkb-available-questions-container .epkb-faq-question', function () {

		let faq = $( this ).clone();
		faq.find( '.epkb-faq-question__action-include' ).remove();

		// Add FAQ to form
		$( '#epkb-faq-group-form .epkb-faq-questions-list' ).prepend( faq );

		// Hide FAQ inside available FAQs list
		$( this ).addClass( 'epkb-faq-question--hide' );

		check_no_faqs_message();
	} );

	// Enable ordering for FAQs inside FAQ Group form
	$( '#epkb-faq-group-form .epkb-faq-questions-list' ).sortable( {
		axis: 'y',
		forceHelperSize: true,
		forcePlaceholderSize: true,
		handle: '.epkb-faq-question__action-order',
		opacity: 0.8,
		placeholder: 'epkb-sortable-placeholder',
	} );

	// Exclude FAQ from Group
	$( document ).on( 'click', '#epkb-faq-question-wp-editor-popup .epkb__help_editor__action__remove-from-group', function ( e ) {
		e.preventDefault();
		e.stopPropagation();

		let faq_form = $( '#epkb-faq-question-wp-editor-popup' );
		let faq_id = faq_form.find( '[name="faq-id"]' ).val();

		// Remove current FAQ from the form
		$( '#epkb-faq-group-form .epkb-faq-questions-list .epkb-faq-question--' + faq_id ).remove();

		// Show current FAQ in the available FAQs list
		$( '#epkb-available-questions-container .epkb-faq-question--' + faq_id ).removeClass( 'epkb-faq-question--hide' );

		check_no_faqs_message();

		// Hide FAQ form
		faq_form.removeClass( 'epkb-faq-question-wp-editor-popup--active' );

		return false;
	} );

	// Update FAQ Group Name
	$( document ).on( 'input', '#epkb-faq-group-form [name="faq-group-name"]', function () {
		$( '#epkb-faq-group-form .epkb-faq-group-form-head__title' ).html( $( this ).val() );
	} );

	// Create new FAQ
	$( document ).on( 'click', '#epkb-faq-create-question',function() {
		show_faq_question_form();
	} );

	// Edit FAQ in current Group form
	$( document ).on( 'click', '#epkb-faq-group-form .epkb-faq-question__action-edit', function() {
		$( '.epkb-faq-question-wp-editor__action__delete' ).removeClass( 'epkb-faq-question-wp-editor__action__delete--active' );
		$( '.epkb__help_editor__action__remove-from-group' ).addClass( 'epkb__help_editor__action__remove-from-group--active' );
		show_faq_question_form( $( this ).closest( '.epkb-faq-question' ).data( 'faq-id' ) );
	} );

	// Edit FAQ inside All FAQs list
	$( document ).on( 'click', '#epkb-all-faqs-container .epkb-faq-question__action-edit', function() {
		$( '.epkb-faq-question-wp-editor__action__delete' ).addClass( 'epkb-faq-question-wp-editor__action__delete--active' );
		$( '.epkb__help_editor__action__remove-from-group' ).removeClass( 'epkb__help_editor__action__remove-from-group--active' );
		show_faq_question_form( $( this ).closest( '.epkb-faq-question' ).data( 'faq-id' ) );
	} );

	// Hide Question Editor popup
	$( document ).on( 'click', '#epkb-faq-question-wp-editor-popup .epkb__help_editor__action__cancel, .epkb-faq-question-wp-editor__overlay', function( e ) {
		e.preventDefault();
		clearInterval( epkb_editor_update_timer );
		$( '#epkb-faq-question-wp-editor-popup' ).removeClass( 'epkb-faq-question-wp-editor-popup--active' );
		return false;
	} );

	// Save FAQ
	$( document ).on( 'submit', '#epkb-faq-question-wp--form', function( e ){

		e.preventDefault();
		calculate_faq_characters_counter();
		save_faq_question_form_to_object();

		let postData = {
			action: 'epkb_save_faq',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce
		};

		// Set questions data in the way that allows to sanitize fields separately
		postData.faq_id = faq_question_form.faq_id;
		postData.faq_title = faq_question_form.title;
		postData.faq_content = faq_question_form.content;

		epkb_send_ajax( postData, function( response ){

			if ( ! response.error && typeof response.message != 'undefined' ) {

				epkb_show_success_notification( response.message );

				// Update existing or add new FAQ in the lists
				if ( $( '#epkb-kb-faqs-page-container .epkb-faq-question--' + response.data.faq_id ).length ) {
					$( '#epkb-kb-faqs-page-container .epkb-faq-question--' + response.data.faq_id + ' .epkb-faq-question__title' ).text( response.data.title );
				} else {
					$( '#epkb-available-questions-container .epkb-available-questions-body' ).append( response.data.faq_html );
					$( '#epkb-all-faqs-container .epkb-body-col--left' ).append( response.data.faq_html );
					$( '#epkb-all-faqs-container .epkb-faq-question--' + response.data.faq_id ).find( '.epkb-faq-question__action-include, .epkb-faq-question__action-order' ).remove();
				}

				sort_faqs_in_all_lists();
				check_no_faqs_message();

				$( '#epkb-faq-question-wp-editor-popup' ).removeClass( 'epkb-faq-question-wp-editor-popup--active' );
			}
		} );

		return false;
	});

	// Delete FAQ
	$( document ).on( 'click', '#epkb-kb-faqs-page-container #epkb_delete_faq', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		let faq_form = $( '#epkb-faq-question-wp-editor-popup' );
		let faq_id = faq_form.find( '[name="faq-id"]' ).val();

		let postData = {
			action: 'epkb_delete_faq',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			faq_id: faq_id
		};

		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {

				epkb_show_success_notification( response.message );

				// Delete FAQ from all lists
				$( '#epkb-kb-faqs-page-container .epkb-faq-question--' + faq_id ).remove();

				sort_faqs_in_all_lists();
				check_no_faqs_message();

				// Hide FAQ form
				faq_form.removeClass( 'epkb-faq-question-wp-editor-popup--active' );
			}
		} );

		return false;
	} );

	function calculate_faq_characters_counter() {

		// Question
		let question_title = $( '#epkb-faq-wp-editor__faq-title' );
		if ( question_title.length ) {
			let question_length = question_title.val().length;
			if ( question_length > 200 ) {
				$( '.epkb-faq-question-wp-editor__question .epkb-characters_left-counter' ).text( 200 );
				question_title.val( question_title.val().substring( 0, 200 ) );
			} else {
				$( '.epkb-faq-question-wp-editor__question .epkb-characters_left-counter' ).text( question_length );
			}
		}

		// Answer - limit to 1500 max
		/* if ( $( '#epkb-faq-question-wp-editor' ).length ) {

			let editor = tinymce.get( 'epkb-faq-question-wp-editor' );
			let answer = '';

			if ( editor && $( '.wp-editor-wrap' ).hasClass( 'tmce-active' ) ) {
				answer = editor.getContent();
			} else {
				answer = $( '#epkb-faq-question-wp-editor' ).val();
			}

			if ( answer.length > 1500 ) {
				answer = answer.substring( 0, 1500 );

				if ( editor ) {
					editor.setContent( answer );
				}

				$( '#epkb-faq-question-wp-editor' ).val( answer );
			}
		} */
	}

	// Save FAQ form data to object
	function save_faq_question_form_to_object() {

		// Save from tinymce to textarea
		tinyMCE.triggerSave();

		// Save FAQ id
		faq_question_form.faq_id = $( '#epkb-faq-editor-id' ).val();

		// Save FAQ title
		faq_question_form.title = $( '#epkb-faq-wp-editor__faq-title' ).val();

		// Save FAQ Content
		faq_question_form.content = $( '#epkb-faq-question-wp-editor' ).val();
	}

	// Get data about the question and fill the form
	function show_faq_question_form( faq_id ) {

		epkb_editor_update_timer = setInterval( calculate_faq_characters_counter, 1000 );

		// Clear question data
		faq_question_form = {
			faq_id: 0,
			title: '',
			content: '',
		};


		// set value to empty
		$( '#epkb-faq-wp-editor__faq-title' ).val( '' );
		$( '#epkb-faq-question-wp-editor' ).val( '' );

		// New question
		if ( typeof faq_id == 'undefined' || ! faq_id ) {
			$( '#epkb-faq-question-wp-editor-popup' ).addClass( 'epkb-faq-question-wp-editor-popup--active' );
			update_faq_question_form();
			return;
		}

		// Get existing question data to fill wp editor
		let postData = {
			action: 'epkb_get_faq',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			faq_id: faq_id
		};

		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.data != 'undefined' ) {
				$( '#epkb-faq-question-wp-editor-popup' ).addClass( 'epkb-faq-question-wp-editor-popup--active' );

				faq_question_form = {
					faq_id: response.data.faq_id,
					title: response.data.title,
					content: response.data.content,
				};

				update_faq_question_form();
			}
		} );
	}

	// Fill editor with question_form data
	function update_faq_question_form() {

		// only for openned popup
		if ( ! $( '#epkb-faq-question-wp-editor-popup' ).hasClass( 'epkb-faq-question-wp-editor-popup--active' ) ) {
			return;
		}

		let editor = tinymce.get( 'epkb-faq-question-wp-editor' );

		// Fill the id
		$( '#epkb-faq-editor-id' ).val( faq_question_form.faq_id );

		// Fill the title
		$( '#epkb-faq-wp-editor__faq-title' ).val( faq_question_form.title );

		// Fill the editor or text editor tab
		if ( editor && $( '.wp-editor-wrap' ).hasClass( 'tmce-active' ) ) {
			editor.setContent( faq_question_form.content );
		} else {
			$( '#epkb-faq-question-wp-editor' ).val( faq_question_form.content );
		}

		$( '.epkb-characters_left-counter' ).text( faq_question_form.content.length + '' );
	}

	// Check visibility of no FAQs message
	function check_no_faqs_message() {

		// Available FAQs list
		if ( $( '#epkb-available-questions-container .epkb-faq-question:not(.epkb-faq-question--hide)' ).length ) {
			$( '#epkb-available-questions-container .epkb-faq-questions-list-empty' ).removeClass( 'epkb-faq-questions-list-empty--active' );
		} else {
			$( '#epkb-available-questions-container .epkb-faq-questions-list-empty' ).addClass( 'epkb-faq-questions-list-empty--active' );
		}

		// Available FAQs list
		if ( $( '#epkb-faq-group-form .epkb-faq-question' ).length ) {
			$( '#epkb-faq-group-form .epkb-faq-questions-list-empty' ).removeClass( 'epkb-faq-questions-list-empty--active' );
		} else {
			$( '#epkb-faq-group-form .epkb-faq-questions-list-empty' ).addClass( 'epkb-faq-questions-list-empty--active' );
		}

		// All FAQs list
		if ( $( '#epkb-all-faqs-container .epkb-faq-question' ).length ) {
			$( '#epkb-all-faqs-container .epkb-faq-questions-list-empty' ).removeClass( 'epkb-faq-questions-list-empty--active' );
		} else {
			$( '#epkb-all-faqs-container .epkb-faq-questions-list-empty' ).addClass( 'epkb-faq-questions-list-empty--active' );
		}
	}

	// Sort FAQs in all lists
	function sort_faqs_in_all_lists() {

		// Sort FAQs in available FAQs list
		if ( $( '#epkb-available-questions-container .epkb-faq-question' ).length > 1 ) {
			$( '#epkb-available-questions-container .epkb-faq-question' ).sort( function( a, b ) {
				return $( a ).find( '.epkb-faq-question__title' ).text() > $( b ).find( '.epkb-faq-question__title' ).text() ? 1 : -1;
			} ).appendTo( '#epkb-available-questions-container .epkb-available-questions-body' );
		}

		// Sort FAQs in all FAQs list (call sort() even if there is one FAQ is available to update columns properly)
		if ( $( '#epkb-all-faqs-container .epkb-faq-question' ).length > 0 ) {
			$( '#epkb-all-faqs-container .epkb-faq-question' ).sort( function( a, b ) {
				return $( a ).find( '.epkb-faq-question__title' ).text() > $( b ).find( '.epkb-faq-question__title' ).text() ? 1 : -1;
			} ).appendTo( '#epkb-all-faqs-container .epkb-body-col--right' );
			while ( $( '#epkb-all-faqs-container .epkb-body-col--left .epkb-faq-question' ).length < $( '#epkb-all-faqs-container .epkb-body-col--right .epkb-faq-question' ).length ) {
				$( $( '#epkb-all-faqs-container .epkb-body-col--right .epkb-faq-question' )[0] ).appendTo( $( '#epkb-all-faqs-container .epkb-body-col--left' ) );
			}
		}
	}

	// Sort FAQ Groups in all lists
	function sort_faq_groups_in_all_lists() {

		// Sort FAQ Groups in FAQ Groups tab (call sort() even if there is one FAQ Group is available to update columns properly)
		if ( $( '#epkb-admin__boxes-list__faqs-groups .epkb-faq-group-container' ).length > 0 ) {
			$( '#epkb-admin__boxes-list__faqs-groups .epkb-faq-group-container' ).sort( function( a, b ) {
				return $( a ).find( '.epkb-faq-group-head__title' ).text() > $( b ).find( '.epkb-faq-group-head__title' ).text() ? 1 : -1;
			} ).appendTo( '#epkb-admin__boxes-list__faqs-groups .epkb-body-col--right' );
			while ( $( '#epkb-admin__boxes-list__faqs-groups .epkb-body-col--left .epkb-faq-group-container' ).length < $( '#epkb-admin__boxes-list__faqs-groups  .epkb-body-col--right .epkb-faq-group-container' ).length ) {
				$( $( '#epkb-admin__boxes-list__faqs-groups .epkb-body-col--right .epkb-faq-group-container' )[0] ).appendTo( $( '#epkb-admin__boxes-list__faqs-groups  .epkb-body-col--left' ) );
			}
		}

		// Sort FAQ Groups in FAQ Shortcodes tab (call sort() even if there is one FAQ Group is available to update columns properly)
		if ( $( '#epkb-admin__boxes-list__faq-shortcodes .epkb-faq-group' ).length > 0 ) {
			$( '#epkb-admin__boxes-list__faq-shortcodes .epkb-faq-group' ).sort( function( a, b ) {
				return $( a ).find( '.epkb-faq-group__title' ).text() > $( b ).find( '.epkb-faq-group__title' ).text() ? 1 : -1;
			} ).appendTo( '#epkb-admin__boxes-list__faq-shortcodes .epkb-body-col--right' );
			while ( $( '#epkb-admin__boxes-list__faq-shortcodes .epkb-body-col--left .epkb-faq-group' ).length < $( '#epkb-admin__boxes-list__faq-shortcodes .epkb-body-col--right .epkb-faq-group' ).length ) {
				$( $( '#epkb-admin__boxes-list__faq-shortcodes .epkb-body-col--right .epkb-faq-group' )[0] ).appendTo( $( '#epkb-admin__boxes-list__faq-shortcodes .epkb-body-col--left' ) );
			}
		}
	}

	function update_faq_shortcode_preview() {
		let all_faq_group_ids = [];
		$( '#epkb-admin__boxes-list__faqs-groups .epkb-faq-group-container' ).each( function() {
			all_faq_group_ids.push( $( this ).data( 'faq-group-id' ) );
		} );
		let updated_shortcode = $( '#epkb-faq-shortcode-container .epkb-ctc__embed-code' ).text().replaceAll( /group_ids="(.*?)"/g, 'group_ids="' + all_faq_group_ids.join( ',' ) + '"' );
		$( '#epkb-faq-shortcode-container .epkb-ctc__embed-code' ).text( updated_shortcode );
	}

	// FAQ Design presets
	$( document ).on( 'change', '#faq_shortcode_preset input', function( e ) {
		let preset_name = $( this ).val();
		let shortcode_content = $( '#epkb-faq-shortcode-container .epkb-ctc__embed-code' ).text();

		// Remove 'design' parameter from shortcode if selected default value
		if ( parseInt( preset_name ) === parseInt( $( this ).closest( '.epkb-input-group' ).data( 'default-value' ) ) ) {
			$( '#epkb-faq-shortcode-container .epkb-ctc__embed-code' ).text( shortcode_content.replaceAll( /\sdesign="(.*?)"/g, '' ) );
			return;
		}

		// Update/add 'design' parameter
		if ( shortcode_content.indexOf( 'design="' ) >= 0 ) {
			$( '#epkb-faq-shortcode-container .epkb-ctc__embed-code' ).text( shortcode_content.replaceAll( /design="(.*?)"/g, 'design="' + preset_name + '"' ) );
		} else {
			$( '#epkb-faq-shortcode-container .epkb-ctc__embed-code' ).text( shortcode_content.replaceAll( ']', ' ' + 'design="' + preset_name + '"]' ) );
		}
	} );

	/*************************************************************************************************
	 *
	 *          CATEGORY ICONS
	 *
	 ************************************************************************************************/
	if ($('.epkb-categories-icons').length) {
		// Tabs
		$('.epkb-categories-icons__button').on('click',function(){

			if ($(this).hasClass('epkb-categories-icons__button--active')) {
				return;
			}

			$('.epkb-categories-icons__button').removeClass('epkb-categories-icons__button--active');
			$(this).addClass('epkb-categories-icons__button--active');


			$('.epkb-categories-icons__tab-body').slideUp('fast');

			var val = $(this).data('type');

			if ( $('.epkb-categories-icons__tab-body--' + val).length ) {
				$('.epkb-categories-icons__tab-body--' + val).slideDown('fast');
			}

			$('#epkb_head_category_icon_type').val(val);
		});

		// Icon Save
		$('.epkb-icon-pack__icon').on('click',function(){
			$('.epkb-icon-pack__icon').removeClass('epkb-icon-pack__icon--checked');
			$(this).addClass('epkb-icon-pack__icon--checked');
			$('#epkb_head_category_icon_name').val($(this).data('key'));
		});

		// Image save
		$('.epkb-category-image__button').on('click',function(e){
			e.preventDefault();

			var button = $(this),
				custom_uploader = wp.media({
					title: button.data('title'),
					library : {
						type : 'image'
					},
					multiple: false
				}).on('select', function() {
					var attachment = custom_uploader.state().get('selection').first().toJSON();

					$('#epkb_head_category_icon_image').val(attachment.id);
					$('.epkb-category-image__button').removeClass('epkb-category-image__button--no-image');
					$('.epkb-category-image__button').addClass('epkb-category-image__button--have-image');
					$('.epkb-category-image__button').css({'background-image' : 'url('+attachment.url+')'});
				})
					.open();
		});

		// Show/Hide Categories block depends on category parent
		$('#parent').on( 'change', function(){

			var category_level;
			var option;
			var select = $(this);
			var template = $('#epkb_head_category_template').val();
			var hide_block = false;

			select.find('option').each(function(){
				if ( $(this).val() == select.val() ) {
					option = $(this);
				}
			});

			if ( option.val() == '-1' ) {
				category_level = 1;
			} else if ( option.hasClass('level-0') ) {
				category_level = 2;
			} else {
				category_level = 3;
			}

			if ( template == 'Tabs' ) {
				if ( category_level !== 2 ) {
					hide_block = true;
				}
			} else if ( template == 'Sidebar' ) {
				hide_block = true;
			} else {
				// all else layouts
				if ( category_level > 1 ) {
					hide_block = true;
				}
			}

			if ( hide_block ) {
				$('.epkb-categories-icons').hide();
				$('.epkb-categories-icons+.epkb-term-options-message').show();
			} else {
				$('.epkb-categories-icons').show();
				$('.epkb-categories-icons+.epkb-term-options-message').hide();
			}

		});

		function epkb_reset_categories_icon_box() {
			$('#epkb_font_icon').trigger('click');
			$('#epkb_head_category_thumbnail_size').val( $('#epkb_head_category_thumbnail_size').find('option').eq(0).val() );
			$('.epkb-category-image__button').addClass('epkb-category-image__button--no-image');
			$('.epkb-category-image__button').removeClass('epkb-category-image__button--have-image');
			$('.epkb-category-image__button').css({'background-image' : ''});
			$('#epkb_head_category_icon_image').val(0);
		}

		// look when new category was added
		$( document ).ajaxComplete(function( event, xhr, settings ) {

			if ( ! settings ) {
				return;
			}

			let data = settings.data.split('&');
			let i;

			for (i = 0; i < data.length; i++) {
				sParameterName = data[i].split('=');

				if (sParameterName[0] === 'action' && sParameterName[1] === 'add-tag' ) {
					epkb_reset_categories_icon_box();
					// remove draft checkbox
					$('[name=epkb_category_is_draft]').prop('checked', false);

					$("html, body").animate({ scrollTop: $('.wp-heading-inline').offset().top }, 300);
				}
			}
		});
	}

	/*************************************************************************************************
	 *
	 *          CATEGORY ORDER LINK
	 *
	 ************************************************************************************************/
	if ( $('#epkb-admin__categories_sorting_link').length ) {
		$('#epkb-admin__categories_sorting_link').insertAfter('.bulkactions');
		$('#epkb-admin__categories_sorting_link').css('display', 'block');
	}
	/*************************************************************************************************
	 *
	 *          AJAX calls
	 *
	 ************************************************************************************************/

	// generic AJAX call handler
	function epkb_send_ajax( postData, refreshCallback, callbackParam, reload, alwaysCallback, $loader ) {

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
				if ( typeof $loader == 'undefined' || $loader === false ) {
					epkb_loading_Dialog('show', '');
				}

				if ( typeof $loader == 'object' ) {
					epkb_loading_Dialog('show', '', $loader);
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
		}).always(function() {

			theResponse = (typeof theResponse === 'undefined') ? '' : theResponse;

			if ( typeof alwaysCallback == 'function' ) {
				alwaysCallback( theResponse );
			}

			if ( ! reload ) {
				epkb_loading_Dialog('remove', '');
			}

			if ( errorMsg ) {
				$('.eckb-bottom-notice-message').remove();
				$('body').append(errorMsg).removeClass('fadeOutDown');

				setTimeout( function() {
					$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
				}, 10000 );
				return;
			}

			if ( typeof refreshCallback === "function" ) {

				if ( typeof callbackParam === 'undefined' ) {
					refreshCallback(theResponse);
				} else {
					refreshCallback(theResponse, callbackParam);
				}
			} else {
				if ( reload ) {
					location.reload();
				}
			}
		});
	}


	/*************************************************************************************************
	 *
	 *          DIALOGS
	 *
	 ************************************************************************************************/

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

	// Close Button Message if Close Icon clicked
	$( document.body ).on( 'click', '.epkb-close-notice', function() {
		let bottom_message = $( this ).closest( '.eckb-bottom-notice-message' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	} );

	$( document.body ).on( 'click', '.eckb-bottom-notice-message__header__close', function() {
		let bottom_message = $( this ).closest( '.eckb-bottom-notice-message-large' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	} );
	
	// AJAX DIALOG USED BY KB CONFIGURATION AND SETTINGS PAGES
	$('#epkb-ajax-in-progress').dialog({
		resizable: false,
		height: 70,
		width: 200,
		modal: false,
		autoOpen: false
	}).hide();


	// New ToolTip
	epkb.on( 'click', '.epkb__option-tooltip__button', function(){
		const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
		let tooltip_on = tooltip_contents.css('display') == 'block';

		tooltip_contents.fadeOut();

		if ( ! tooltip_on ) {
			clearTimeout(timeoutOptionTooltip);
			tooltip_contents.fadeIn();
		}
	});
	let timeoutOptionTooltip;
	epkb.on( 'mouseenter', '.epkb__option-tooltip__button, .epkb__option-tooltip__contents', function(){
		const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
		clearTimeout(timeoutOptionTooltip);
		tooltip_contents.fadeIn();
	});

	epkb.on( 'mouseleave', '.epkb__option-tooltip__button, .epkb__option-tooltip__contents', function(){
		const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
		timeoutOptionTooltip = setTimeout( function() {
			tooltip_contents.fadeOut();
		}, 1000);
	});

	// ToolTip
	epkb.on( 'click', '.eckb-tooltip-button', function(){
		$( this ).parent().find( '.eckb-tooltip-contents' ).fadeToggle();
	});

	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? '<p>' + $message + '</p>': '') +
			'</span>' +
			'</div>' +
			'<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>' +
			'</div>';
	}

	function epkb_show_error_notification( $message, $title = '' ) {
		$('.eckb-bottom-notice-message').remove();
		$('body').append( epkb_admin_notification( $title, $message, 'error' ) );

		setTimeout( function() {
			$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
		}, 10000 );
	}

	function epkb_show_success_notification( $message, $title = '' ) {
		$('.eckb-bottom-notice-message').remove();
		$('body').append( epkb_admin_notification( $title, $message, 'success' ) );

		setTimeout( function() {
			$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
		}, 10000 );
	}

	/**
	 * Accordion for the options 
	 */
	$('body').on('click', '.eckb-wizard-accordion .eckb-wizard-option-heading', function(){
		var wrap = $(this).closest('.eckb-wizard-accordion');
		var currentItem = $(this).closest('.eckb-wizard-accordion__body-content');
		var isCurrentActive = currentItem.hasClass('eckb-wizard-accordion__body-content--active');

		wrap.find('.eckb-wizard-accordion__body-content').removeClass('eckb-wizard-accordion__body-content--active');
		
		if (!isCurrentActive) {
			currentItem.addClass('eckb-wizard-accordion__body-content--active');
		}
		
	});

	$('body').on('click', '#eckb-wizard-main-page-preview a, .epkb-wizard-theme-panel-container a, #eckb-wizard-article-page-preview a', false);

	//Admin Notice
	$('.epkb-notice-remind').on('click',function(e){
		e.preventDefault();
		$(this).parent().parent().remove();
	});

	//Dismiss ongoing notice
	$(document).on( 'click', '.epkb-notice-dismiss', function( event ) {
		event.preventDefault();

		$('#'+$(this).data('notice-id')).slideUp();

		var postData = {
			action: 'epkb_dismiss_ongoing_notice',
			epkb_dismiss_id: $(this).data('notice-id')
		};
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: postData
		});
	} );

	// Dismiss notification after successful completion of Setup Wizard
	$(document).on( 'click', '.epkb-kb__need-help__after-setup-wizard-dialog .epkb-notice-dismiss', function() {
		$( $( this ).data( 'target') ).slideUp();
	});

	// Shared handlers for close buttons of Dialog Box Form
	$('.epkb-dialog-box-form .epkb-dbf__close, .epkb-dialog-box-form .epkb-dbf__footer__cancel').on('click',function(){
		$(this).closest( '.epkb-dialog-box-form' ).toggleClass( 'epkb-dialog-box-form--active' );
	});
	$('.epkb-dialog-box-form .epkb-dbf__footer__accept__btn').on('click',function(){
		$(this).closest('.epkb-dialog-box-form').find('form').trigger( 'submit' );
	});

	// Reveal Settings ( Edit Button )
	$( 'body' ).find( '.epkb__header__edit' ).on( 'click', function(){
		$( this ).parent().parent().find('.epkb-ts__input-container').slideToggle();
		$( this ).parent().parent().find('.epkb-ts__action-container').slideToggle();
		$( this ).parents( '.epkb-toggle-setting-container' ).toggleClass( 'epkb-toggle-setting-container--active' );
	});



	// Admin Questionnaire item click
	$( 'body' ).on( 'click', '.eckb-Q__list__item-container', function(){

		$( this ).find('.eckb-Q__item__question__toggle-icon').toggleClass( "epkbfa-plus-square epkbfa-minus-square" );

		if( $( this ).hasClass( "eckb-Q__list__item--active" ) ) {

			$( this ).removeClass( "eckb-Q__list__item--active" );

		} else {

			$( this ).addClass( "eckb-Q__list__item--active" );

		}

	});

	// Confirm button for popup notification
	$( '.epkb-notification-box-popup__button-confirm' ).on( 'click', function () {
		if ( $( this ).attr( 'data-target' ).length > 0 ) {
			$( this ).closest( $( this ).attr( 'data-target' ) ).remove();
		}
	});

	// 'Explore Features' button on 'Need Help?' => 'Get Started' page (possibly other similar links)
	$( '.epkb-admin__step-cta-box__link[data-target]' ).on( 'click', function () {

		// Get target keys
		let target_keys = $( this ).attr( 'data-target' );
		if ( typeof target_keys === 'undefined' || target_keys.length === 0 ) {
			return;
		}
		target_keys = target_keys.split( '__' );

		// Top panel item
		$( '.epkb-admin__top-panel__item[data-target="' + target_keys[0] + '"]' ).trigger( 'click' );

		// Secondary panel item
		if ( target_keys.length > 1 ) {
			setTimeout( function () {
				$( '.epkb-admin__secondary-panel__item[data-target="' + target_keys[1] + '"]' ).trigger( 'click' );
			}, 100 );
		}
	});

	function clear_bottom_notifications() {
		var bottom_message = $('body').find('.eckb-bottom-notice-message');
		if ( bottom_message.length ) {
			bottom_message.addClass( 'fadeOutDown' );
			setTimeout( function() {
				bottom_message.html( '' );
			}, 1000);
		}
	}

	function clear_message_after_set_time(){

		if( $('.eckb-bottom-notice-message' ).length > 0 ) {
			clearTimeout( remove_message_timeout );

			//Add fadeout class to notice after set amount of time has passed.
			remove_message_timeout = setTimeout(function () {
				clear_bottom_notifications();
			} , 10000);
		}
	}
	clear_message_after_set_time();

	$( document ).on( 'click', '#eckb-kb-create-demo-data', function( e ) {
		e.preventDefault();

		let postData = {
			action: 'epkb_create_kb_demo_data',
			epkb_kb_id: $( this ).data( 'id' ),
			_wpnonce_epkb_ajax_action: epkb_vars.nonce
		};

		epkb_send_ajax( postData, function( response ){
			if ( typeof response.message != 'undefined' ) {
				$('.eckb-bottom-notice-message').remove();
				$('body').append(response.message).removeClass('fadeOutDown');
				// reload order view to show articles
				$( '#eckb-wizard-ordering__page input' ).first().trigger('change');
				return;
			}
		} );

	});

	// Switch tabs inside Admin form
	$( document ).on( 'click', '.epkb-admin__form .epkb-admin__form-tab', function() {
		switch_admin_form_tab( $( this ) );
	} );
	function switch_admin_form_tab( current_tab ) {
		let target_tab_key = $( current_tab ).data( 'target' ),
			admin_form = $( current_tab ).closest( '.epkb-admin__form' ),
			first_sub_tab = admin_form.find( '.epkb-admin__form-sub-tabs--' + target_tab_key + ' .epkb-admin__form-sub-tab' ).first();

		// Show all tabs as not active and hide their content
		admin_form.find( '.epkb-admin__form-tab' ).removeClass( 'epkb-admin__form-tab--active' );
		admin_form.find( '.epkb-admin__form-tab-wrap' ).removeClass( 'epkb-admin__form-tab-wrap--active' );

		// Show current tab as active and show its content
		$( current_tab ).addClass( 'epkb-admin__form-tab--active' );
		admin_form.find( '.epkb-admin__form-tab-wrap--' + target_tab_key ).addClass( 'epkb-admin__form-tab-wrap--active' );

		// Open first sub-tab when clicked the parent tab
		if ( first_sub_tab.length ) {
			switch_admin_form_sub_tab( first_sub_tab );

		// Show all sub-tabs as not active and hide their content
		} else {
			admin_form.find( '.epkb-admin__form-sub-tab' ).removeClass( 'epkb-admin__form-sub-tab--active' );
			admin_form.find( '.epkb-admin__form-sub-tab-wrap' ).removeClass( 'epkb-admin__form-sub-tab-wrap--active' );
		}

		// Update anchor
		window.location.hash = '#settings__' + target_tab_key;
	}

	// Handle click event on sub-tabs inside Admin form
	$( document ).on( 'click', '.epkb-admin__form .epkb-admin__form-sub-tab', function() {
		switch_admin_form_sub_tab( $( this ) );
	} );

	// Switch sub-tab inside Admin form
	function switch_admin_form_sub_tab( current_sub_tab ) {

		let admin_form = current_sub_tab.closest( '.epkb-admin__form' ),
			current_tab_key = $( current_sub_tab ).closest( '.epkb-admin__form-sub-tabs' ).data( 'tab-key' ),
			current_sub_tab_key = current_sub_tab.length ? current_sub_tab.data( 'target' ) : '';

		// Ensure only parent tab is shown as active and only its content is opened when clicked on sub-tab
		admin_form.find( '.epkb-admin__form-tab' ).removeClass( 'epkb-admin__form-tab--active' );
		admin_form.find( '.epkb-admin__form-tab-wrap' ).removeClass( 'epkb-admin__form-tab-wrap--active' );
		$( '.epkb-admin__form-tab[data-target="' + current_tab_key + '"]' ).addClass( 'epkb-admin__form-tab--active' );
		admin_form.find( '.epkb-admin__form-tab-wrap--' + current_tab_key ).addClass( 'epkb-admin__form-tab-wrap--active' );

		let	target_sub_tab_key = current_sub_tab.data( 'target' ),
			target_sub_tab_content = admin_form.find( '.epkb-admin__form-sub-tab-wrap--' + target_sub_tab_key );

		// Show all sub-tabs as not active and hide their content
		admin_form.find( '.epkb-admin__form-sub-tab' ).removeClass( 'epkb-admin__form-sub-tab--active' );
		admin_form.find( '.epkb-admin__form-sub-tab-wrap' ).removeClass( 'epkb-admin__form-sub-tab-wrap--active' );

		// Show current sub-tab as active and show its content
		current_sub_tab.addClass( 'epkb-admin__form-sub-tab--active' );
		target_sub_tab_content.addClass( 'epkb-admin__form-sub-tab-wrap--active' );

		// Update anchor
		window.location.hash = '#settings__' + current_tab_key + ( current_sub_tab_key.length ? '__' + current_sub_tab_key : '' );
	}

	// Link to open Full Editor Tab
	$( document ).on( 'click', '.epkb-admin__form .epkb-admin__form-tab-content--about-kb .epkb-admin__form-tab-content-desc__link' +
		', .epkb-admin__form .epkb-admin__form-tab-content--main-page-about-kb .epkb-admin__form-tab-content-desc__link', function( e ) {
		e.preventDefault();
		$( '.epkb-admin__form .epkb-admin__form-tab[data-target="editor"]' ).click();
		return false;
	});

	// Link to open Categories & Articles Tab
	$( document ).on( 'click', '.epkb-admin__form .epkb-admin__form-tab-content--manage-theme-compat .epkb-admin__form-tab-content-desc__link' +
		', .epkb-admin__form .epkb-admin__form-tab-content--layout .epkb-admin__form-tab-content__to-settings-link', function( e ) {
		e.preventDefault();
		$( '.epkb-admin__form .epkb-admin__form-sub-tab[data-selected-module="categories_articles"]' ).click();
		$( [document.documentElement, document.body] ).animate( {
			scrollTop: $( '[data-target="theme-compatibility-mode"]' ).offset().top - 50
		}, 300 );
		return false;
	});

	// Link to open Labels -> Sidebar Intro Text
	$( document ).on( 'click', '.epkb-admin__form .epkb-admin__form-tab-content--sidebar_main_page_intro_text .epkb-admin__form-tab-content-desc__link', function( e ) {
		e.preventDefault();
		$( '.epkb-admin__form .epkb-admin__form-tab[data-target="labels"]' ).click();
		$( [document.documentElement, document.body] ).animate( {
			scrollTop: $( '[data-target="sidebar_main_page_intro_text"]' ).offset().top
		}, 300 );
		return false;
	});

	// Link to open KB Main Page -> Search Box
	$( document ).on( 'click', '.epkb-admin__form .epkb-admin__form-sub-tab-wrap--article-page-search-box .epkb-admin__form-tab-content-desc__link', function( e ) {
		e.preventDefault();
		let target_sub_tab = $( '.epkb-admin__form .epkb-admin__form-sub-tab[data-selected-module="search"]' );
		if ( target_sub_tab.length ) {
			target_sub_tab.click();
		} else {
			$( '.epkb-admin__form .epkb-admin__form-tab[data-target="main-page"]' ).click();
		}
		$( [document.documentElement, document.body] ).animate( {
			scrollTop: 0
		}, 300 );
		return false;
	});

	// Link to open Labels -> FAQs Feature -> Title
	$( document ).on( 'click', '#ml_faqs_title_location_group .epkb-admin__form-tab-content-desc__link', function( e ) {
		e.preventDefault();
		$( '.epkb-admin__form .epkb-admin__form-tab[data-target="labels"]' ).click();
		$( [document.documentElement, document.body] ).animate( {
			scrollTop: $( '[data-target="labels_faqs_feature"]' ).offset().top
		}, 300 );
		return false;
	});

	// Link to open Labels -> Articles List Feature -> Title
	$( document ).on( 'click', '#ml_articles_list_title_location_group .epkb-admin__form-tab-content-desc__link', function( e ) {
		e.preventDefault();
		$( '.epkb-admin__form .epkb-admin__form-tab[data-target="labels"]' ).click();
		$( [document.documentElement, document.body] ).animate( {
			scrollTop: $( '[data-target="labels_articles_list_feature"]' ).offset().top
		}, 300 );
		return false;
	});

	// Link to open Labels
	$( document ).on( 'click', '.epkb-admin__form-tab-content--bottom-labels-link .epkb-admin__form-tab-content-desc__link', function( e ) {
		e.preventDefault();
		$( '.epkb-admin__form .epkb-admin__form-tab[data-target="labels"]' ).click();
		$( [document.documentElement, document.body] ).animate( {
			scrollTop: 0
		}, 300 );
		return false;
	});

	// Link to Settings tab inside admin notices when the same page is currently open
	$( document ).on( 'click', '.epkb-notification-box-top__body__desc a', function( e ) {
		let location_parts = $( this ).attr( 'href' ).split( '#' );
		if ( location_parts.length > 1 ) {
			let target_top_tab = $( '.epkb-admin__top-panel__item--' + location_parts[1] );
			if ( target_top_tab.length ) {
				e.preventDefault();
				target_top_tab.trigger( 'click' );
				return false;
			}
		}
	});

	let isColorInputSync = false;
	$('.epkb-admin__color-field input').wpColorPicker({
		change: function( colorEvent, ui) {

			// Do nothing for programmatically changed value (for sync purpose)
			if ( isColorInputSync ) {
				return;
			}

			isColorInputSync = true;

			// Get current color value
			let color_value = $( colorEvent.target ).wpColorPicker( 'color' );
			let setting_name = $( colorEvent.target ).attr( 'name' );

			// Sync other color pickers that have the same name
			$( '.epkb-admin__color-field input[name="' + setting_name + '"]' ).not( colorEvent.target ).each( function () {
				$( this ).wpColorPicker( 'color', color_value );
			} );

			isColorInputSync = false;
		},
	});

	/**
	 * Save button for config tabs
	 */
	function save_config_tab_settings( event, reload_page ) {

		let $wrap = $( '.epkb-admin__kb__form-save__button' ).closest( '.epkb-admin__form' );

		if ( ! $wrap.length ) {
			return;
		}

		// collect settings
		let kb_config = {};

		// apply tinymce changes to textareas if need
		if ( typeof tinyMCE != 'undefined' ) {
			tinyMCE.triggerSave()
		}

		$wrap.find('input, select, textarea').each(function(){

			// ignore inputs with empty name and pro feature fields (an ad field)
			if ( ! $( this ).attr( 'name' ) || ! $( this ).attr( 'name' ).length
				|| $( this ).closest( '.epkb-input-group' ).find( '.epkb__option-pro-tag' ).length
				|| $( this ).closest( '.epkb-input-group' ).find( '.epkb__option-pro-tag-container' ).length ) {
				return true;
			}

			if ( $(this).attr('type') === 'checkbox' ) {

				// checkboxes multiselect
				if ( $( this ).closest( '.epkb-admin__checkboxes-multiselect' ).length ) {
					if ( $( this ).prop( 'checked' ) ) {
						if ( ! kb_config[$(this).attr('name')] ) {
							kb_config[$(this).attr('name')] = [];
						}
						kb_config[$(this).attr('name')].push( $(this).val() );
					}

				// single checkbox
				} else {
					kb_config[ $(this).attr('name') ] = $(this).prop('checked') ? 'on' : 'off';
				}
				return true;
			}

			if ( $(this).attr('type') === 'radio' ) {
				if ( $(this).prop('checked') ) {
					kb_config[ $(this).attr('name') ] = $(this).val();
				}
				return true;
			}

			if ( typeof $(this).attr('name') == 'undefined' ) {
				return true;
			}
			kb_config[ $(this).attr('name') ] = $(this).val();
		});

		// Ensure 'faq_group_ids' is set even if no FAQ Groups are selected
		if ( $( '[name="faq_group_ids"]' ).length && typeof kb_config.faq_group_ids == 'undefined' ) {
			kb_config.faq_group_ids = 0;
		}

		kb_config.epkb_kb_id = $( '#epkb-list-of-kbs' ).val();

		// Force reload page if:
		// - is modular Main Page
		// - AND Main Page search module is not present
		// - AND Article Page search in sync with Main Page search
		if ( ! $( '#modular_main_page_toggle' ).length && ! $( '.epkb-admin__form-tab-content--module-selection [data-value="search"].epkb-input-custom-dropdown__option--selected' ).length && $( '[name="article_search_sync_toggle"]:checked' ).length ) {
			reload_page = true;
		}

		epkb_send_ajax(
			{
				action: 'epkb_apply_settings_changes',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				epkb_kb_id: kb_config.epkb_kb_id,
				kb_config: JSON.stringify( kb_config )
			},
			function( response ) {
				$( '.eckb-top-notice-message' ).remove();

				if ( typeof kb_config.kb_name != 'undefined' ) {
					$( '#epkb-list-of-kbs option[value="' + kb_config.epkb_kb_id + '"]' ).html( kb_config.kb_name );
				}

				if ( $("#editor_backend_mode1").length && $("#editor_backend_mode1").prop('checked') ) {
					$('[data-open-editor-link]').data('open-editor-link', 'back');
				} else {
					$('[data-open-editor-link]').data('open-editor-link', 'front');
				}

				if ( reload_page ) {
					location.reload();
				} else {
					if ( typeof response.message !== 'undefined' ) {
						clear_bottom_notifications();
						$( 'body' ).append( response.message );
					}
					clear_message_after_set_time();
				}
			},
			undefined,
			reload_page
		);

		return false;
	}
	$( document ).on( 'click', '.epkb-admin__kb__form-save__button', save_config_tab_settings );

	// Conditional setting input
	$( document ).on( 'click', '.eckb-conditional-setting-input', function() {

		// Find current input
		let current_input = $( this ).find( 'input' );
		if ( $( current_input[0] ).attr( 'type' ) === 'radio' ) {
			current_input = $( this ).find( 'input:checked' );
		}
		if ( ! current_input.length ) {
			current_input = $( this ).find( 'select' );
		}

		// OR LOGIC: Find content that is dependent to the current input
		let or_dependent_targets = $( '.eckb-condition-depend__' + current_input.attr( 'name' ) );

		// AND LOGIC: Find content that is dependent to the current input
		let and_dependent_targets = $( '.eckb-condition-depend-and__' + current_input.attr( 'name' ) );

		// Hide all dependent fields if the current input is not visible - only for AND logic, because OR logic can be satisfied with any of dependency
		if ( $( this ).css( 'display' ) === 'none' ) {
			$( and_dependent_targets ).hide();
			return;
		}

		// OR LOGIC: Show fields if condition matched
		or_dependent_targets.each( function() {

			// Find all dependencies
			let all_dependency_fields = $( this ).data( 'dependency-ids' );
			if ( typeof all_dependency_fields === 'undefined' ) {
				return;
			}
			all_dependency_fields = all_dependency_fields.split( ' ' );

			// Find all values for which show the dependent content
			let all_dependency_values = $( this ).data( 'enable-on-values' );
			if ( typeof all_dependency_values === 'undefined' ) {
				return;
			}
			all_dependency_values = all_dependency_values.split( ' ' );

			// First hide the dependent content, and then show it if any of its currently visible dependencies has corresponding value
			$( this ).hide();
			$( this ).closest( '.epkb-input-group-combined-units').find( '.epkb-input-desc' ).hide();

			for ( let i = 0; i < all_dependency_fields.length; i++ ) {

				// Find dependency field
				let dependency_field = $( '#' + all_dependency_fields[i] );
				if ( typeof dependency_field === 'undefined' || ! dependency_field.length ) {
					continue;
				}

				// Ignore currently hidden fields
				if ( $( dependency_field ).closest( '.epkb-admin__input-field' ).css( 'display' ) === 'none' ) {
					continue;
				}

				// Find dependency input
				let dependency_input = $( dependency_field ).is( 'select' ) ? ( dependency_field ) : dependency_field.find( 'input' );
				if ( dependency_input.attr( 'type' ) === 'radio' || dependency_input.attr( 'type' ) === 'checkbox' ) {
					dependency_input = $( dependency_field ).find( 'input:checked' );
				}
				if ( typeof dependency_input === 'undefined' || ! dependency_input.length ) {
					continue;
				}

				let current_field_id = $( this ).attr( 'id' );

				// Show dependent content if value of the dependency input in dependency values list
				if ( all_dependency_values.indexOf( dependency_input.val() ) >= 0 ) {
					$( this ).show();
					$( this ).closest( '.epkb-input-group-combined-units').find( '.epkb-input-desc' ).show();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				trigger_conditional_field_click( current_field_id );
			}
		} );

		// AND LOGIC: Show fields if condition matched
		and_dependent_targets.each( function() {

			let current_dependent_target = this;

			// Find all dependencies
			let all_dependency_fields = $( current_dependent_target ).data( 'dependency-and' );
			if ( typeof all_dependency_fields === 'undefined' ) {
				return;
			}
			all_dependency_fields = all_dependency_fields.trim().split( ' ' );

			// First show the dependent content, and then hide it if any of its dependencies does not have corresponding value or is currently hidden
			$( current_dependent_target ).show();
			for ( let i = 0; i < all_dependency_fields.length; i++ ) {

				let current_field_id = $( this ).attr( 'id' );
				let dependency_id = all_dependency_fields[i].split( '--' )[0];
				let dependency_value = all_dependency_fields[i].split( '--' )[1];

				// Find dependency field - hide if is not found
				let dependency_field = $( '#' + dependency_id );
				if ( typeof dependency_field === 'undefined' || ! dependency_field.length ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				// Hide for currently hidden fields
				if ( $( dependency_field ).closest( '.epkb-admin__input-field' ).css( 'display' ) === 'none' ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				// Find dependency input
				let dependency_input = $( dependency_field ).is( 'select' ) ? ( dependency_field ) : dependency_field.find( 'input' );
				if ( dependency_input.attr( 'type' ) === 'radio' || dependency_input.attr( 'type' ) === 'checkbox' ) {
					dependency_input = $( dependency_field ).find( 'input:checked' );
				}
				if ( typeof dependency_input === 'undefined' || ! dependency_input.length ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				// Hide dependent content if value of the dependency input does not match dependency value
				if ( dependency_input.val() !== dependency_value ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				trigger_conditional_field_click( current_field_id );
			}
		} );
	} );

	// Trigger click event of the current dependent field to check its own dependent fields
	function trigger_conditional_field_click( field_id ) {

		let current_field = $( '#' + field_id );
		if ( ! current_field.length ) {
			return;
		}

		if ( current_field.hasClass( 'eckb-conditional-setting-input' ) ) {
			setTimeout( function() {
				current_field.trigger( 'click' );
			}, 1 );
		}
	}

	// Initialize conditional fields
	$( '.eckb-conditional-setting-input' ).trigger( 'click' );

	// Allow only one active sidebar
	$( '.epkb-input[name="article_nav_sidebar_type_left"]' ).change( function() {
		if ( $( this ).closest( '.epkb-admin__select-field' ).css( 'display' ) === 'none' ) {
			return;
		}
		if ( $( this ).val() !== 'eckb-nav-sidebar-none' ) {
			$( '[name="article_nav_sidebar_type_right"][value="eckb-nav-sidebar-none"]' ).parent().find( '.epkb-label' ).click();
			$( this ).click();
		}
	});
	$( '.epkb-input[name="article_nav_sidebar_type_right"]' ).change( function() {
		if ( $( this ).closest( '.epkb-admin__select-field' ).css( 'display' ) === 'none' ) {
			return;
		}
		if ( $( this ).val() !== 'eckb-nav-sidebar-none' ) {
			$( '[name="article_nav_sidebar_type_left"][value="eckb-nav-sidebar-none"]' ).parent().find( '.epkb-label' ).click();
			$( this ).click();
		}
	});

	/** Save config WPML settings */
	$( 'body' ).on( 'change', '#epkb-setting-box__list-tools__other [name=wpml_is_enabled]', function(){

		// Remove old messages
		$('.eckb-top-notice-message').remove();

		let postData = {
			action: 'epkb_wpml_enable',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			wpml_enable: $(this).prop('checked') ? 'on' : 'off',
			epkb_kb_id: $( '#epkb-list-of-kbs' ).val()
		};

		epkb_send_ajax( postData, function( response ) {
			$( '.eckb-top-notice-message' ).remove();
			if ( typeof response.message !== 'undefined' ) {
				clear_bottom_notifications();
				$( 'body' ).append( response.message );
				clear_message_after_set_time();
			}

			if ( typeof response.html !== 'undefined' ) {
				$('.epkb-show-sequence-wrap').html( response.html );
			}
		} );
	});

	// Enable or Disable Preload Fonts setting
	$( document ).on( 'change', 'input[name="preload_fonts"]', function() {

		// Remove old messages
		$('.eckb-top-notice-message').remove();

		let postData = {
			action: 'epkb_preload_fonts',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			preload_fonts: $(this).prop('checked') ? 'on' : 'off'
		};

		epkb_send_ajax( postData, function( response ) {
			$( '.eckb-top-notice-message' ).remove();
			if ( typeof response.message !== 'undefined' ) {
				clear_bottom_notifications();
				$( 'body' ).append( response.message );
				clear_message_after_set_time();
			}

			if ( typeof response.html !== 'undefined' ) {
				$('.epkb-show-sequence-wrap').html( response.html );
			}
		} );
	} );

	// Enable or Disable OpenAI setting
	$( document ).on( 'change', 'input[name="disable_openai"]', function() {

		// Remove old messages
		$('.eckb-top-notice-message').remove();

		let postData = {
			action: 'epkb_disable_openai',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			disable_openai: $(this).prop('checked') ? 'on' : 'off'
		};

		epkb_send_ajax( postData, function( response ) {
			$( '.eckb-top-notice-message' ).remove();
			if ( typeof response.message !== 'undefined' ) {
				clear_bottom_notifications();
				$( 'body' ).append( response.message );
				clear_message_after_set_time();
			}

			if ( typeof response.html !== 'undefined' ) {
				$('.epkb-show-sequence-wrap').html( response.html );
			}
		} );
	} );

	// Open editor tab when user want to change theme compatibility mode
	$('[data-open-editor-link]').on('click', function(){
		if ( $(this).data('open-editor-link') == 'back' ) {
			$('.epkb-admin__form-tab[data-target="editor"]').trigger('click');
			return false;
		}
	});

	// save editor type after change option
	$('#editor_backend_mode input').on('change', function(){
		$('.epkb-admin__kb__form-save__button').trigger('click');
	});

	// Toggle to switch TOC visibility
	$( document ).on( 'change', '#toc_toggler input', function() {

		// Select default location
		if ( $( this ).prop( 'checked' ) ) {

			// Case 1: set to first available position on Left Sidebar
			if ( set_toc_to_article_sidebar_position( 'left' ) ) {
				return;
			}

			// Case 2: if all positions of Left Sidebar have components, then try set to Right Sidebar
			if ( set_toc_to_article_sidebar_position( 'right' ) ) {
				return;
			}

			// Case 3: if all positions of both Sidebars have components, then set to Content
			$( '#toc_content' ).val( '1' ).trigger( 'change' );
			$( '#toc_locations1' ).prop( 'checked', true );
			$( '#toc_locations0, #toc_locations2' ).prop( 'checked', false );
			return;
		}

		// Unselect all locations
		$( '#toc_content' ).val( '0' ).trigger( 'change' );
		$( '#toc_locations input' ).prop( 'checked', false );

		// Unselect from Sidebar positions
		unselect_toc_in_article_sidebar_positions();

		$( this ).trigger( 'check_toggler' );
	} );
	$( document ).on( 'check_toggler', '#toc_toggler input', function() {
		let state = false;
		$( '#toc_locations input' ).each( function() {
			if ( $( this ).prop('checked' ) ) {
				state = true;
			}
		} );
		$( this ).prop( 'checked', state );
	} );

	// TOC Location - update icons and set corresponding Position
	$( document ).on( 'click', '#toc_locations input', function() {
		let current_location = $( this ).prop( 'value' );
		let input_checked = $( this ).prop( 'checked' );
		let is_toc_set = false;

		switch ( current_location ) {

			case 'toc_left':
				unselect_toc_in_article_sidebar_positions();
				$( '#toc_content' ).val( '0' ).trigger( 'change' );
				if ( input_checked ) {
					is_toc_set = set_toc_to_article_sidebar_position( 'left' );
				}
				break;

			case 'toc_content':
				unselect_toc_in_article_sidebar_positions();
				$( '#toc_content' ).val( '0' ).trigger( 'change' );
				if ( input_checked ) {
					$( '#toc_locations1' ).prop( 'checked', true );
					$( '#toc_locations0, #toc_locations2' ).prop( 'checked', false );
					$( '#toc_content' ).val( '1' ).trigger( 'change' );
					is_toc_set = true;
				}
				break;

			case 'toc_right':
				unselect_toc_in_article_sidebar_positions();
				$( '#toc_content' ).val( '0' ).trigger( 'change' );
				if ( input_checked ) {
					is_toc_set = set_toc_to_article_sidebar_position( 'right' );
				}
				break;

			default:
				break;
		}

		$( '#toc_locations input' ).each( function() {

			// Skip current Location input (unset current location if failed to set toc position to the current location)
			if ( $( this ).prop( 'value' ) === current_location && is_toc_set ) {
				return true;
			}

			// Unselect Location input
			$( this ).prop( 'checked', false );
		} );

		// Refresh toggler
		$( '#toc_toggler input' ).trigger( 'check_toggler' );
	} );
	function unselect_toc_in_article_sidebar_positions() {
		$( '#toc_left, #toc_right' ).each( function() {
			if ( parseInt( $( this ).val() ) > 0 ) {
				$( this ).val( '0' ).trigger( 'change' );
			}
		} );
	}
	function set_toc_to_article_sidebar_position( sidebar_suffix ) {
		let is_toc_set = false;
		if ( parseInt( $( '#toc_' + sidebar_suffix ).val() ) === 0 ) {
			$( '#toc_' + sidebar_suffix ).val( '3' ).trigger( 'change' );
			is_toc_set = true;
		}
		return is_toc_set;
	}

	// Article Sidebar Position - update TOC Location setting on TOC selection
	$( document ).on( 'change', '#toc_left', function( event, is_triggered_for_update ) {
		if ( parseInt( $( this ).val() ) > 0 ) {
			$( '#toc_locations0' ).prop( 'checked', true );
			$( '#toc_locations1, #toc_locations2' ).prop( 'checked', false );
			$( '#toc_content' ).val( '0' ).trigger( 'change' );
			$( '#toc_toggler input' ).trigger( 'check_toggler' );
			$( this ).data( 'prev-value', '3' );
		} else if ( parseInt( $( this ).data( 'prev-value' ) ) > 0 ) {
			$( this ).data( 'prev-value', $( this ).val() );
			if ( ! is_triggered_for_update ) {
				$( '#toc_locations0, #toc_locations1, #toc_locations2' ).prop( 'checked', false );
				$( '#toc_content' ).val( '0' ).trigger( 'change' );
				$( '#toc_toggler input' ).trigger( 'check_toggler' );
			}
		}
	} );
	$( document ).on( 'change', '#toc_right', function( event, is_triggered_for_update ) {
		if ( parseInt( $( this ).val() ) > 0 ) {
			$( '#toc_locations2' ).prop( 'checked', true );
			$( '#toc_locations0, #toc_locations1' ).prop( 'checked', false );
			$( '#toc_content' ).val( '0' ).trigger( 'change' );
			$( '#toc_toggler input' ).trigger( 'check_toggler' );
			$( this ).data( 'prev-value', '3' );
		} else if ( parseInt( $( this ).data( 'prev-value' ) ) > 0 ) {
			$( this ).data( 'prev-value', $( this ).val() );
			if ( ! is_triggered_for_update ) {
				$( '#toc_locations0, #toc_locations1, #toc_locations2' ).prop( 'checked', false );
				$( '#toc_content' ).val( '0' ).trigger( 'change' );
				$( '#toc_toggler input' ).trigger( 'check_toggler' );
			}
		}
	} );

	// Toggler to disable related inputs
	$( '[data-control-toggler] input' ).on( 'change', function() {
		let toggler_input = $( this ).closest( '[data-control-toggler]' ),
			control_disabled_value = toggler_input.data( 'control-disabled-value' ),
			control_enabled_value = toggler_input.data( 'control-enabled-value' );

		// Enable inputs
		if ( $( this ).prop( 'checked' ) ) {

			// Radio buttons
			$( "[name='" + toggler_input.data( 'control-toggler' ) + "'][type='radio']" ).each( function() {
				if ( $( this ).val() == control_enabled_value ) {
					$( this ).prop( 'checked', true );
					return false;
				}
			} );

			// Select
			$( "select[name='" + toggler_input.data( 'control-toggler' ) + "']" ).val( control_enabled_value ).trigger( 'change' );

		// Disable inputs
		} else {

			// Radio buttons
			$( "[name='" + toggler_input.data( 'control-toggler' ) + "'][type='radio']" ).each( function() {

				// Radio buttons
				if ( $( this ).val() == control_disabled_value ) {
					$( this ).prop( 'checked', true );
					return false;
				}
			} );

			// Select
			$( "select[name='" + toggler_input.data( 'control-toggler' ) + "']" ).val( control_disabled_value ).trigger( 'change' );
		}
	} );

	// Update toggler when related input changed
	$( '.epkb-admin__radio-icons input, select' ).on( 'change', function() {

		let toggler_input = $( "[data-control-toggler='" + $( this ).prop( 'name' ) + "'] input" );
		if ( ! toggler_input.length ) {
			return;
		}

		let control_disabled_value = toggler_input.closest( '[data-control-toggler]' ).data( 'control-disabled-value' );
		toggler_input.prop( 'checked', $( this ).val() != control_disabled_value );
	} );

	// left/right sidebar disabling
	if ( $('#article-left-sidebar-toggle').length && $('#article-left-sidebar-toggle input').prop('checked') == false ) {
		$('#article-left-sidebar-toggle').parent().addClass('epkb-sidebar-settings-disabled');
	}

	if ( $('#article-right-sidebar-toggle').length && $('#article-right-sidebar-toggle input').prop('checked') == false ) {
		$('#article-right-sidebar-toggle').parent().addClass('epkb-sidebar-settings-disabled');
	}

	$('#article-left-sidebar-toggle input').on('change', function(){
		if( $('#article-left-sidebar-toggle input').prop('checked') ) {
			$('#article-left-sidebar-toggle').parent().removeClass('epkb-sidebar-settings-disabled');
		} else {
			$('#article-left-sidebar-toggle').parent().addClass('epkb-sidebar-settings-disabled');
		}
	});

	$('#article-right-sidebar-toggle input').on('change', function(){
		if( $('#article-right-sidebar-toggle input').prop('checked') ) {
			$('#article-right-sidebar-toggle').parent().removeClass('epkb-sidebar-settings-disabled');
		} else {
			$('#article-right-sidebar-toggle').parent().addClass('epkb-sidebar-settings-disabled');
		}
	});

	$('.epkb-admin__form-tab-content-lm__toggler').on('click', function(e){

		e.stopPropagation();

		if ( $(this).closest('.epkb-admin__form-tab-content-learn-more').hasClass('epkb-admin__form-tab-content-learn-more--active') ) {
			$('.epkb-admin__form-tab-content-learn-more').removeClass('epkb-admin__form-tab-content-learn-more--active');
		} else {

			$('.epkb-admin__form-tab-content-learn-more').removeClass('epkb-admin__form-tab-content-learn-more--active');

			$(this).closest('.epkb-admin__form-tab-content-learn-more').addClass('epkb-admin__form-tab-content-learn-more--active');
		}
	});

	$('body').on('click', function(){
		$('.epkb-admin__form-tab-content-learn-more').removeClass('epkb-admin__form-tab-content-learn-more--active');
	});

	/*************************************************************************************************
	 *
	 *          Change Modular Main Page
	 *
	 ************************************************************************************************/
	$( document ).on( 'click', '#modular_main_page_toggle .epkb-settings-control-toggle', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-kb-modular-main-page--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_modular_main_page_toggle );
		return false;
	});

	// Initialize confirmation button for Modular Main Page toggle
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-modular-main-page--active .epkb-dbf__footer__accept__btn', function() {

		// Apply changes for Modular Main Page
		let modular_main_page_toggle = $( 'input[name="modular_main_page_toggle"]' );
		modular_main_page_toggle.prop( 'checked', ! modular_main_page_toggle.prop( 'checked' ) );

		// Hide confirmation dialog and save settings with page reload
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-kb-modular-main-page--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate confirmation box for Main Page layout
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-modular-main-page--active .epkb-dbf__footer__cancel__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-kb-modular-main-page--active' );
	} );

	// Change Main Page layout
	let $selected_layout;
	$( document ).on( 'click', 'input[name="kb_main_page_layout"]', function() {

		// If Elegant Layouts is disabled, then Grid and Sidebar do not apply their values - show ad box instead
		let current_input_gorup = $( this ).closest( '.epkb-input-group' );
		if ( current_input_gorup.hasClass( 'eckb-mp-layout-elay-disabled' ) ) {
			if ( $( this ).val() === 'Grid' || $( this ).val() === 'Sidebar' ) {
				$( '#epkb-dialog-pro-feature-ad-kb_main_page_layout' ).addClass( 'epkb-dialog-pro-feature-ad--active' );
				return false;
			}
		}

		// Do nothing if user clicked on currently active option
		if ( $( this ).attr( 'checked' ) ) {
			$( this ).prop( 'checked', true );
			return;
		}

		$selected_layout = $(this);
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-kb-main-page-layout--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_kb_main_page_layout );

		return false;
	});

	// Initialize confirmation button for Main Page layout
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-main-page-layout--active .epkb-dbf__footer__accept__btn', function() {

		// Apply changes for Main Page layout
		if ( $selected_layout ) {
			$selected_layout.prop( 'checked', true );
		}

		// Hide confirmation dialog and save settings with page reload
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-kb-main-page-layout--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate confirmation box for Main Page layout
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-main-page-layout--active .epkb-dbf__footer__cancel__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-kb-main-page-layout--active' );
	} );

	/*************************************************************************************************
	 *
	 *          Change Theme Compatibility Mode
	 *
	 ************************************************************************************************/
	// Activate dialog
	let $templates_for_kb;
	$( document ).on( 'click', 'input[name="templates_for_kb"]', function() {

		// Do nothing if user clicked on currently active option
		if ( $( this ).attr( 'checked' ) ) {
			return false;
		}

		$templates_for_kb = $(this);
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-template-for-kb--active' );
		if ( $( this ).val() === 'kb_templates' ){
			$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_kb_templates );
		} else {
			$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_current_theme_templates );
		}

		return false;
	});

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation .epkb-dbf__footer__accept__btn', function() {

		// Do nothing if the confirmation dialog is called not for Theme Compatibility Mode
		if ( ! $confirmation_dialog.hasClass( 'epkb-template-for-kb--active' ) ) {
			return;
		}

		// Apply changes for Theme Compatibility Mode
		if ( $templates_for_kb ) {
			$templates_for_kb.prop( 'checked', true );
		}

		// Hide confirmation dialog and save settings with page reload
		$( '#epkb-admin-page-reload-confirmation' ).removeClass( 'epkb-dialog-box-form--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation .epkb-dbf__footer__cancel__btn', function() {
		$( '#epkb-admin-page-reload-confirmation' ).removeClass( 'epkb-template-for-kb--active' );
	} );

	/*************************************************************************************************
	 *
	 *          Change Category Archive Page Theme Compatibility Mode
	 *
	 ************************************************************************************************/
	// Activate dialog
	let $template_for_archive_page;
	$( document ).on( 'click', 'input[name="template_for_archive_page"]', function() {

		// Do nothing if user clicked on currently active option
		if ( $( this ).attr( 'checked' ) ) {
			return false;
		}

		$template_for_archive_page = $(this);
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-template-for-archive-page--active' );
		if ( $( this ).val() === 'kb_templates' ){
			$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_kb_templates );
		} else {
			$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_current_theme_templates );
		}

		return false;
	});

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation .epkb-dbf__footer__accept__btn', function() {

		// Do nothing if the confirmation dialog is called not for Theme Compatibility Mode
		if ( ! $confirmation_dialog.hasClass( 'epkb-template-for-archive-page--active' ) ) {
			return;
		}

		// Apply changes for Theme Compatibility Mode
		if ( $template_for_archive_page ) {
			$template_for_archive_page.prop( 'checked', true );
		}

		// Hide confirmation dialog and save settings with page reload
		$( '#epkb-admin-page-reload-confirmation' ).removeClass( 'epkb-dialog-box-form--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation .epkb-dbf__footer__cancel__btn', function() {
		$( '#epkb-admin-page-reload-confirmation' ).removeClass( 'epkb-template-for-archive-page--active' );
	} );

	/*************************************************************************************************
	 *
	 *          Toggle to sync Article Page Search settings with Main Page Search settings
	 *
	 ************************************************************************************************/
	// Activate dialog
	$( document ).on( 'click', '#article_search_sync_toggle .epkb-settings-control-toggle', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-kb-article-search-sync--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_article_search_sync_toggle );
		return false;
	});

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-article-search-sync--active .epkb-dbf__footer__accept__btn', function() {

		// Apply changes
		let article_search_sync_toggle = $( 'input[name="article_search_sync_toggle"]' );
		article_search_sync_toggle.prop( 'checked', ! article_search_sync_toggle.prop( 'checked' ) );

		// Hide confirmation dialog and save settings with page reload
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-kb-article-search-sync--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-article-search-sync--active .epkb-dbf__footer__cancel__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-kb-article-search-sync--active' );
	} );

	/*************************************************************************************************
	 *
	 *          Toggle Article Page Search
	 *
	 ************************************************************************************************/
	// Activate dialog
	$( document ).on( 'click', '#article_search_toggle .epkb-settings-control-toggle', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-kb-article-search--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_article_search_toggle );
		return false;
	});

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-article-search--active .epkb-dbf__footer__accept__btn', function() {

		// Apply changes
		let article_search_toggle = $( 'input[name="article_search_toggle"]' );
		article_search_toggle.prop( 'checked', ! article_search_toggle.prop( 'checked' ) );

		// Hide confirmation dialog and save settings with page reload
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-kb-article-search--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-article-search--active .epkb-dbf__footer__cancel__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-kb-article-search--active' );
	} );


	/*************************************************************************************************
	 *
	 *          ADVANCED SEARCH PRESETS
	 *
	 ************************************************************************************************/
	// Activate dialog
	$( document ).on( 'change', '#advanced_search_mp_presets input, #advanced_search_ap_presets input', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-asea-presets-selection--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_asea_presets_selection );
		return false;
	} );

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-asea-presets-selection--active .epkb-dbf__footer__accept__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-asea-presets-selection--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-asea-presets-selection--active .epkb-dbf__footer__cancel__btn', function() {
		$( '#advanced_search_mp_presets input[value="current"], #advanced_search_ap_presets input[value="current"]' ).prop( 'checked', true );
		$confirmation_dialog.removeClass( 'epkb-asea-presets-selection--active' );
	} );

	/*************************************************************************************************
	 *
	 *          CATEGORY ARCHIVE PAGE PRESETS
	 *
	 ************************************************************************************************/
	// Activate dialog
	$( document ).on( 'change', '#archive_content_sub_categories_display_mode input', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-archive-presets-selection--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_archive_presets_selection );
		return false;
	} );

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-archive-presets-selection--active .epkb-dbf__footer__accept__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-archive-presets-selection--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-archive-presets-selection--active .epkb-dbf__footer__cancel__btn', function() {
		$( '#archive_content_sub_categories_display_mode input[value="current"]' ).prop( 'checked', true );
		$confirmation_dialog.removeClass( 'epkb-archive-presets-selection--active' );
	} );

	/*************************************************************************************************
	 *
	 *          FAQs MODULE PRESETS
	 *
	 ************************************************************************************************/
	// Active dialog
	$( document ).on( 'change', '#faq_preset_name input', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-faqs-module-presets-selection--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_faqs_presets_selection );
		return false;
	} );

	// Save settings
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-faqs-module-presets-selection--active .epkb-dbf__footer__accept__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-faqs-module-presets-selection--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate dialog
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-faqs-module-presets-selection--active .epkb-dbf__footer__cancel__btn', function() {
		$( '#faq_preset_name input[value="current"]' ).prop( 'checked', true );
		$confirmation_dialog.removeClass( 'epkb-faqs-module-presets-selection--active' );
	} );

	/*************************************************************************************************
	 *
	 *          Miscellaneous
	 *
	 ************************************************************************************************/
	// Allow duplicate text fields
	$('#epkb-admin__boxes-list__settings input[type=text], #epkb-admin__boxes-list__settings textarea').on('keyup', function(){
		let name = $(this).prop('name');
		let val = $(this).val();

		if ( $('#epkb-admin__boxes-list__settings').find('[name="' + name + '"]').length == 1 ) {
			return;
		}

		$('#epkb-admin__boxes-list__settings').find('[name="' + name + '"]').each(function(){
			$(this).val(val);
		});
	});

	function update_custom_selection_green_mark() {

		// Through each custom selection group
		let custom_selection_groups = [];
		$( '[data-custom-selection-group]' ).each( function() {

			// Execute once for each unique group
			let current_selection_group = $( this ).data( 'custom-selection-group' );
			if ( custom_selection_groups.includes( current_selection_group ) ) {
				return;
			}
			custom_selection_groups.push( current_selection_group );

			// Reset all green marks in the current selection group
			$( '[data-custom-selection-group="' + current_selection_group + '"] .epkb-input-custom-dropdown__option-mark' ).html( '' );

			// Through each element of the current selection group
			$( '[data-custom-selection-group="' + current_selection_group + '"]' ).each( function() {

				// The 'none' value does not need green mark
				let selected_value = $( this ).find( 'select' ).val();
				if ( selected_value === 'none' ) {
					return;
				}

				// Set green mark for the current selected value inside other dropdowns of the current selection group
				let selection_label = $( this ).data( 'custom-selection-label' );
				if ( selection_label ) {
					$( '[data-custom-selection-group="' + current_selection_group + '"] [data-value="' + selected_value + '"]:not(.epkb-input-custom-dropdown__option--selected)' ).find( '.epkb-input-custom-dropdown__option-mark' ).html( '(' + selection_label + ')' );
				}
			} );
		} );
	}

	// Custom Dropdown
	$( document ).on( 'click', '.epkb-input-custom-dropdown__input', function( e ) {

		// Avoid trigger of 'click' event for any parent element when clicked on the input
		e.stopPropagation();

		let current_list = $( this ).closest( '.input_container' ).find( '.epkb-input-custom-dropdown__options-list' );

		// Close all option lists
		$( '.epkb-input-custom-dropdown__options-list' ).not( current_list ).hide();

		// Show current option list
		$( current_list ).toggle();
	} );

	// Handle selection for Custom Dropdown
	$( document ).on( 'click', '.epkb-input-custom-dropdown__option', function() {

		// Handle change of value
		let new_value = $( this ).data( 'value' );
		let input_container = $( this ).closest( '.input_container' );
		input_container.find( '.epkb-input-custom-dropdown__option' ).removeClass( 'epkb-input-custom-dropdown__option--selected' );
		$( this ).addClass( 'epkb-input-custom-dropdown__option--selected' );
		let prev_value = input_container.find( 'select' ).val();

		// Hide list of options
		input_container.find( '.epkb-input-custom-dropdown__options-list' ).hide();

		// Change value for the hidden select (to have it filled on form submission)
		input_container.find( 'select' ).val( new_value ).trigger( 'change' );

		// Update label text of the custom dropdown
		let value_label = input_container.find( 'select option[value="' + new_value + '"]' ).html();
		input_container.find( '.epkb-input-custom-dropdown__input span' ).html( value_label );

		let current_input_name = input_container.find( 'select' ).attr( 'name' );

		// Unset current value in other dropdowns of the current unselection group
		let current_unselection_group = $( this ).closest( '[data-custom-unselection-group]' ).data( 'custom-unselection-group' );
		$( '[data-custom-unselection-group="' + current_unselection_group + '"] select' ).each( function() {
			if ( current_input_name !== $( this ).attr( 'name' ) && $( this ).val() === new_value ) {
				$( this ).val( 'none' ).trigger( 'change', true ); // trigger 'change' to have the updated appearance of select element in browser
				$( this ).closest( '.eckb-conditional-setting-input' ).trigger( 'click' ); // trigger dependent fields
			}
		} );

		// Unset other dropdowns of the current unselection group if any of them has non-zero value
		let current_nonzero_unselection_group = $( this ).closest( '[data-custom-nonzero-unselection-group]' ).data( 'custom-nonzero-unselection-group' );
		$( '[data-custom-nonzero-unselection-group="' + current_nonzero_unselection_group + '"] select' ).each( function() {
			if ( parseInt( new_value ) > 0 && current_input_name !== $( this ).attr( 'name' ) && parseInt( $( this ).val() ) > 0 ) {
				$( this ).val( '0' ).trigger( 'change', true ); // trigger 'change' to have the updated appearance of select element in browser
				$( this ).closest( '.eckb-conditional-setting-input' ).trigger( 'click' ); // trigger dependent fields
			}
		} );

		// When user adds a new row with a Module, use the width from the row above if any
		if ( current_unselection_group === 'ml-row' && prev_value === 'none' && new_value !== 'none' ) {

			// If row above has no module, then use prev ++, until either row above with module found or all rows above checked
			let current_row = $( this ).closest( '.epkb-admin__form-sub-tab-wrap' );
			let rows_above = current_row.prevAll( '.epkb-admin__form-sub-tab-wrap' );
			rows_above.each( function() {

				// Continue only if row above has module
				let source_row_module_selector = $( this ).find( '[data-custom-selection-group="ml-row"] select' );
				if ( source_row_module_selector.length && source_row_module_selector.val() !== 'none' ) {

					let source_row_module_selector_name = source_row_module_selector.attr( 'name' );
					let source_row_width_name = source_row_module_selector_name.replace( '_module', '_desktop_width' );
					let source_row_width_units_name = source_row_module_selector_name.replace( '_module', '_desktop_width_units' );

					let current_row_width_name = current_input_name.replace( '_module', '_desktop_width' );
					let current_row_width_units_name = current_input_name.replace( '_module', '_desktop_width_units' );

					// Set width value from row above
					let row_width_value = $( '[name="' + source_row_width_name + '"]' ).val();
					let row_width_units = $( '[name="' + source_row_width_units_name + '"]:checked' ).val();
					$( '[name="' + current_row_width_name + '"]' ).val( row_width_value ).trigger( 'change' );
					$( '[name="' + current_row_width_units_name + '"][value="' + row_width_units + '"]' ).trigger( 'click' );

					// Row above with module found, then stop loop
					return false;
				}
			} );
		}
	} );

	// Update Custom Dropdown when value of its select element was programmatically changed
	$( document ).on( 'change', '.epkb-input-custom-dropdown select', function( e ) {

		let new_value = $( this ).val();
		let input_container = $( this ).closest( '.input_container' );
		$( input_container ).find( '.epkb-input-custom-dropdown__option' ).removeClass( 'epkb-input-custom-dropdown__option--selected' );
		$( input_container ).find( '.epkb-input-custom-dropdown__option[data-value="' + new_value + '"]' ).addClass( 'epkb-input-custom-dropdown__option--selected' );

		// Update label text of the custom dropdown
		let value_label = $( input_container ).find( 'select option[value="' + new_value + '"]' ).html();
		$( input_container ).find( '.epkb-input-custom-dropdown__input span' ).html( value_label );

		// Update green marks
		update_custom_selection_green_mark();

		// Update icons
		$( this ).closest( '.epkb-input-custom-dropdown' ).find( '.epkb-input-custom-dropdown__option-icon' ).removeClass( 'epkb-input-custom-dropdown__option-icon--active' );
		$( this ).closest( '.epkb-input-custom-dropdown' ).find( '.epkb-input-custom-dropdown__option-icon[data-option-value="' + new_value + '"]' ).addClass( 'epkb-input-custom-dropdown__option-icon--active' );
	} );

	// Hide options list of the Custom Dropdown when clicked outside and the list is opened
	$( document ).on( 'click', function() {
		$( '.epkb-input-custom-dropdown__options-list' ).hide();
	} );

	// Initialize Custom Dropdowns
	$( '.epkb-input-custom-dropdown select' ).trigger( 'change' );

	// Switch categories list for selected KB
	$( document ).on( 'change', '#ml_faqs_kb_id', function() {

		// Uncheck selected categories because they are related to the previous KB
		$( '#ml_faqs_category_ids input[type="checkbox"]' ).each( function() {
			if ( $( this ).prop( 'checked' ) ) {
				$( this ).prop( 'checked', false );
			}
		} );

		// Hide all KBs categories
		$( '.epkb-ml-faqs-kb-categories' ).addClass( 'epkb-hide-elem' );

		// Show categories for selected KB
		$( '.epkb-ml-faqs-kb-categories--' + $( this ).val() ).removeClass( 'epkb-hide-elem' );
	} );

	// Image Icon selection
	$( document ).on( 'click', '.epkb-admin__icon-font-selection .epkb-icon-pack__icon', function( e ) {
		e.stopPropagation();
		let button = $( this ),
			container = button.closest( '.epkb-admin__icon-font-selection' );

		// Unselect all icons
		container.find( '.epkb-icon-pack__icon' ).removeClass( 'epkb-icon-pack__icon--checked' );

		// Select current icon
		button.addClass( 'epkb-icon-pack__icon--checked' );

		// Set current icon value to hidden input
		$( '[name="' + container.data( 'setting-name' ) + '"]' ).val( button.data( 'key' ) );
	} );

	// Image save
	$( document ).on( 'click', '.epkb-admin__icon-image-selection .epkb-input-icon-image__button',function( e ) {
		e.stopPropagation();
		let button = $( this ),
			container = button.closest( '.epkb-admin__icon-image-selection' ),
			custom_uploader = wp.media( {
				title: button.data( 'title' ),
				library : {
					type : 'image'
				},
				multiple: false
			} ).on( 'select', function() {
				let attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
				$( '[name="' + container.data( 'setting-name' ) + '"]' ).val( attachment.id );
				button.removeClass( 'epkb-input-icon-image__button--no-image' ).addClass( 'epkb-input-icon-image__button--have-image' ).css( { 'background-image' : 'url('+attachment.url+')' } );
			} ).open();
	} );

	// Switch Modules inside Rows settings
	$( document ).on( 'change', '[data-settings-group="ml-row"].epkb-row-module-setting select', function() {

		let current_module_name = $( this ).val();

		// Add module to current row
		if ( current_module_name !== 'none' ) {
			let target_fields = $( '.epkb-admin__form-tab-content--module-settings .eckb-ml-module__' + current_module_name );
			$( this ).closest( '.epkb-admin__form-sub-tab-wrap' ).find( '.epkb-admin__form-tab-content--module-settings .epkb-admin__kb__form' ).append( target_fields );
		}

		// Update visibility of module settings for used and unused rows
		$( '[data-settings-group="ml-row"].epkb-row-module-setting select' ).each( function() {
			let module_settings_box = $( this ).closest( '.epkb-admin__form-sub-tab-wrap' ).find( '.epkb-admin__form-tab-content--module-settings' );
			if ( $( this ).val() === 'none' ) {
				module_settings_box.addClass( 'epkb-admin__form-tab-content--hide' );
			} else {
				module_settings_box.removeClass( 'epkb-admin__form-tab-content--hide' );
			}
		} );

		// Switch Settings boxes which belong to currently selected module
		switch_module_boxes( this );

		// Update labels for sub-tabs
		$( '.epkb-admin__form-sub-tabs--main-page .epkb-admin__form-sub-tab' ).each( function() {

			let target_select_id = $( this ).data( 'module-selector' );
			if ( ! target_select_id ) {
				return;
			}

			let target_select = $( '#' + target_select_id );
			let sub_tab = $( this );
			let sub_tab_label = sub_tab.find( '.epkb-admin__form-sub-tab-title' )
			if ( target_select.val() === 'none' ) {
				let no_module_label = $( this ).data( 'no-module-label' );
				sub_tab_label.html( no_module_label ? no_module_label : '' );
				sub_tab.addClass( 'epkb-admin__form-sub-tab--unused' );
			} else {
				let module_label = target_select.find( 'option[value="' + target_select.val() + '"]' ).html();
				sub_tab_label.html( module_label );
				sub_tab.removeClass( 'epkb-admin__form-sub-tab--unused' );
			}

			// Update selected module key for the sub-tab
			$( this ).attr( 'data-selected-module', target_select.val() );
		} );
	} );

	// Switch Settings boxes which belong to certain module
	// Add the following CSS classes in PHP config to necessary Settings boxes:
	// - epkb-admin__form-tab-content--module-box
	// - epkb-admin__form-tab-content--{module name}-box
	// - epkb-admin__form-tab-content--hide
	// Add 'data' => [ 'insert-box-after' => {selector} ] in PHP config to insert the box after certain Settings box
	function switch_module_boxes( module_selector ) {
		let current_module_name = $( module_selector ).val();

		// Hide other modules Settings boxes in the current sub-tab
		let other_modules_boxes = $( module_selector ).closest( '.epkb-admin__form-sub-tab-wrap' ).find( '.epkb-admin__form-tab-content--module-box:not(.epkb-admin__form-tab-content--' + current_module_name + '-box)' );
		other_modules_boxes.addClass( 'epkb-admin__form-tab-content--hide' );

		// Find all Settings boxes which belong to the currently selected module
		let module_boxes = $( module_selector ).closest( '.epkb-admin__form-tab-wrap' ).find( '.epkb-admin__form-tab-content--' + current_module_name + '-box' );
		if ( ! module_boxes.length ) {
			return;
		}

		$( module_boxes.get().reverse() ).each( function () {
			$( this ).removeClass( 'epkb-admin__form-tab-content--hide' );

			// Show Settings boxes which belong to the currently selected module
			let insert_box_after = $( this ).data( 'insert-box-after' );
			$( module_selector ).closest( '.epkb-admin__form-sub-tab-wrap' ).find( insert_box_after ).after( this );
		} );

		// Insure the selected Layout is shown as active - fix for Grid or Sidebar Layout selection with Elegant Layouts disabled
		if ( current_module_name === 'categories_articles' ) {
			$( '[name="kb_main_page_layout"]:checked' ).trigger( 'click' );
		}
	}

	// Initialize Layout box settings
	$( '[data-settings-group="ml-row"].epkb-row-module-setting select' ).each( function() {
		switch_module_boxes( this );
	} );

	// Disallow 'enter' key inside specified textareas (to disable new lines)
	$( document ).on( 'keypress', '.epkb-admin__input-field--disallow-new-lines textarea', function( event ) {
		if ( ( event.keyCode || event.which ) === 13 ) {
			return false;
		}
	} );
	
	// Disable PRO inputs for Settings Page
	$( '#epkb-admin__boxes-list__settings .epkb-admin__input-disabled' ).each( function(){
		$( this ).find( 'input, select, textarea, button' ).prop( 'disabled', true );
	});

	// Toggle the PRO Setting Tooltip
	$( document ).on( 'click', '.epkb-admin__input-disabled, .epkb__option-pro-tag', function (){
		let $tooltip = $( this ).closest( '.epkb-input-group' ).find( '.epkb__option-pro-tooltip' );
		let is_visible = $tooltip.is(':visible');

		// hide all pro tooltip
		$( '.epkb__option-pro-tooltip' ).hide();

		// toggle current pro tooltip
		if ( is_visible ) {
			$tooltip.hide();
		} else {
			$tooltip.show();
		}
	});

	// Toggle the PRO Setting Pro Feature Ad Popup

	// if user clicks on the popup itself it will close except the Learn More button
	$( document ).on( 'click', '.epkb-dialog-pro-feature-ad, .epkb-dbf__close', function (e){
		let target = $( e.target );
		if ( ! target.closest( '.epkb-dialog-pro-feature-ad__content' ).length ) {
			$( this ).removeClass( 'epkb-dialog-pro-feature-ad--active' );
		}
	});

	$( document ).on( 'click', '.epkb__option-pro-tag-pro-feature-ad', function (){
		const popup_id = $( this ).data( "target" );
		$( '#' + popup_id ).addClass( 'epkb-dialog-pro-feature-ad--active' );
	});

	$(document).on('click', function (e) {
		let target = $(e.target);
		if (!target.closest('.epkb__option-pro-tag-pro-feature-ad').length &&
			!target.closest('.epkb-dialog-pro-feature-ad').length &&
			!target.closest('.epkb-dialog-pro-feature-ad2').length) {
			$('.epkb-dialog-pro-feature-ad, .epkb-dialog-pro-feature-ad2').removeClass('epkb-dialog-pro-feature-ad--active');
		}
	});

	// If user clicks on the next or previous icon for php function: pro_feature_ad_box_with_images
	let featureContainers = $( '.epkb-feature-container' );
	let currentIndex = 0;

	function showFeature( index ) {
		featureContainers.removeClass( 'epkb-feature--active' ).eq( index ).addClass( 'epkb-feature--active' );
	}

	$( '.epkb-feature-next' ).on('click', function() {
		currentIndex = ( currentIndex + 1 ) % featureContainers.length;
		showFeature( currentIndex );
	});

	$( '.epkb-feature-previous' ).on('click', function() {
		currentIndex = ( currentIndex - 1 + featureContainers.length ) % featureContainers.length;
		showFeature( currentIndex );
	});

	// Hide PRO Setting Tooltip if click outside the tooltip
	$( document ).on( 'click', function (e){
		let target = $( e.target );
		if ( ! target.closest( '.epkb__option-pro-tooltip' ).length && ! target.closest( '.epkb-admin__input-disabled' ).length && ! target.closest( '.epkb__option-pro-tag' ).length  ) {
			$( '.epkb__option-pro-tooltip' ).hide();
		}
	});

	// Set better default width of Modular Sidebar when user toggle it 'on'
	$( document ).on( 'change', '[name="ml_categories_articles_sidebar_toggle"]', function() {
		if ( ! $( this ).prop( 'checked' ) ) {
			return;
		}

		let sidebar_width_value = $( this ).closest( '.epkb-settings-control-container' ).data( 'default-value-pc' );
		$( '[name="ml_categories_articles_sidebar_desktop_width"]' ).val( sidebar_width_value );
	} );

	// Load Font Icons for Resource Links feature on demand
	$( document ).on( 'click', '.epkb-ml-resource-links-icons-loader', function() {

		let loader = $( this );

		let postData = {
			action: 'epkb_load_resource_links_icons',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			active_icon: loader.data( 'selected' )
		};

		epkb_send_ajax( postData, function( response ) {
			loader.closest( '.epkb-ml-resource-links-icons-loader-wrap' ).replaceWith( response.data );
		} );
	} );

	// Load General Typography on demand
	$( document ).on( 'click', '.epkb-general_typography-loader', function() {

		let loader = $( this );

		let postData = {
			action: 'epkb_load_general_typography',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			active_font_family: loader.data( 'selected' )
		};

		epkb_send_ajax( postData, function( response ) {
			loader.closest( '.epkb-general_typography-loader-wrap' ).replaceWith( response.data );
			$( '#general_typography_font_family' ).trigger( 'change' );
		} );
	} );

	// Switch Archive Page V3 toggle
	$( document ).on( 'click', '#archive_page_v3_toggle .epkb-settings-control-toggle', function() {
		$confirmation_dialog.addClass( 'epkb-dialog-box-form--active epkb-kb-archive-page--active' );
		$( '#epkb-admin-page-reload-confirmation .epkb-dbf__body' ).html( epkb_vars.on_archive_page_v3_toggle );
		return false;
	});

	// Initialize confirmation button for Archive Page V3 toggle
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-archive-page--active .epkb-dbf__footer__accept__btn', function() {

		// Apply changes for Archive Page
		let archive_page_v3_toggle = $( 'input[name="archive_page_v3_toggle"]' );
		archive_page_v3_toggle.prop( 'checked', ! archive_page_v3_toggle.prop( 'checked' ) );

		// Hide confirmation dialog and save settings with page reload
		$confirmation_dialog.removeClass( 'epkb-dialog-box-form--active epkb-kb-archive-page--active' );
		save_config_tab_settings( false, true );
	} );

	// Deactivate confirmation box for Archive Page
	$( document ).on( 'click', '#epkb-admin-page-reload-confirmation.epkb-kb-archive-page--active .epkb-dbf__footer__cancel__btn', function() {
		$confirmation_dialog.removeClass( 'epkb-kb-archive-page--active' );
	} );

	//Scroll to top
	$( '.epkb-admin__link-scroll-top' ).on( 'click',  function() {
		$( 'html, body' ).animate( { scrollTop: 0 }, 300 );
		return false;
	} );


});
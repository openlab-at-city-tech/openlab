jQuery(document).ready(function($) {

	// Add 'AI Help' button to block editor
	if ( wp && wp.data ) {
		wp.data.subscribe( function () {
			setTimeout( function () {
				if ( $( '#epkb-ai-help-sidebar-button' ).length === 0 ) {
					var toolbalEl = $( '.edit-post-header-toolbar' );
					if( typeof toolbalEl != 'undefined' ){
						$( toolbalEl ).append( '<input type="button" id="epkb-ai-help-sidebar-button" value="' + epkb_ai_vars.ai_help_button_title + '">' );
					}
				}
			}, 100 );
		} );
	}

	// Open AI Help Sidebar
	$( document ).on( 'click', '#wp-admin-bar-epkb-ai-help-sidebar-button', function( e ) {
		e.preventDefault();
		$( '.epkb-ai-help-sidebar' ).addClass( 'epkb-ai-help-sidebar--active' );
		$( '#wp-admin-bar-epkb-ai-help-sidebar-button' ).addClass( 'wp-admin-bar-epkb-ai-help-sidebar-button--active' );
		return false;
	} );
	$( document ).on( 'click', '#epkb-ai-help-sidebar-button, #epkb-ai-help-meta-box-button', function() {
		$( '.epkb-ai-help-sidebar' ).addClass( 'epkb-ai-help-sidebar--active' );
	} );

	// Close AI Help Sidebar
	$( document ).on( 'click', '.epkb-ai-help-sidebar-btn-close', function( e ) {
		e.preventDefault();
		$( '.epkb-ai-help-sidebar' ).removeClass( 'epkb-ai-help-sidebar--active' );
		$( '#wp-admin-bar-epkb-ai-help-sidebar-button' ).removeClass( 'wp-admin-bar-epkb-ai-help-sidebar-button--active' );
		return false;
	} );

	// Switch Navigation links
	$( document ).on( 'click', '.epkb-ai-help-sidebar__nav-link', function() {
		$( '.epkb-ai-help-sidebar__nav-link' ).removeClass( 'epkb-ai-help-sidebar__nav-link--active' );
		$( this ).addClass( 'epkb-ai-help-sidebar__nav-link--active' );

		let target_screen = $( this ).data( 'target' );
		$( '.epkb-ai-help-sidebar__body' ).removeClass( 'epkb-ai-help-sidebar__body--active' );
		$( '.epkb-ai-help-sidebar__body-' + target_screen ).addClass( 'epkb-ai-help-sidebar__body--active' );
		$( '.epkb-ai-help-sidebar' ).attr( 'data-active-tab', target_screen );
	} );

	// Open Settings tab
	$( document ).on( 'click', '.epkb-ai-help-sidebar__open-settings-tab-btn', function( e ) {
		e.stopPropagation();
		$( '.epkb-ai-help-sidebar__nav-link' ).trigger( 'click' );
		return false;
	} );


	/* TAB: Helper Functions--------------------------------------------------------------------*/

	// Dismiss main intro
	$( document ).on( 'click', '.epkb-ai-help-sidebar__main-intro__dismiss-btn', function() {
		$( this ).closest( '.epkb-ai-help-sidebar__main-intro' ).remove();

		let postData = {
			action: 'epkb_ai_request',
			_wpnonce_epkb_ajax_action: epkb_ai_vars.nonce,
			ai_action: 'epkb_ai_dismiss_main_intro'
		};

		epkb_send_ajax( postData, undefined, undefined, false, false, $( '.epkb-ai-help-sidebar__improve-text-selected-text-container' ) );
	} );

	// Show screen for Improve Text
	$( document ).on( 'click', '.epkb-ai__fix-improve-text-btn', function( e ) {
		e.preventDefault();
		if ( ! ai_help_check_api_key() ) {
			return false;
		}

		$( '.epkb-ai-help-sidebar__main' ).hide();
		$( '.epkb-ai-help-sidebar__screen-usage' ).hide();
		$( '.epkb-ai-help-sidebar' ).attr( 'data-back-btn', 'show' );
		$( '.epkb-ai-help-sidebar__improve-text' ).show();

		// Add support for selection mode
		$( '.epkb-ai-help-sidebar' ).addClass( 'epkb-ai-help-sidebar--select-text-mode' );

		return false;
	} );

	// Return to main AI Help Sidebar screen
	$( document ).on( 'click', '.epkb-ai-help-sidebar__nav-back-btn', function() {

		$('[data-target="helper-functions"]').trigger('click');
		$( '.epkb-ai-help-sidebar__main' ).show();
		$( '.epkb-ai-help-sidebar__screen-usage' ).hide();
		$( '.epkb-ai-help-sidebar' ).attr( 'data-back-btn', 'hide' );
		$( '.epkb-ai-help-sidebar__improve-text' ).hide();
		$( '.epkb-ai-help-sidebar__article-outline' ).hide();

		// Remove support for selection mode
		$( '.epkb-ai-help-sidebar' ).removeClass( 'epkb-ai-help-sidebar--select-text-mode' );
	} );

	// Get selected text to input field
	$(document).on('selectionchange', function(){

		// ignore wp menu, AI panel, header top bar
		if ( $(this.getSelection().anchorNode).closest('#wpbody-content').length == 0 ) {
			return;
		}

		// check panel is open and Improve Text screen is active
		if ( ! $('.epkb-ai-help-sidebar').hasClass('epkb-ai-help-sidebar--active') || $('.epkb-ai-help-sidebar__improve-text').css('display') == 'none' ) {
			return;
		}

		// when we click outside the selected text mark will disappear, ignore this case
		if ( document.getSelection().toString().length == 0 ) {
			return;
		}

		// get selection
		let range = document.getSelection().getRangeAt(0);
		let clonedSelection = range.cloneContents();
		$('.epkb-ai-help-sidebar__improve-text-input__textarea').html( clonedSelection );

		// remove Elementor UI
		$('.epkb-ai-help-sidebar__improve-text-input__textarea').find( '#elementor-editor' ).remove();

		// strip all HTML attributes and not basic tags
		let filtered_html = strip_html_tags_and_attrs( $('.epkb-ai-help-sidebar__improve-text-input__textarea').html() );
		$('.epkb-ai-help-sidebar__improve-text-input__textarea').html( filtered_html );

		// wrap <li> tags without parent to corresponding parent tag
		let parentTagName = $( range.commonAncestorContainer ).prop( 'tagName' );
		if ( parentTagName && ( parentTagName === 'UL' || parentTagName === 'OL' ) && $('.epkb-ai-help-sidebar__improve-text-input__textarea').find( ':first-child' ).prop( 'tagName' ) === 'LI' ) {
			$('.epkb-ai-help-sidebar__improve-text-input__textarea').html( '<' + parentTagName.toLowerCase() + '>' + filtered_html + '</' + parentTagName.toLowerCase() + '>' );
		}
	});

	// TinyMCE Support
	$('#wp-admin-bar-epkb-ai-help-sidebar-button, #epkb-ai-help-meta-box-button').one('click', function(){
		let iframe = $('#content_ifr').contents();
		iframe.on('selectionchange', function(){

			// check panel is open and Improve Text screen is active
			if ( ! $('.epkb-ai-help-sidebar').hasClass('epkb-ai-help-sidebar--active') || $('.epkb-ai-help-sidebar__improve-text').css('display') == 'none' ) {
				return;
			}

			// when we click outside the selected text mark will disappear, ignore this case
			if ( document.getElementById("content_ifr").contentDocument.getSelection().toString().length == 0 ) {
				return;
			}

			// get selection
			let range = document.getElementById("content_ifr").contentDocument.getSelection().getRangeAt(0);
			let clonedSelection = range.cloneContents();
			$('.epkb-ai-help-sidebar__improve-text-input__textarea').html(clonedSelection);

			// remove Elementor UI
			$('.epkb-ai-help-sidebar__improve-text-input__textarea').find( '#elementor-editor' ).remove();

			// strip all HTML attributes and not basic tags
			let filtered_html = strip_html_tags_and_attrs( $('.epkb-ai-help-sidebar__improve-text-input__textarea').html() );
			$('.epkb-ai-help-sidebar__improve-text-input__textarea').html( filtered_html );

			// wrap <li> tags without parent to corresponding parent tag
			let parentTagName = $( range.commonAncestorContainer ).prop( 'tagName' );
			if ( parentTagName && ( parentTagName === 'UL' || parentTagName === 'OL' ) && $('.epkb-ai-help-sidebar__improve-text-input__textarea').find( ':first-child' ).prop( 'tagName' ) === 'LI' ) {
				$('.epkb-ai-help-sidebar__improve-text-input__textarea').html( '<' + parentTagName.toLowerCase() + '>' + filtered_html + '</' + parentTagName.toLowerCase() + '>' );
			}
		});
	});

	// Improve Text screen actions
	let improve_text_selection_part = '';
	$( document ).on('selectionchange', function(){
		if ( $(this.getSelection().anchorNode).closest('.epkb-ai-help-sidebar__improve-text-input__textarea-wrap').length == 0 ) {
			return;
		}
		let range = document.getSelection().getRangeAt(0);
		improve_text_selection_part = range.cloneContents();
	});
	$( '.epkb-ai-help-sidebar__improve-text-toolbar input[type="submit"]' ).on( 'click', function( e ){

		let local_selection_text = $( '<div></div>' ).append( improve_text_selection_part ).html();

		// Use either selected part of text or entire text inside the textarea
		let input_text = actual_content_length( local_selection_text ) > 3
			? local_selection_text
			: $( '.epkb-ai-help-sidebar__improve-text-input__textarea' ).html();

		// Do nothing if the prompt is empty or too short
		if ( actual_content_length( input_text ) < 3 ) {
			epkb_show_error_notification( epkb_ai_vars.msg_empty_input );
			return false;
		}

		// Copy action - does not require AJAX
		if ( $( this ).hasClass( 'epkb_ai_copy' ) ) {

			let coppied_range;

			// Use content from the entire textarea if the local selection is too short
			if ( actual_content_length( local_selection_text ) > 3 ) {
				coppied_range = document.getSelection().getRangeAt(0);
			} else {
				coppied_range = document.createRange();
				coppied_range.selectNodeContents( $( '.epkb-ai-help-sidebar__improve-text-input__textarea' ).get( 0 ) );
			}

			// Get selection
			let coppied_selection = window.getSelection();
			coppied_selection.removeAllRanges();
			coppied_selection.addRange( coppied_range );

			// Copy to clipboard
			document.execCommand( 'copy' );

			// Unselect
			coppied_selection.removeAllRanges();

			epkb_show_success_notification( epkb_ai_vars.msg_ai_copied_to_clipboard );
			return;
		}

		let postData = {
			action: 'epkb_ai_request',
			_wpnonce_epkb_ajax_action: epkb_ai_vars.nonce,
			input_text: input_text,
			ai_action: $( this ).attr( 'class' ).trim()
		};

		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );

				$( '.epkb-ai-help-sidebar__screen-usage' ).show().find( '.epkb-ai-help-sidebar__screen-usage-tokens span' ).html( response.tokens_used );

				if ( typeof response.fixed_input_text != 'undefined' && response.fixed_input_text.length > 0 ) {
					if ( actual_content_length( local_selection_text ) > 3 ) {
						$( '.epkb-ai-help-sidebar__improve-text-input__textarea' ).html( $( '.epkb-ai-help-sidebar__improve-text-input__textarea' ).html().replace( input_text, response.fixed_input_text ) );
					} else {
						$( '.epkb-ai-help-sidebar__improve-text-input__textarea' ).html( response.fixed_input_text );
					}
				}
			}

		}, undefined, false, false, $( '.epkb-ai-help-sidebar__improve-text-selected-text-container' ) );

		return false;
	});

	// Generate article outline
	$( document ).on( 'click', '.epkb_ai_generate_article_outline', function( e ) {

		e.preventDefault();
		if ( ! ai_help_check_api_key() ) {
			return false;
		}

		let title = '';

		if ( $('input#title').length ) {
			// Classic editor
			title = ai_help_sidebar_sanitize_user_input( $('input#title').val() );
		} else {
			title = ai_help_sidebar_sanitize_user_input( wp.data.select("core/editor").getEditedPostAttribute('title') );
		}

		$( '.epkb-ai-help-sidebar__screen-usage' ).hide();
		$( '.epkb-ai-help-sidebar__main' ).hide();
		$( '.epkb-ai-help-sidebar__article-outline' ).show();
		$( '.epkb-ai-help-sidebar__article-outline-input-container' ).show();
		$( '.epkb-ai-help-sidebar__article-outline-results-container' ).hide();

		$( '.epkb-ai-help-sidebar' ).attr( 'data-back-btn', 'show' );
		$( '#epkb_ai_article_title' ).val( title );

		return false;
	});

	// Generate article outline
	$( document ).on( 'click', '.epkb_ai_generate_article_outline_button ', function( e ) {

		e.preventDefault();
		if ( ! ai_help_check_api_key() ) {
			return false;
		}

		let title = $( '#epkb_ai_article_title' ).val();

		if ( title.length < 3 ) {
			epkb_show_error_notification( epkb_ai_vars.msg_empty_input );
			return false;
		}

		let postData = {
			action: 'epkb_ai_request',
			_wpnonce_epkb_ajax_action: epkb_ai_vars.nonce,
			input_text: title,
			ai_action: 'epkb_ai_generate_outline'
		}

		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' && typeof response.result != 'undefined' && response.result.length > 0 ) {
				epkb_show_success_notification( response.message );
			}

			if ( typeof response.result != 'undefined' && response.result.length > 0 ) {
				$( '.epkb-ai-help-sidebar__screen-usage' ).show().find( '.epkb-ai-help-sidebar__screen-usage-tokens span' ).html( response.tokens_used );
				$( '.epkb-ai-help-sidebar__article-outline-result__textarea' ).html( response.result );

				$( '.epkb-ai-help-sidebar__article-outline-input-container' ).hide();
				$( '.epkb-ai-help-sidebar__article-outline-results-container' ).show();
			}
		}, undefined, false, false, $( '.epkb-ai-help-sidebar__article-outline' ) );
	});

	/* TAB: AI --------------------------------------------------------------------*/
	$( document ).on( 'keypress', '.epkb-ai-help-sidebar__ai-input', function( e ) {

		// Send request only for 'Enter' key
		if ( e.which !== 13 ) {
			return;
		}

		let input = $( this );

		let prompt = ai_help_sidebar_sanitize_user_input( input.val() );

		$( '.epkb-ai-help-sidebar__ai-response-container' ).html( '<div class="epkb-ai-help-sidebar__ai-response-prompt">' +
			'<span class="epkbfa epkbfa-user epkb-ai-help-sidebar__ai-response-prompt-icon"></span>' +
			'<div class="epkb-ai-help-sidebar__ai-response-prompt-text">' + prompt + '</div>' +
		'</div>' );

		// Prevent next request until the current request is finished
		input.attr( 'disabled', 'disabled' );

		let postData = {
			action: 'epkb_ai_request',
			_wpnonce_epkb_ajax_action: epkb_ai_vars.nonce,
			input_text: prompt,
			ai_action: 'epkb_ai_chat'
		}

		epkb_send_ajax( postData, function( response ){
			if ( typeof response.result != 'undefined' && response.result.length > 0 ) {
				$( '.epkb-ai-help-sidebar__screen-usage' ).show().find( '.epkb-ai-help-sidebar__screen-usage-tokens span' ).html( response.tokens_used );
				$( '.epkb-ai-help-sidebar__ai-response-container' ).append( '<div class="epkb-ai-help-sidebar__ai-response-result">' +
					'<span class="ep_font_icon_light_bulb epkb-ai-help-sidebar__ai-response-result-icon"></span>' +
					'<div class="epkb-ai-help-sidebar__ai-response-result-text">' + ai_help_sidebar_sanitize_user_input( response.result ) + '</div>' +
				'</div>' );
			}
		}, undefined, false, function() {
			// Enable input and return focus to it
			input.removeAttr( 'disabled' ).focus();
		}, $( '.epkb-ai-help-sidebar__ai-response-container' ) );
	} );

	/* Feedback Form --------------------------------------------------------------------*/
	$( document ).on( 'click', '.epkb-ai__open-feedback-btn, #ai-help-feedback-link', function( e ) {
		$( '.epkb-ai-help-sidebar__nav-link' ).removeClass( 'epkb-ai-help-sidebar__nav-link--active' );

		let target_screen = $( this ).data( 'target' );
		$( '.epkb-ai-help-sidebar__body' ).removeClass( 'epkb-ai-help-sidebar__body--active' );
		$( '.epkb-ai-help-sidebar__body-' + target_screen ).addClass( 'epkb-ai-help-sidebar__body--active' );
		$( '.epkb-ai-help-sidebar' ).attr( 'data-active-tab', target_screen );
		$( '.epkb-ai-help-sidebar' ).attr( 'data-back-btn', 'show' );

		return false;
	} );

	$( document ).on( 'submit', '.epkb-ai-help-sidebar__feedback-form', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		// form have required attribute so we don't need to check it here
		let form = $( this );
		let postData = {
			action: 'epkb_ai_feedback',
			_wpnonce_epkb_ajax_action: epkb_ai_vars.nonce,
			feedback_text: form.find( 'textarea[name="feedback_text"]' ).val(),
			feedback_email: form.find( 'input[name="feedback_email"]' ).val(),
			feedback_name: form.find( 'input[name="feedback_name"]' ).val(),
		}

		epkb_send_ajax( postData, function( response ){
			if ( typeof response.message != 'undefined' && response.message.length > 0 ) {
				epkb_show_success_notification( response.message );

				// go back
				$( '.epkb-ai-help-sidebar__nav-back-btn' ).trigger( 'click' );
			}
		}, undefined, false, false, $( '.epkb-ai-help-sidebar__feedback-form' ));
	});

	/** AI helper functions --------------------------------------------------------------------*/

	function ai_help_sidebar_sanitize_user_input( input_text ) {
		return input_text.trim().replace( /&/g, "&amp;" ).replace( /</g, "&lt;" ).replace( />/g, "&gt;" ).replace( /"/g, "&quot;" ).replace( /'/g, "&#039;" )
	}

	// return if api key exists or false + show message
	function ai_help_check_api_key() {
		if ( $( '.epkb-ai-help-sidebar' ).attr( 'data-apikey-state' ) === 'off' ) {

			// for users with access to Settings tab open the tab and show corresponding message
			let api_key_input = $( '#openai_api_key' );
			if ( api_key_input.length && ! api_key_input.val().trim().length ) {
				epkb_show_error_notification( epkb_ai_vars.msg_no_key_admin );
				$( '.epkb-ai-help-sidebar__nav-link' ).trigger( 'click' );

			// for users with no access to Settings only show corresponding message
			} else if( ! api_key_input.length ) {
				epkb_show_error_notification( epkb_ai_vars.msg_no_key );
			}
			return false;
		}

		return true;
	}

	function strip_html_tags_and_attrs( input_html ) {
		return input_html.replace( /(<\/?(?:span|p|li|ul|ol|br|i|strong|b|h1|h2|h3|h4|h5|h6|em|sub|sup|pre)[^>]*>)|<[^>]+>/gi, '$1' ).replace( /\s+\S+?=("|')(.*?)("|')/g, '' );
	}

	function actual_content_length( input_html ) {
		return input_html.replace( /<[^>]*>/g, '' ).replace(/&nbsp;/g, '').trim().length;
	}


	/* TAB: Settings --------------------------------------------------------------------*/
	// Save Settings
	$( document ).on( 'click', '.epkb-ai-help-sidebar__settings-save-btn', function() {

		let $wrap = $( '.epkb-ai-help-sidebar__settings-form' );

		if ( ! $wrap.length ) {
			return;
		}

		let openai_api_key = $( 'input[name="openai_api_key"]' ).val().trim();

		let postData = {
			action: 'epkb_ai_request',
			_wpnonce_epkb_ajax_action: epkb_ai_vars.nonce,
			ai_action: 'epkb_ai_save_settings',
			openai_api_key: openai_api_key,
			disable_openai: $('input[name="disable_openai"]').prop('checked') ? 'on' : 'off'
		}

		epkb_send_ajax( postData, function( response ) {
			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );
				let api_key_state = openai_api_key.length > 0 ? 'on' : 'off';
				$( '.epkb-ai-help-sidebar' ).attr( 'data-apikey-state', api_key_state )
			}
		}, undefined, false, false, $( '.epkb-ai-help-sidebar__body-settings' ) );

		return false;
	} );


	/* Dialogs --------------------------------------------------------------------*/
	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-bottom-notice-message">' +
			'<span class="eckb-bottom-notice-message-icon ' + ( $type == 'success' ? 'ep_font_icon_checkmark' : 'epkbfa epkbfa-times-circle' ) + '"></span>' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>'+$title+'</h4>' : '' ) +
			($message ? '<p>' + $message + '</p>': '' ) +
			'</span>' +
			'</div>' +
			'</div>';
	}
	
	let epkb_notification_timeout;
	
	function epkb_show_error_notification( $message, $title = '' ) {

		$( '.epkb-ai-help-sidebar__bottom-notice-message-container' ).html( '' );
		$( '.epkb-ai-help-sidebar__bottom-notice-message-container' ).append( epkb_admin_notification( $title, $message, 'error' ) );

		clearTimeout( epkb_notification_timeout );
		epkb_notification_timeout = setTimeout( function() {
			$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
		}, 10000 );
	}

	function epkb_show_success_notification( $message, $title = '' ) {
		$( '.epkb-ai-help-sidebar__bottom-notice-message-container' ).html( '' );
		$( '.epkb-ai-help-sidebar__bottom-notice-message-container' ).append( epkb_admin_notification( $title, $message, 'success' ) );

		clearTimeout( epkb_notification_timeout );
		epkb_notification_timeout = setTimeout( function() {
			$( '.eckb-bottom-notice-message' ).addClass( 'fadeOutDown' );
		}, 10000 );
	}
	
	// scroll to element with animation
	function epkb_scroll_to( $el ) {
		if ( ! $el.length ) {
			return;
		}
	
		$("html, body").animate({ scrollTop: $el.offset().top - 100 }, 300);
	}

	// Close Button Message if Close Icon clicked
	$( document.body ).on( 'click', '.epkb-close-notice', function() {
		let bottom_message = $( this ).closest( '.eckb-bottom-notice-message' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	} );

	$( document.body ).on( 'click', '.epkbfa-times-circle', function() {
		let bottom_message = $( this ).closest( '.epkb-ai-help-sidebar__bottom-notice-message-container' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	} );

	
	/*************************************************************************************************
	 *
	 *  AJAX calls
	 *
	 ************************************************************************************************/

	// generic AJAX call handler
	function epkb_send_ajax( postData, refreshCallback, callbackParam, reload, alwaysCallback, $loader ) {

		let errorMsg;
		let theResponse;
		refreshCallback = (typeof refreshCallback === 'undefined' ) ? 'epkb_callback_noop' : refreshCallback;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				if ( typeof $loader == 'undefined' || $loader === false ) {
					epkb_loading_Dialog( 'show', epkb_ai_vars.msg_ai_help_loading );
				}

				if ( typeof $loader == 'object' ) {
					epkb_loading_Dialog( 'show', epkb_ai_vars.msg_ai_help_loading, $loader);
				}
			}
		}).done(function (response){
			theResponse = ( response ? response : '' );
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				errorMsg = theResponse.message ? theResponse.message : epkb_admin_notification( '', epkb_ai_vars.reload_try_again, 'error' );
			}

		}).fail( function ( response, textStatus, error ){
			//noinspection JSUnresolvedVariable
			errorMsg = ( error ? ' [' + error + ']' : epkb_ai_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			errorMsg = epkb_admin_notification(epkb_ai_vars.error_occurred + '. ' + epkb_ai_vars.msg_try_again, errorMsg, 'error' );
		}).always(function() {

			theResponse = (typeof theResponse === 'undefined' ) ? '' : theResponse;

			if ( typeof alwaysCallback == 'function' ) {
				alwaysCallback( theResponse );
			}

			if ( typeof $loader == 'undefined' || $loader === false ) {
				epkb_loading_Dialog( 'remove', '' );
			}

			if ( typeof $loader == 'object' ) {
				epkb_loading_Dialog( 'remove', '', $loader );
			}

			if ( errorMsg ) {
				$( '.epkb-ai-help-sidebar__bottom-notice-message-container' ).html( '' );
				$( '.epkb-ai-help-sidebar__bottom-notice-message-container' ).append(errorMsg);

				setTimeout( function() {
					$( '.eckb-bottom-notice-message' ).addClass( 'fadeOutDown' );
				}, 10000 );
				return;
			}

			if ( typeof refreshCallback === "function" ) {

				if ( callbackParam === 'undefined' ) {
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

	$('.epkb-ai-help-sidebar__improve-text-result__textarea').on( 'click', function() {
		this.focus();
		this.select();
	});

	/*************************************************************************************************
	 *
	 *  Utilities
	 *
	 ***********************************************************************************************/

	/**
	 * Displays a Center Dialog box with a loading icon and text.
	 *
	 * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	 * This code is used in these files, any changes here must be done to the following files.
	 *   - admin-plugin-pages.js
	 *   - admin-kb-config-scripts.js
	 *   - admin-kb-wizard-script.js
	 *	 - admin-kb-setup-wizard-script.js
	 * @param  {string}displayType Show or hide Dialog initially. ( show, remove )
	 * @param  {string}message OptionalMessage output from database or settings.
	 *
	 * @return {html}  Removes old dialogs and adds the HTML to the end body tag with optional message.
	 */
	function epkb_loading_Dialog( displayType, message, parent_container ){

    		if ( displayType === 'show' ) {

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
    			parent_container.append( output );

    		} else if( displayType === 'remove' ) {

    			// Remove loading dialogs.
    			parent_container.find( '.epkb-admin-dialog-box-loading' ).remove();
    			parent_container.find( '.epkb-admin-dialog-box-overlay' ).remove();
    		}
    	}
});
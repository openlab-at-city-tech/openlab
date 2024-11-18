"use strict";

/**
 * General functions for epkb js. Will work in all js files
 * Example:
 * $(document).epkb( 'action_name', 'params' );
 * or some element instead "document" if the action need it like usual jQuery widget
 */
(function ( $ ) {

	// safe place to name functions without epkb prefix

	// change width of the filled part of the progress bar
	function set_progress_bar_width( $bar, percent ) {

		percent = parseFloat( percent );

		if ( percent > 100 ) {
			percent = 100;
		}

		if ( percent < 0 ) {
			percent = 0;
		}

		$bar.find('.epkb-progress__bar div').css({
			'width' : percent + '%'
		});

		$bar.find('.epkb-progress__percentage').text( percent + '%' );
	}

	function add_progress_bar_log_message( $bar, params ) {
		if ( typeof params !== 'object' ) {
			return;
		}

		if ( typeof params.type == 'undefined' ) {
			params.type = 'in-progress';
		}

		if ( typeof params.message == 'undefined' ) {
			params.message = '';
		}

		// only 1 action can be in progress
		$bar.find('.epkb-export-progress__row--in-progress').remove();

		let html = $bar.find('.epkb-progress__log').html();

		if ( params.type == 'in-progress' ) {
			html += `
					<div class="epkb-export-progress__row epkb-export-progress__row--in-progress">
						<div class="epkb-export__title">${params.message}</div>
						<div class="epkb-export__icon"><i class="epkbfa epkbfa-spinner"></i></div>
					</div>`;
		}

		if ( params.type == 'error' ) {
			html += `
					<div class="epkb-export-progress__row epkb-export-progress__row--error">
						<div class="epkb-export__title">${params.message}</div>
						<div class="epkb-export__icon"><i class="epkbfa epkbfa-exclamation-circle"></i></div>
					</div>`;
		}

		if ( params.type == 'success' ) {
			html += `
					<div class="epkb-export-progress__row epkb-export-progress__row--success">
						<div class="epkb-export__title">${params.message}</div>
						<div class="epkb-export__icon"><i class="epkbfa epkbfa-check-circle"></i></div>
					</div>`;
		}

		$bar.find('.epkb-progress__log').html( html );
	}

	// response from ajax_show_error_die functions
	function show_ajax_error_notice( errorMsg ) {

		$('.eckb-bottom-notice-message').remove();
		$('body').append( errorMsg ).removeClass('fadeOutDown');

		let $el = $('.eckb-bottom-notice-message');
		setTimeout( function() {
			$el.addClass( 'fadeOutDown' );
		}, 10000 );
	}

	// show any simple notice
	// params = { message: '', $title: '', type: 'success' }
	function show_notice( params ) {
		let title = '';

		if ( typeof params.title != 'undefined' && params.title ) {
			title = '<h4>' + title + '</h4>';
		}

		if ( typeof params.message == 'undefined' ) {
			params.message = '';
		}

		if ( typeof params.type == 'undefined' ) {
			params.type = 'success';
		}

		let notice = `
		<div class="eckb-bottom-notice-message">
			<div class="contents">
				<span class="${params.type}">
					${title}
					<p>${params.message}</p>
				</span>
			</div>
			<div class='epkb-close-notice epkbfa epkbfa-window-close'></div>
		</div>`;

		$('.eckb-bottom-notice-message').remove();
		$('body').append( notice ).removeClass('fadeOutDown');

		let $el = $('.eckb-bottom-notice-message');
		setTimeout( function() {
			$el.addClass( 'fadeOutDown' );
		}, 10000 );
	}

	// add jQuery widget
	$.fn.epkb = function( action, params ) {

		switch ( action ) {
			case 'tools/hide_panels':
				$(document).trigger('epkb_hide_export_import_panels');
				break;

			case 'progress/set':
				set_progress_bar_width( $(this), params );
				break;

			case 'progress/show':
				break;

			case 'progress/hide':
				break;

			case 'progress/add_log':
				add_progress_bar_log_message( $(this), params );
				break;

			case 'progress/clear_log':
				$(this).find('.epkb-progress__log').html('');
				break;

			case 'progress/add_status':
				$(this).find('.epkb-data-status-log').html( params );
				break;

			case 'ajax/error_message':
				show_ajax_error_notice( params );
				break;

			case 'notice/show':
				show_notice( params );
				break;
		}

		return this;
	};
}( jQuery ));

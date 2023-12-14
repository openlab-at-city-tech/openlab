import Swal from 'sweetalert2';

/**
 * Handles:
 * - Copy to Clipboard functionality
 * - Dismissable Notices
 *
 * @since 1.5.0
 */

(function($, window, document, nextgen_gallery_admin ) {

	let nextgen_notifications;

	window.nextgen_notifications = nextgen_notifications = {
		init() {
			var app = this;
			app.$drawer = $( '#nextgen-notifications-drawer' );
			app.find_elements();
			app.init_open();
			app.init_close();
			app.init_dismiss();
			app.init_view_switch();
			app.update_count( app.active_count );
		},

		should_init() {
			var app = this;
			return app.$drawer.length > 0;
		},
		find_elements() {
			var app = this;
			app.$open_button      = $( '#nextgen-notifications-button' );
			app.$count            = app.$drawer.find( '#nextgen-notifications-count' );
			app.$dismissed_count  = app.$drawer.find( '#nextgen-notifications-dismissed-count' );
			app.active_count      = app.$open_button.data( 'count' ) ? app.$open_button.data( 'count' ) : 0;
			app.dismissed_count   = app.$open_button.data( 'dismissed' );
			app.$body             = $( 'body' );
			app.$dismissed_button = $( '#nextgen-notifications-show-dismissed' );
			app.$active_button    = $( '#nextgen-notifications-show-active' );
			app.$active_list      = $( '.nextgen-notifications-list .nextgen-notifications-active' );
			app.$dismissed_list   = $( '.nextgen-notifications-list .nextgen-notifications-dismissed' );
			app.$dismiss_all      = $( '#nextgen-dismiss-all' );
		},
		update_count( count ) {
			var app = this;
			app.$open_button.data( 'count', count ).attr( 'data-count', count );
			if ( 0 === count ) {
				app.$open_button.removeAttr( 'data-count' );
			}
			app.$count.text( count );
			app.dismissed_count += Math.abs( count - app.active_count );
			app.active_count     = count;

			app.$dismissed_count.text( app.dismissed_count );

			if ( 0 === app.active_count ) {
				app.$dismiss_all.hide();
			}
		},
		init_open() {
			var app = this;
			app.$open_button.on(
				'click',
				function ( e ) {
					e.preventDefault();
					app.$body.addClass( 'nextgen-notifications-open' );
				}
			);
		},
		init_close() {

			var app = this;
			app.$body.on(
				'click',
				'.nextgen-notifications-close, .nextgen-notifications-overlay',
				function ( e ) {
					e.preventDefault();
					app.$body.removeClass( 'nextgen-notifications-open' );
				}
			);
		},
		init_dismiss() {
			var app = this;
			app.$drawer.on(
				'click',
				'.nextgen-notification-dismiss',
				function ( e ) {
					e.preventDefault();
					const id = $( this ).data( 'id' );
					app.dismiss_notification( id );
					if ( 'all' === id ) {
						app.move_to_dismissed( app.$active_list.find( 'li' ) );
						app.update_count( 0 );
						return;
					}
					app.move_to_dismissed( $( this ).closest( 'li' ) );
					app.update_count( app.active_count - 1 );
				}
			);
		},
		move_to_dismissed( element ) {
			var app = this;
			element.slideUp(
				function () {
					$( this ).prependTo( app.$dismissed_list ).show();
				}
			);
		},
		dismiss_notification( id ) {
			var app = this;
			return $.post(
				ajaxurl,
				{
					action: 'nextgen_notification_dismiss',
					nonce: nextgen_gallery_admin.dismiss_notification_nonce,
					id: id,
				}
			);
		},
		init_view_switch() {
			var app = this;
			app.$dismissed_button.on(
				'click',
				function ( e ) {
					e.preventDefault();
					app.$drawer.addClass( 'show-dismissed' );
				}
			);
			app.$active_button.on(
				'click',
				function ( e ) {
					e.preventDefault();
					app.$drawer.removeClass( 'show-dismissed' );
				}
			);
		}
	};

	// DOM ready
	$(function() {
		nextgen_notifications.init();
	});

})(jQuery, window, document, nextgen_gallery_admin );


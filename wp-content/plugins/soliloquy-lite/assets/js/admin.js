import Swal from 'sweetalert2';

/**
 * Handles:
 * - Copy to Clipboard functionality
 * - Dismissable Notices
 *
 * @since 1.5.0
 */

(function($, window, document, soliloquy_admin ) {
	/* global ajaxurl, envira */

	let soliloquy_notifications,
		soliloquy_connect;
	window.soliloquy_notifications = soliloquy_notifications = {
		init() {
			var app = this;
			app.$drawer = $( '#soliloquy-notifications-drawer' );
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
			app.$open_button      = $( '#soliloquy-notifications-button' );
			app.$count            = app.$drawer.find( '#soliloquy-notifications-count' );
			app.$dismissed_count  = app.$drawer.find( '#soliloquy-notifications-dismissed-count' );
			app.active_count      = app.$open_button.data( 'count' ) ? app.$open_button.data( 'count' ) : 0;
			app.dismissed_count   = app.$open_button.data( 'dismissed' );
			app.$body             = $( 'body' );
			app.$dismissed_button = $( '#soliloquy-notifications-show-dismissed' );
			app.$active_button    = $( '#soliloquy-notifications-show-active' );
			app.$active_list      = $( '.soliloquy-notifications-list .soliloquy-notifications-active' );
			app.$dismissed_list   = $( '.soliloquy-notifications-list .soliloquy-notifications-dismissed' );
			app.$dismiss_all      = $( '#soliloquy-dismiss-all' );
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
					app.$body.addClass( 'soliloquy-notifications-open' );
				}
			);
		},
		init_close() {

			var app = this;
			app.$body.on(
				'click',
				'.soliloquy-notifications-close, .soliloquy-notifications-overlay',
				function ( e ) {
					e.preventDefault();
					app.$body.removeClass( 'soliloquy-notifications-open' );
				}
			);
		},
		init_dismiss() {
			var app = this;
			app.$drawer.on(
				'click',
				'.soliloquy-notification-dismiss',
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
					action: 'soliloquy_notification_dismiss',
					nonce: soliloquy_admin.dismiss_notification_nonce,
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

	window.soliloquy_connect = soliloquy_connect = {

		init() {
			$( this.ready() );
		},
		ready(){
			this.connectClicked();

		},
		connectClicked() {
			let app = this;
			$( '#soliloquy-settings-connect-btn' ).on(
				'click',
				function (e) {
					e.preventDefault();
					app.gotoUpgradeUrl();
				}
			);
		},
		gotoUpgradeUrl() {
			let app = this;
			let data = {
				action: 'soliloquy_connect', key: $( '#soliloquy-settings-key' ).val(), _wpnonce: soliloquy_admin.connect_nonce,
			};

			$.post( ajaxurl, data ).done(
				function ( res ) {
					if ( res.success ) {
						if ( res.data.reload ) {
							app.proAlreadyInstalled( res );
							return;
						}
						window.location.href = res.data.url;
						return;
					}

					Swal.fire(
						{
							title: soliloquy_admin.oops,
							html: res.data.message,
							icon: 'warning',
							confirmButtonColor: '#3085d6',
							confirmButtonText: soliloquy_admin.ok,
							customClass: {
								confirmButton: 'soliloquy-button',
							},
						}
					);
				}
			).fail(
				function ( xhr ) {
					app.failAlert( xhr );
				}
			);
		},
		proAlreadyInstalled( res ) {
			Swal.fire(
				{
					title: soliloquy_admin.almost_done,
					text: res.data.message,
					icon: 'success',
					confirmButtonColor: '#3085d6',
					confirmButtonText: soliloquy_admin.plugin_activate_btn,
					customClass: {
						confirmButton: 'soliloquy-button',
					},
				}
			).then(
				( result ) => {
					if ( result.isConfirmed ) {
						window.location.reload();
					}
				}
			);
		},
		failAlert() {
			Swal.fire(
				{
					title: soliloquy_admin.oops,
					html: soliloquy_admin.server_error + '<br>' + xhr.status + ' ' + xhr.statusText + ' ' + xhr.responseText,
					icon: 'warning',
					confirmButtonColor: '#3085d6',
					confirmButtonText: soliloquy_admin.ok,
					customClass: {
						confirmButton: 'envira-button',
					},
				}
			);
		},
	}
		// DOM ready
		$(function() {
			soliloquy_connect.init();
			soliloquy_notifications.init();
			$('#screen-meta-links').prependTo('#soliloquy-header-temp');
			$('#screen-meta').prependTo('#soliloquy-header-temp');
		});

})(jQuery, window, document, soliloquy_admin );


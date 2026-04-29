/**
 * KB Setup Steps - JavaScript for Help Resources page
 */
jQuery( document ).ready( function( $ ) {
	'use strict';

	const setupSteps = {

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			// Done button click
			$( document ).on( 'click', '.epkb-setup-steps__btn--done', this.handleDoneClick.bind( this ) );

			// Later button click
			$( document ).on( 'click', '.epkb-setup-steps__btn--later', this.handleLaterClick.bind( this ) );

			// Complete later step from hidden list
			$( document ).on( 'click', '.epkb-setup-steps__btn--complete-later', this.handleCompleteLaterClick.bind( this ) );

			// Restore button click
			$( document ).on( 'click', '.epkb-setup-steps__btn--restore', this.handleRestoreClick.bind( this ) );

			// Toggle completed steps
			$( document ).on( 'click', '.epkb-setup-steps__toggle-btn', this.toggleCompletedSteps.bind( this ) );

			// Reset button click
			$( document ).on( 'click', '.epkb-setup-steps__btn--reset', this.handleResetClick.bind( this ) );

			// Inline pointer button click
			$( document ).on( 'click', '.epkb-setup-steps__btn--inline-pointer', this.handleInlinePointerClick.bind( this ) );

			// Learn More dialog button click
			$( document ).on( 'click', 'button.epkb-setup-steps__btn--learn-more', this.handleLearnMoreClick.bind( this ) );

			// Learn More dialog close handlers
			$( document ).on( 'click', '.epkb-setup-steps-dialog__close, .epkb-setup-steps-dialog-overlay', this.closeLearnMoreDialog.bind( this ) );
			$( document ).on( 'keydown', this.handleDialogKeydown.bind( this ) );

			// Show celebration fireworks if all complete
			this.checkForCelebration();
		},

		handleDoneClick: function( e ) {
			e.preventDefault();
			this.submitStepAction( $( e.currentTarget ), 'done' );
		},

		handleLaterClick: function( e ) {
			e.preventDefault();
			this.submitStepAction( $( e.currentTarget ), 'later' );
		},

		handleCompleteLaterClick: function( e ) {
			e.preventDefault();
			this.submitStepAction( $( e.currentTarget ), 'done' );
		},

		submitStepAction: function( $btn, mode ) {
			const $item = $btn.closest( '.epkb-setup-steps__item' );
			const $container = $btn.closest( '.epkb-setup-steps' );
			const stepKey = $btn.data( 'step-key' );
			const kbId = parseInt( $container.data( 'kb-id' ), 10 );
			const actionMode = mode === 'later' ? 'later' : 'done';

			if ( ! stepKey ) {
				return;
			}

			const $actionButtons = $item.find( '.epkb-setup-steps__btn--done, .epkb-setup-steps__btn--later, .epkb-setup-steps__btn--complete-later' );
			const $restoreBtn = $item.find( '.epkb-setup-steps__btn--restore' );

			$actionButtons.prop( 'disabled', true );
			$restoreBtn.prop( 'disabled', true );
			$btn.addClass( 'epkb-loading' );

			const data = {
				action: 'epkb_mark_setup_step_done',
				step_key: stepKey,
				mode: actionMode,
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			if ( ! isNaN( kbId ) && kbId > 0 ) {
				data.kb_id = kbId;
			}

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function( response ) {
					if ( response.success ) {
						location.reload();
						return;
					}

					$actionButtons.prop( 'disabled', false );
					$restoreBtn.prop( 'disabled', false );
					$btn.removeClass( 'epkb-loading' );
					console.error( 'Error marking step:', response.data );
				},
				error: function( xhr, status, error ) {
					$actionButtons.prop( 'disabled', false );
					$restoreBtn.prop( 'disabled', false );
					$btn.removeClass( 'epkb-loading' );
					console.error( 'AJAX error:', error );
				}
			} );
		},

		checkForCelebration: function() {
			const $celebration = $( '.epkb-setup-steps__celebration' );
			if ( $celebration.length ) {
				// Trigger celebration fireworks
				this.showCelebrationFireworks();
			}
		},

		showCelebrationFireworks: function() {
			const $celebration = $( '.epkb-setup-steps__celebration' );
			if ( ! $celebration.length ) return;

			const offset = $celebration.offset();
			const width = $celebration.outerWidth();
			const height = $celebration.outerHeight();
			const colors = [ '#fbbf24', '#f59e0b', '#d97706', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899' ];

			// Create container
			let $container = $( '.epkb-fireworks-container' );
			if ( ! $container.length ) {
				$container = $( '<div class="epkb-fireworks-container"></div>' ).appendTo( 'body' );
			}

			// Launch fireworks in waves
			const launchWave = function( delay ) {
				setTimeout( function() {
					for ( let i = 0; i < 25; i++ ) {
						const color = colors[ Math.floor( Math.random() * colors.length ) ];
						const size = Math.random() * 10 + 6;
						const left = offset.left + Math.random() * width;
						const top = offset.top + ( height / 2 );
						const spread = ( Math.random() - 0.5 ) * 400;
						const particleDelay = Math.random() * 0.2;
						const duration = Math.random() * 0.5 + 1.2;

						const $particle = $( '<div class="epkb-firework"></div>' )
							.css( {
								left: left + 'px',
								top: top + 'px',
								width: size + 'px',
								height: size + 'px',
								backgroundColor: color,
								animationDelay: particleDelay + 's',
								animationDuration: duration + 's',
								'--spread': spread + 'px'
							} );

						$container.append( $particle );

						setTimeout( function() {
							$particle.remove();
						}, ( particleDelay + duration ) * 1000 + 100 );
					}
				}, delay );
			};

			// 3 waves of fireworks
			launchWave( 0 );
			launchWave( 400 );
			launchWave( 800 );

			// Clean up container
			setTimeout( function() {
				if ( $container.children().length === 0 ) {
					$container.remove();
				}
			}, 3000 );
		},

		handleRestoreClick: function( e ) {
			e.preventDefault();

			const $btn = $( e.currentTarget );
			const $container = $btn.closest( '.epkb-setup-steps' );
			const stepKey = $btn.data( 'step-key' );
			const kbId = parseInt( $container.data( 'kb-id' ), 10 );

			if ( ! stepKey ) {
				return;
			}

			$btn.prop( 'disabled', true ).addClass( 'epkb-loading' );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: Object.assign( {
					action: 'epkb_restore_setup_step',
					step_key: stepKey,
					_wpnonce_epkb_ajax_action: epkb_vars.nonce
				}, ( ! isNaN( kbId ) && kbId > 0 ) ? { kb_id: kbId } : {} ),
				success: function( response ) {
					if ( response.success ) {
						// Reload the page to refresh the steps
						location.reload();
					} else {
						$btn.prop( 'disabled', false ).removeClass( 'epkb-loading' );
						console.error( 'Error restoring step:', response.data );
					}
				},
				error: function( xhr, status, error ) {
					$btn.prop( 'disabled', false ).removeClass( 'epkb-loading' );
					console.error( 'AJAX error:', error );
				}
			} );
		},

		toggleCompletedSteps: function( e ) {
			e.preventDefault();

			const $btn = $( e.currentTarget );
			const $list = $( '.epkb-setup-steps__completed-list' );
			const completedCount = $list.find( '.epkb-setup-steps__item' ).length;

			// Use toggle instead of slideToggle to avoid issues with column-count layout
			if ( $list.is( ':visible' ) ) {
				$list.hide();
				$btn.html(
					'<span class="epkbfa epkbfa-chevron-down"></span> ' +
					( epkb_vars.show_completed_text || 'Show Completed Steps' ) +
					' (' + completedCount + ')'
				);
			} else {
				$list.show();
				$btn.html(
					'<span class="epkbfa epkbfa-chevron-up"></span> ' +
					( epkb_vars.hide_completed_text || 'Hide Completed Steps' ) +
					' (' + completedCount + ')'
				);
			}
		},

		handleResetClick: function( e ) {
			e.preventDefault();

			const $btn = $( e.currentTarget );

			$btn.prop( 'disabled', true ).addClass( 'epkb-loading' );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'epkb_reset_setup_steps',
					_wpnonce_epkb_ajax_action: epkb_vars.nonce
				},
				success: function( response ) {
					if ( response.success ) {
						// Reload page to show fresh state
						location.reload();
					} else {
						$btn.prop( 'disabled', false ).removeClass( 'epkb-loading' );
						console.error( 'Error resetting steps:', response.data );
					}
				},
				error: function( xhr, status, error ) {
					$btn.prop( 'disabled', false ).removeClass( 'epkb-loading' );
					console.error( 'AJAX error:', error );
				}
			} );
		},

		handleInlinePointerClick: function( e ) {
			e.preventDefault();

			const $btn = $( e.currentTarget );
			const targetElement = $btn.data( 'target-element' );
			const pointerTitle = $btn.data( 'pointer-title' );
			const pointerContent = $btn.data( 'pointer-content' );

			if ( ! targetElement ) {
				return;
			}

			// Find the target element
			const $target = this.findTarget( targetElement );
			if ( ! $target || ! $target.length ) {
				console.warn( 'Target element not found:', targetElement );
				return;
			}

			// Check if this is an admin menu item
			const isAdminMenuItem = $target.closest( '#adminmenu' ).length > 0;

			// For admin menu items, expand the submenu to ensure visibility
			if ( isAdminMenuItem ) {
				const $menuItem = $target.closest( 'li.menu-top' );
				if ( $menuItem.length && ! $menuItem.hasClass( 'wp-has-current-submenu' ) ) {
					$menuItem.addClass( 'opensub' ).find( '.wp-submenu' ).show();
				}
			}

			// Close any existing pointers
			$( '.wp-pointer' ).remove();

			// Show the pointer
			const content = '<h3>' + pointerTitle + '</h3><p>' + pointerContent + '</p>';
			$target.pointer( {
				content: content,
				position: {
					edge: isAdminMenuItem ? 'left' : 'top',
					align: 'center'
				},
				pointerClass: 'epkb-setup-pointer',
				buttons: function( event, t ) {
					const $closeBtn = $( '<button type="button" class="button button-primary epkb-pointer-next">' + ( epkb_vars.got_it_text || 'Got it!' ) + '</button>' );
					$closeBtn.on( 'click', function() {
						t.element.pointer( 'close' );
					} );
					return $closeBtn;
				}
			} ).pointer( 'open' );
		},

		findTarget: function( selector ) {
			// Handle comma-separated selectors (find first match)
			const selectors = selector.split( ',' );
			for ( let i = 0; i < selectors.length; i++ ) {
				const sel = selectors[i].trim();
				const $el = $( sel );
				if ( $el.length ) {
					const $visible = $el.filter( ':visible' );
					if ( $visible.length ) {
						return $visible.first();
					}
					return $el.first();
				}
			}
			return null;
		},

		handleLearnMoreClick: function( e ) {
			e.preventDefault();

			const $btn = $( e.currentTarget );
			const title = $btn.data( 'title' );
			const description = $btn.data( 'description' );
			const docUrl = $btn.data( 'doc-url' );
			const videoUrl = $btn.data( 'video-url' );
			const askAi = $btn.data( 'ask-ai' );
			const actionUrl = $btn.data( 'action-url' );
			const actionText = $btn.data( 'action-text' );

			const $dialog = $( '.epkb-setup-steps-dialog' );
			const $overlay = $( '.epkb-setup-steps-dialog-overlay' );

			// Set dialog content
			$dialog.find( '.epkb-setup-steps-dialog__title' ).text( title );
			$dialog.find( '.epkb-setup-steps-dialog__description' ).html( this.formatDescription( description ) );

			// Show/hide footer buttons - simple display toggle, no animations
			const $footer = $dialog.find( '.epkb-setup-steps-dialog__footer' );
			const $actionBtn = $dialog.find( '.epkb-setup-steps-dialog__btn--action' );
			const $docBtn = $dialog.find( '.epkb-setup-steps-dialog__btn--doc' );
			const $videoBtn = $dialog.find( '.epkb-setup-steps-dialog__btn--video' );
			const $askAiBtn = $dialog.find( '.epkb-setup-steps-dialog__btn--ask-ai' );

			// Reset all buttons to hidden
			$actionBtn[0].style.display = 'none';
			$docBtn[0].style.display = 'none';
			$videoBtn[0].style.display = 'none';
			$askAiBtn[0].style.display = 'none';

			// Show only needed buttons
			if ( actionUrl && actionText ) {
				$actionBtn.attr( 'href', actionUrl ).find( '.epkb-setup-steps-dialog__btn-text' ).text( actionText );
				$actionBtn[0].style.display = 'inline-flex';
			}

			if ( docUrl ) {
				$docBtn.attr( 'href', docUrl );
				$docBtn[0].style.display = 'inline-flex';
			}

			if ( videoUrl ) {
				$videoBtn.attr( 'href', videoUrl );
				$videoBtn[0].style.display = 'inline-flex';
			}

			if ( askAi ) {
				$askAiBtn[0].style.display = 'inline-flex';
			}

			// Show/hide footer
			const hasButtons = actionUrl || docUrl || videoUrl || askAi;
			$footer[0].style.display = hasButtons ? 'flex' : 'none';

			// Show dialog
			$overlay.addClass( 'epkb-setup-steps-dialog-overlay--active' );
			$dialog.addClass( 'epkb-setup-steps-dialog--active' );

			// Focus close button for accessibility
			$dialog.find( '.epkb-setup-steps-dialog__close' ).focus();
		},

		closeLearnMoreDialog: function( e ) {
			if ( e ) {
				e.preventDefault();
			}
			$( '.epkb-setup-steps-dialog-overlay' ).removeClass( 'epkb-setup-steps-dialog-overlay--active' );
			$( '.epkb-setup-steps-dialog' ).removeClass( 'epkb-setup-steps-dialog--active' );
		},

		handleDialogKeydown: function( e ) {
			// Close dialog on Escape key
			if ( e.key === 'Escape' && $( '.epkb-setup-steps-dialog--active' ).length ) {
				this.closeLearnMoreDialog();
			}
		},

		/**
		 * Format description text to HTML with proper lists
		 * Converts: "Text: • item1 • item2" to "<p>Text:</p><ul><li>item1</li><li>item2</li></ul>"
		 * Converts: "Text: 1) step1 2) step2" to "<p>Text:</p><ol><li>step1</li><li>step2</li></ol>"
		 */
		formatDescription: function( text ) {
			if ( ! text ) {
				return '';
			}

			// Escape HTML to prevent XSS
			const escapeHtml = function( str ) {
				const div = document.createElement( 'div' );
				div.textContent = str;
				return div.innerHTML;
			};

			text = escapeHtml( text );

			// Check for numbered steps pattern: "1) ... 2) ... 3) ..."
			const numberedPattern = /(\d+)\)\s+/g;
			if ( numberedPattern.test( text ) ) {
				// Split by numbered pattern
				const parts = text.split( /\d+\)\s+/ );
				const intro = parts[0].trim();
				const items = parts.slice( 1 ).map( item => item.trim() ).filter( item => item );

				if ( items.length > 0 ) {
					let html = intro ? '<p>' + intro + '</p>' : '';
					html += '<ol>';
					items.forEach( function( item ) {
						html += '<li>' + item + '</li>';
					} );
					html += '</ol>';
					return html;
				}
			}

			// Check for bullet pattern: "• item1 • item2"
			if ( text.includes( '•' ) ) {
				const parts = text.split( '•' );
				const intro = parts[0].trim();
				const items = parts.slice( 1 ).map( item => item.trim() ).filter( item => item );

				if ( items.length > 0 ) {
					let html = intro ? '<p>' + intro + '</p>' : '';
					html += '<ul>';
					items.forEach( function( item ) {
						html += '<li>' + item + '</li>';
					} );
					html += '</ul>';
					return html;
				}
			}

			// No special formatting, return as paragraph
			return '<p>' + text + '</p>';
		}
	};

	setupSteps.init();
} );

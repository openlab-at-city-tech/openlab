<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WordPress Pointers API integration for KB Setup Steps guided tours
 */
class EPKB_Setup_Pointers {

	const POINTER_PARAM = 'epkb_show_pointer';

	public function __construct() {
		// Admin page pointers
		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_admin_pointer' ) );

		// Frontend page pointers (for admin users on KB pages)
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_frontend_pointer' ) );
	}

	/**
	 * Check if we should show a pointer and enqueue scripts for admin pages
	 */
	public function maybe_enqueue_admin_pointer() {

		// Check if pointer parameter is present
		$step_key = isset( $_GET[ self::POINTER_PARAM ] ) ? sanitize_key( $_GET[ self::POINTER_PARAM ] ) : '';
		if ( empty( $step_key ) ) {
			return;
		}

		// Verify user has permission
		if ( ! EPKB_Admin_UI_Access::is_user_admin_editor() ) {
			return;
		}

		$this->enqueue_pointer_scripts( $step_key );
	}

	/**
	 * Check if we should show a pointer on frontend KB pages (for admin users)
	 */
	public function maybe_enqueue_frontend_pointer() {

		// Check if pointer parameter is present
		$step_key = isset( $_GET[ self::POINTER_PARAM ] ) ? sanitize_key( $_GET[ self::POINTER_PARAM ] ) : '';
		if ( empty( $step_key ) ) {
			return;
		}

		// Only for logged-in users with KB admin access
		if ( ! is_user_logged_in() || ! EPKB_Admin_UI_Access::is_user_admin_editor() ) {
			return;
		}

		// Check if this is a KB page
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return;
		}

		$this->enqueue_pointer_scripts( $step_key, true );
	}

	/**
	 * Enqueue pointer scripts and styles
	 */
	private function enqueue_pointer_scripts( $step_key, $is_frontend = false ) {

		// Get step configuration
		$steps = EPKB_Setup_Steps::get_all_steps();
		if ( ! isset( $steps[ $step_key ] ) ) {
			return;
		}

		$step = $steps[ $step_key ];
		if ( empty( $step['show_me'] ) || ! is_array( $step['show_me'] ) ) {
			return;
		}

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		$kb_id = EPKB_Utilities::is_positive_int( $kb_id ) ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		// Enqueue WordPress pointer styles and scripts
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		// Add inline styles for frontend pointers
		if ( $is_frontend ) {
			wp_add_inline_style( 'wp-pointer', '
				.wp-pointer.epkb-setup-pointer { z-index: 10000000 !important; }
				.epkb-fe-tooltip { position: relative; margin: 15px 0; background: #fff; border-radius: 6px; box-shadow: 0 6px 25px rgba(21, 128, 61, 0.25); border: 2px solid #22c55e; max-width: 320px; }
				.epkb-fe-tooltip__arrow { position: absolute; top: -10px; left: 25px; width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-bottom: 10px solid #22c55e; }
				.epkb-fe-tooltip__content { padding: 0; }
				.epkb-fe-tooltip__content h3 { margin: 0; padding: 14px 18px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: #fff; font-size: 15px; font-weight: 600; border-radius: 4px 4px 0 0; line-height: 1.3; }
				.epkb-fe-tooltip__content h3 small { font-weight: normal; opacity: 0.85; margin-left: 8px; font-size: 12px; }
				.epkb-fe-tooltip__content p { margin: 0; padding: 14px 18px; font-size: 13px; line-height: 1.6; color: #333; background: #f0fdf4; }
				.epkb-fe-tooltip__buttons { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-top: 1px solid #dcfce7; background: #fff; border-radius: 0 0 4px 4px; }
				.epkb-fe-tooltip__skip { color: #666; font-size: 12px; text-decoration: none; }
				.epkb-fe-tooltip__skip:hover { color: #15803d; text-decoration: underline; }
				.epkb-fe-tooltip__next { background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); border: none; color: #fff; padding: 10px 20px; font-size: 13px; font-weight: 600; border-radius: 5px; cursor: pointer; box-shadow: 0 2px 8px rgba(34, 197, 94, 0.4); transition: all 0.2s ease; }
				.epkb-fe-tooltip__next:hover { background: linear-gradient(135deg, #16a34a 0%, #166534 100%); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(34, 197, 94, 0.5); }
				.epkb-fe-tooltip-target { outline: 3px solid #22c55e !important; outline-offset: 3px; border-radius: 4px; background: rgba(34, 197, 94, 0.08) !important; }
			' );
		}

		// Get translated texts for this step
		$step_texts = EPKB_Setup_Steps::get_step_texts( $step_key );

		// Build pointers array for sequence
		$pointers = array();
		foreach ( $step['show_me'] as $index => $show_me ) {
			if ( empty( $show_me['target_element'] ) ) {
				continue;
			}

			// Use translated pointer texts if available, otherwise fall back to step title/description
			$pointer_texts = isset( $step_texts['pointers'][ $index ] ) ? $step_texts['pointers'][ $index ] : array();
			$title = ! empty( $pointer_texts['title'] ) ? $pointer_texts['title'] : $step_texts['title'];
			$content = ! empty( $pointer_texts['content'] ) ? $pointer_texts['content'] : $step_texts['description'];

			$target_element = EPKB_Setup_Steps::format_target_value( $show_me['target_element'], $kb_id );

			$open_feature = empty( $show_me['frontend_feature'] ) ? '' : sanitize_key( $show_me['frontend_feature'] );
			$open_section = empty( $show_me['frontend_section'] ) ? '' : sanitize_key( $show_me['frontend_section'] );

			$pointer_data = array(
				'target'  => $target_element,
				'title'   => $title,
				'content' => $content,
			);

			if ( ! empty( $open_feature ) ) {
				$pointer_data['open_feature'] = $open_feature;
			}

			if ( ! empty( $open_section ) ) {
				$pointer_data['open_section'] = $open_section;
			}

			$pointers[] = $pointer_data;
		}

		if ( empty( $pointers ) ) {
			return;
		}

		// Add inline script to show pointer sequence
		$script = "
			jQuery( document ).ready( function( $ ) {
				var epkbPointers = " . wp_json_encode( $pointers ) . ";
				var isFrontendPointer = " . ( $is_frontend ? 'true' : 'false' ) . ";
				var currentPointerIndex = 0;
				var activePointer = null;
				var retryCount = 0;
				var maxRetries = isFrontendPointer ? 20 : 10;
				var frontendEditorOpened = false;

				var prepareFrontendContext = function( pointerConfig ) {
					if ( ! isFrontendPointer ) {
						return;
					}

					if ( pointerConfig.open_feature ) {
						var \$featureButton = $( '.epkb-fe__feature-select-button[data-feature=\"' + pointerConfig.open_feature + '\"]' );
						if ( \$featureButton.length ) {
							\$featureButton.trigger( 'click' );
						}
					}

					if ( pointerConfig.open_section ) {
						var \$section = $( '.epkb-fe__settings-section[data-target=\"' + pointerConfig.open_section + '\"]' );
						if ( \$section.length ) {
							var \$header = \$section.find( '.epkb-fe__settings-section-header' );
							if ( \$header.length && ! \$section.hasClass( 'epkb-fe__is_opened' ) ) {
								\$header.trigger( 'click' );
							}
						}
					}
				};

				var openFrontendEditor = function() {
					if ( ! isFrontendPointer ) {
						return true;
					}

					var \$editor = $( '#epkb-fe__editor' );
					if ( ! \$editor.length ) {
						return true;
					}

					// If already visible, we're ready
					if ( \$editor.is( ':visible' ) ) {
						frontendEditorOpened = true;
						return true;
					}

					// If we haven't triggered the open yet, do it now
					if ( ! frontendEditorOpened ) {
						var \$toggle = $( '.epkb-fe__toggle' );
						if ( \$toggle.length ) {
							\$toggle.trigger( 'click' );
						} else {
							\$editor.show();
						}
						frontendEditorOpened = true;
					}

					// Panel is opening but not visible yet - need to wait
					return false;
				};

				var showPointerSequence = function() {
					if ( currentPointerIndex >= epkbPointers.length ) {
						cleanupUrl();
						return;
					}

					var pointerConfig = epkbPointers[ currentPointerIndex ];

					// Wait for Frontend Editor to be fully visible before showing pointer
					if ( ! openFrontendEditor() ) {
						retryCount++;
						if ( retryCount < maxRetries ) {
							setTimeout( showPointerSequence, 500 );
							return;
						}
					}

					prepareFrontendContext( pointerConfig );
					var \$target = findTarget( pointerConfig.target );

					if ( ! \$target || ! \$target.length || ( isFrontendPointer && ! \$target.is( ':visible' ) ) ) {
						// Retry for async-loaded elements (Gutenberg)
						retryCount++;
						if ( retryCount < maxRetries ) {
							setTimeout( showPointerSequence, 500 );
							return;
						}
						// Skip to next pointer after max retries
						retryCount = 0;
						currentPointerIndex++;
						setTimeout( showPointerSequence, 100 );
						return;
					}

					retryCount = 0;
					showPointer( \$target, pointerConfig );
				};

				var findTarget = function( selector ) {
					// Handle comma-separated selectors (find first match)
					var selectors = selector.split( ',' );
					for ( var i = 0; i < selectors.length; i++ ) {
						var sel = selectors[i].trim();
						var \$el = $( sel );
						if ( \$el.length ) {
							// For visibility check, allow elements that exist but may not be visible yet
							var \$visible = \$el.filter( ':visible' );
							if ( \$visible.length ) {
								return \$visible.first();
							}
							// Return first element even if not visible (might become visible)
							return \$el.first();
						}
					}
					return null;
				};

				var showPointer = function( \$target, config ) {
					var isLastPointer = ( currentPointerIndex === epkbPointers.length - 1 );
					var stepText = '(' + ( currentPointerIndex + 1 ) + '/' + epkbPointers.length + ')';
					var buttonText = isLastPointer ? '" . esc_js( __( 'Got it!', 'echo-knowledge-base' ) ) . "' : '" . esc_js( __( 'Next', 'echo-knowledge-base' ) ) . "';

					// For frontend pointers inside Frontend Editor, use custom tooltip instead of wp-pointer
					if ( isFrontendPointer && \$target.closest( '#epkb-fe__editor' ).length ) {
						showFrontendEditorTooltip( \$target, config, buttonText, isLastPointer );
						return;
					}

					var content = '<h3>' + config.title + ' <small style=\"font-weight:normal;opacity:0.7;\">' + stepText + '</small></h3>';
					content += '<p>' + config.content + '</p>';

					// Determine if this is an admin menu item
					var isAdminMenuItem = \$target.closest( '#adminmenu' ).length > 0;
					var pointerEdge = isAdminMenuItem ? 'left' : 'top';
					var pointerAlign = 'center';

					// For admin menu items, expand the submenu to ensure visibility
					if ( isAdminMenuItem ) {
						var \$menuItem = \$target.closest( 'li.menu-top' );
						if ( \$menuItem.length && ! \$menuItem.hasClass( 'wp-has-current-submenu' ) ) {
							\$menuItem.addClass( 'opensub' ).find( '.wp-submenu' ).show();
						}
					}

					// Scroll element into view
					if ( \$target.offset() && ! isAdminMenuItem ) {
						$( 'html, body' ).animate( {
							scrollTop: Math.max( 0, \$target.offset().top - 150 )
						}, 300 );
					}

					// Close any existing pointer
					if ( activePointer ) {
						activePointer.pointer( 'close' );
					}

					// Initialize pointer with Next/Got it button
					\$target.pointer( {
						content: content,
						position: {
							edge: pointerEdge,
							align: pointerAlign
						},
						pointerClass: 'epkb-setup-pointer',
						buttons: function( event, t ) {
							var \$buttons = $( '<div class=\"epkb-pointer-buttons\"></div>' );

							// Skip button (except on last)
							if ( ! isLastPointer ) {
								var \$skipBtn = $( '<a href=\"#\" class=\"epkb-pointer-skip\">" . esc_js( __( 'Skip tour', 'echo-knowledge-base' ) ) . "</a>' );
								\$skipBtn.on( 'click', function( e ) {
									e.preventDefault();
									t.element.pointer( 'close' );
									cleanupUrl();
								} );
								\$buttons.append( \$skipBtn );
							}

							// Next/Got it button
							var \$nextBtn = $( '<button type=\"button\" class=\"button button-primary epkb-pointer-next\">' + buttonText + '</button>' );
							\$nextBtn.on( 'click', function() {
								t.element.pointer( 'close' );
								currentPointerIndex++;
								if ( currentPointerIndex < epkbPointers.length ) {
									setTimeout( showPointerSequence, 300 );
								} else {
									cleanupUrl();
								}
							} );
							\$buttons.append( \$nextBtn );

							return \$buttons;
						}
					} ).pointer( 'open' );

					activePointer = \$target;
				};

				// Custom tooltip for elements inside Frontend Editor (avoids z-index issues)
				var showFrontendEditorTooltip = function( \$target, config, buttonText, isLastPointer ) {
					var stepText = '(' + ( currentPointerIndex + 1 ) + '/' + epkbPointers.length + ')';

					// Remove any existing tooltip
					$( '.epkb-fe-tooltip' ).remove();

					// Scroll target into view within the editor
					var \$editor = $( '#epkb-fe__editor' );
					var targetTop = \$target.position().top;
					\$editor.animate( { scrollTop: \$editor.scrollTop() + targetTop - 100 }, 300 );

					// Create tooltip HTML
					var tooltipHtml = '<div class=\"epkb-fe-tooltip\">' +
						'<div class=\"epkb-fe-tooltip__arrow\"></div>' +
						'<div class=\"epkb-fe-tooltip__content\">' +
							'<h3>' + config.title + ' <small>' + stepText + '</small></h3>' +
							'<p>' + config.content + '</p>' +
							'<div class=\"epkb-fe-tooltip__buttons\">' +
								( ! isLastPointer ? '<a href=\"#\" class=\"epkb-fe-tooltip__skip\">" . esc_js( __( 'Skip tour', 'echo-knowledge-base' ) ) . "</a>' : '' ) +
								'<button type=\"button\" class=\"epkb-fe-tooltip__next\">' + buttonText + '</button>' +
							'</div>' +
						'</div>' +
					'</div>';

					var \$tooltip = $( tooltipHtml );

					// Insert tooltip after target and position it
					\$target.after( \$tooltip );

					// Add highlight to target
					\$target.addClass( 'epkb-fe-tooltip-target' );

					// Handle button clicks
					\$tooltip.find( '.epkb-fe-tooltip__skip' ).on( 'click', function( e ) {
						e.preventDefault();
						\$tooltip.remove();
						\$target.removeClass( 'epkb-fe-tooltip-target' );
						cleanupUrl();
					} );

					\$tooltip.find( '.epkb-fe-tooltip__next' ).on( 'click', function() {
						\$tooltip.remove();
						\$target.removeClass( 'epkb-fe-tooltip-target' );
						currentPointerIndex++;
						if ( currentPointerIndex < epkbPointers.length ) {
							setTimeout( showPointerSequence, 300 );
						} else {
							cleanupUrl();
						}
					} );

					activePointer = { pointer: function( action ) {
						if ( action === 'close' ) {
							\$tooltip.remove();
							\$target.removeClass( 'epkb-fe-tooltip-target' );
						}
					} };
				};

				var cleanupUrl = function() {
					var url = new URL( window.location.href );
					url.searchParams.delete( '" . esc_js( self::POINTER_PARAM ) . "' );
					window.history.replaceState( {}, '', url.toString() );
				};

				// Start pointer sequence after page load - longer delay for Gutenberg
				setTimeout( showPointerSequence, 1500 );
			} );
		";

		wp_add_inline_script( 'wp-pointer', $script );
	}

	/**
	 * Get pointer data for a specific step
	 */
	public static function get_pointer_data( $step_key ) {

		$steps = EPKB_Setup_Steps::get_all_steps();
		if ( ! isset( $steps[ $step_key ] ) ) {
			return null;
		}

		$step = $steps[ $step_key ];

		return array(
			'title'          => $step['title'],
			'description'    => $step['description'],
			'target_element' => isset( $step['show_me'][0]['target_element'] ) ? $step['show_me'][0]['target_element'] : '',
			'position'       => array(
				'edge'  => 'top',
				'align' => 'center',
			),
		);
	}
}

<?php

namespace Imagely\NGG\Admin\Notifications;

use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\URL;

class Manager {

	public $_notifications    = [];
	public $_displayed_notice = false;
	public $_dismiss_url      = null;

	/**
	 * @var Manager
	 */
	static $_instance = null;

	/**
	 * @return Manager
	 */
	static function get_instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new Manager();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->_dismiss_url = site_url( '/?ngg_dismiss_notice=1' );
	}

	public function has_displayed_notice() {
		return $this->_displayed_notice;
	}

	public function add( $name, $handler ) {
		$this->_notifications[ $name ] = $handler;
	}

	public function remove( $name ) {
		unset( $this->_notifications[ $name ] );
	}

	public function render() {
		$output = [];

		foreach ( array_keys( $this->_notifications ) as $notice ) {
			if ( ( $html = $this->render_notice( $notice ) ) ) {
				$output[] = $html;
			}
		}

		echo implode( "\n", $output );
	}

	public function is_dismissed( $name ) {
		$retval = false;

		$settings  = Settings::get_instance();
		$dismissed = $settings->get( 'dismissed_notifications', [] );

		if ( isset( $dismissed[ $name ] ) ) {
			if ( ( $id = get_current_user_id() ) ) {
				if ( in_array( $id, $dismissed[ $name ] ) ) {
					$retval = true;
				} elseif ( in_array( 'unknown', $dismissed[ $name ] ) ) {
					$retval = true;
				}
			}
		}

		return $retval;
	}

	public function dismiss( $name, $dismiss_code = 1 ) {
		$response = [];

		if ( ( $handler = $this->get_handler_instance( $name ) ) ) {
			$has_method = method_exists( $handler, 'is_dismissable' );
			if ( ( $has_method && $handler->is_dismissable() ) || ! $has_method ) {
				if ( method_exists( $handler, 'dismiss' ) ) {
					$response            = $handler->dismiss( $dismiss_code );
					$response['handled'] = true;
				}

				if ( is_bool( $response ) ) {
					$response = [ 'dismiss' => $response ];
				}

				// Set default key/values
				if ( ! isset( $response['handled'] ) ) {
					$response['handled'] = false;
				}
				if ( ! isset( $response['dismiss'] ) ) {
					$response['dismiss'] = true;
				}
				if ( ! isset( $response['persist'] ) ) {
					$response['persist'] = $response['dismiss'];
				}
				if ( ! isset( $response['success'] ) ) {
					$response['success'] = $response['dismiss'];
				}
				if ( ! isset( $response['code'] ) ) {
					$response['code'] = $dismiss_code;
				}

				if ( $response['dismiss'] ) {
					$settings  = Settings::get_instance();
					$dismissed = $settings->get( 'dismissed_notifications', [] );
					if ( ! isset( $dismissed[ $name ] ) ) {
						$dismissed[ $name ] = [];
					}
					$user_id              = get_current_user_id();
					$dismissed[ $name ][] = ( $user_id ? $user_id : 'unknown' );
					$settings->set( 'dismissed_notifications', $dismissed );

					if ( $response['persist'] ) {
						$settings->save();
					}
				}
			} else {
				$response['error'] = __( 'Notice is not dismissible', 'nggallery' );
			}
		} else {
			$response['error'] = __( 'No handler defined for this notice', 'nggallery' );
		}

		return $response;
	}

	public function get_handler_instance( $name ) {
		$retval = null;

		if ( isset( $this->_notifications[ $name ] ) ) {
			$handler = $this->_notifications[ $name ];

			if ( is_object( $handler ) ) {
				$retval = $handler;
			} elseif ( is_array( $handler ) ) {
				$retval = new Wrapper( $name, $handler );
			} elseif ( class_exists( $handler ) ) {
				$retval = call_user_func( [ $handler, 'get_instance' ], $name );
			}
		}

		return $retval;
	}

	public function enqueue_scripts() {
		if ( $this->has_displayed_notice() ) {
			$router = \Imagely\NGG\Util\Router::get_instance();
			wp_enqueue_script(
				'ngg_admin_notices',
				$router->get_static_url( 'photocrati-nextgen_admin#admin_notices.js' ),
				[],
				NGG_SCRIPT_VERSION,
				true
			);
			wp_localize_script(
				'ngg_admin_notices',
				'ngg_notification_dismiss_settings',
				[
					'url'   => $this->_dismiss_url,
					'nonce' => \wp_create_nonce( 'ngg_dismiss_notification' ),
				]
			);
		}
	}

	public function serve_ajax_request() {
		if ( isset( $_REQUEST['ngg_dismiss_notice'] ) ) {
			$retval = [ 'failure' => true ];
			if ( ! headers_sent() ) {
				header( 'Content-Type: application/json' );
			}

			ob_start();

			if ( ! isset( $_REQUEST['nonce'] ) || ! \wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'ngg_dismiss_notification' ) ) {
				$retval['msg'] = __( 'Invalid security token', 'nggallery' );
			} else {

				if ( ! isset( $_REQUEST['code'] ) ) {
					$_REQUEST['code'] = 1;
				}

				if ( isset( $_REQUEST['name'] ) ) {
					$retval = $this->dismiss(
						sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ),
						intval(
							sanitize_text_field( wp_unslash( $_REQUEST['code'] ) )
						)
					);
				} else {
					$retval['msg'] = __( 'Not a valid notice name', 'nggallery' );
				}
			}

			ob_end_clean();

			echo json_encode( $retval );

			exit();
		}
	}

	public function render_notice( $name ) {
		$retval = '';

		if ( ( $handler = $this->get_handler_instance( $name ) ) && ! $this->is_dismissed( $name ) ) {
			// Does the handler want to render?
			$has_method = method_exists( $handler, 'is_renderable' );
			if ( ( $has_method && $handler->is_renderable() ) || ! $has_method ) {
				$show_dismiss_button = false;
				if ( method_exists( $handler, 'show_dismiss_button' ) ) {
					$show_dismiss_button = $handler->show_dismiss_button();
				} elseif ( method_exists( $handler, 'is_dismissable' ) ) {
					$show_dismiss_button = $handler->is_dismissable();
				}

				$template = method_exists( $handler, 'get_mvc_template' )
					? $handler->get_mvc_template()
					: 'photocrati-nextgen_admin#admin_notice';

				// The 'inline' class is necessary to prevent our notices from being moved in the DOM
				// see https://core.trac.wordpress.org/ticket/34570 for reference
				$css_class  = 'inline ';
				$css_class .= ( method_exists( $handler, 'get_css_class' ) ? $handler->get_css_class() : 'updated' );

				$view = new \C_MVC_View(
					$template,
					[
						'css_class'           => $css_class,
						'is_dismissable'      => ( method_exists( $handler, 'is_dismissable' ) ? $handler->is_dismissable() : false ),
						'html'                => ( method_exists( $handler, 'render' ) ? $handler->render() : '' ),
						'show_dismiss_button' => $show_dismiss_button,
						'notice_name'         => $name,
					]
				);

				$retval = $view->render( true );

				if ( method_exists( $handler, 'enqueue_backend_resources' ) ) {
					$handler->enqueue_backend_resources();
				}

				$this->_displayed_notice = true;
			}
		}

		return $retval;
	}
}

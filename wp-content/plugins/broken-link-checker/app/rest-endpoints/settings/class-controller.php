<?php
/**
 * Rest endpoint for saving/getting settings.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Rest_Endpoints\Settings
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Rest_Endpoints\Settings;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use WPMUDEV_BLC\Core\Controllers\Rest_Api;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Rest_Endpoints\Settings\Includes\Schema;
use WPMUDEV_BLC\App\Emails\Recipient_Activation\Controller as RecipientMailer;
use WPMUDEV_BLC\App\Webhooks\Recipient_Activation\Controller as Activation_Webhook;
use WPMUDEV_BLC\App\Scheduled_Events\Scan\Controller as Scehduled_Scan;
use WPMUDEV_BLC\Core\Utils\Utilities;

// Dashboard Trait.
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

use function array_map;
use function is_wp_error;
use function sanitize_key;


/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Dashboard
 */
class Controller extends Rest_Api {
	/**
	 * Use Dashboard_API Traits.
	 *
	 * @since 2.0.0
	 */
	use Dashboard_API;

	/**
	 * Settings keys.
	 *
	 * @var array
	 */
	protected $email_collection = array();

	public function init() {
		Settings::instance()->init();

		$this->settings_keys = array_map(
			function ( $settings_key ) {
				return sanitize_key( $settings_key );
			},
			array_keys( Settings::instance()->default )
		);

		$this->namespace = "wpmudev_blc/{$this->version}";
		$this->rest_base = 'settings';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_settings_permissions' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( $this, 'save_settings_permissions' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Updates settings.
	 *
	 * @param WP_REST_Request $request WP_REST_Request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed WP_REST_Response|WP_Error|WP_HTTP_Response|mixed $response
	 */
	public function save_settings( WP_REST_Request $request ) {
		$this->request_action = $request->get_param( 'action' );
		$saved_response       = null;
		/**
		 * An array of instructions.
		 */
		$instruction_set = array();

		switch ( $this->request_action ) {
			case 'save':
				if ( $request->get_param( 'saveScheduleEventOnly' ) ) {
					$settings       = $this->set_schedule_settings( $this->filter_settings_keys( $request->get_params() ) );
					$saved_response = Scehduled_Scan::instance()->set_scan_schedule( $settings );

					if ( ! $saved_response ) {
						$saved_response = new WP_Error( 'E_SCHEDULE_CRON_NOT_CREATED', __( 'Schedule event not created. Please refresh page and try once more', 'broken-link-checker' ) );
					}
				} else {
					$saved_response = $this->save_schedule_settings( $request );
					$this->send_confirmation_links();
				}

				//Activation_Webhook::instance()->reset_rule();
				Activation_Webhook::instance()->flush_rewrite_rules( true );
				break;
			case 'save-version-highlights-option' :
				Settings::instance()->set(
					array(
						'version_highlights' => array(
							'2_2_0' => true,
						),
					)
				);

				Settings::instance()->save();
				break;
			case 'save-legacy-modal-option':
				$saved_response = $this->save_legacy_modal_settings( $request );
				break;
			case 'set-scan-status':
				$scan_status = sanitize_text_field( $request->get_param( 'scan_status' ) );
				if ( in_array( $scan_status, array( 'none', 'in_progress', 'completed' ) ) ) {
					Settings::instance()->set( array( 'scan_status' => $scan_status ) );
					$saved_response = Settings::instance()->save();
				}
				break;
			case 'enable-dash-plugin':
				Settings::instance()->set( array( 'v2_activation_request' => true ) );
				Settings::instance()->save();

				$dash_plugin_file_path = 'wpmudev-updates/update-notifications.php';

				// Check if Dash plugin is installed.
				if ( Utilities::dash_plugin_installed() ) {
					// Let's check if it has been activated in the meantime.
					if ( Utilities::dash_plugin_active() ) {
						$response_message = __( 'Dashboard plugin is already active. Please wait while refreshing page.', 'broken-link-checker' );
						$instruction_set  = array(
							array(
								'key'   => 'show_notice',
								'value' => array(
									'content'     => $response_message,
									'notice_type' => 'success',
									// general, informative, success, warning, error, upsell.
								),
							),
							array(
								'key'   => 'reload',
								'value' => true
							),
						);

					} else {
						// Activate Dash plugin.
						if ( ! is_wp_error( activate_plugin( $dash_plugin_file_path ) ) ) {
							$response_message = __( 'Dashboard plugin has been activated.', 'broken-link-checker' );

							if ( (bool) self::site_connected() ) {
								$instruction_set = array(
									array(
										'key'   => 'show_notice',
										'value' => array(
											'content'     => $response_message,
											'notice_type' => 'success',
											// general, informative, success, warning, error, upsell.
										),
									),
									array(
										'key'   => 'reload',
										'value' => true
									),
								);

							} else {
								$instruction_set = array(
									array(
										'key'   => 'show_notice',
										'value' => array(
											'content'     => __( 'Dashboard plugin has been activated.  You should be redirected to sign-in page shortly', 'broken-link-checker' ),
											'notice_type' => 'success',
											// general, informative, success, warning, error, upsell.
										),
									),
									array(
										'key'   => 'redirect',
										'value' => add_query_arg( array(
											'page' => 'wpmudev',
										), is_multisite() ? network_admin_url() : get_admin_url() ),
									),
								);
							}

						} else {
							// Dash plugin could not be activated.
							$instruction_set = array(
								array(
									'key'   => 'show_notice',
									'value' => array(
										'content'     => __( 'Dashboard plugin could not be activated. You can reload page and try again or contact support.', 'broken-link-checker' ),
										'notice_type' => 'error',
										// general, informative, success, warning, error, upsell.
									),
								),
							);

						}
					}

				} else {
					// Dash plugin is not installed.
					$instruction_set = array(
						array(
							'key'   => 'show_notice',
							'value' => array(
								'content'     => sprintf(
									//translators: 1: The Hub's signup url.
									__( 'Dashboard plugin is not installed. You can connect your site for free directly from <a href="%s">Hub<a>.', 'broken-link-checker' ),
									esc_html( Utilities::hub_signup_url() )
								),
								'notice_type' => 'warning', // general, informative, success, warning, error, upsell.
							),
						),
					);

				}

				break;
		}

		$response_data = array(
			'message'     => __( 'Settings saved', 'broken-link-checker' ),
			'status_code' => 200,
		);

		if ( is_wp_error( $saved_response ) ) {
			$response_data = array(
				'message'     => $saved_response->get_error_message(),
				'status_code' => 500,
			);
		}

		// We can send some instructions over. Eg to reload page.
		if ( ! empty( $instruction_set ) ) {
			$response_data['instructions'] = $instruction_set;
		}

		$response = $this->prepare_item_for_response( $response_data, $request );

		do_action(
			'wpmudev_blc_rest_enpoints_after_save_settings',
			$response,
			$request,
			$this
		);

		return apply_filters(
			'wpmudev_blc_rest_enpoints_saved_settings_response',
			rest_ensure_response( $response ),
			$response,
			$request,
			$this
		);

	}

	/**
	 * Sets schedule settings.
	 *
	 * @param $settings
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function set_schedule_settings( $settings ) {
		$existing_settings = Settings::instance()->get();

		// We only need to store user ids in options for user recipients. The rest can be generated when preparing
		// data for view.
		$settings['schedule']['recipients'] = wp_list_pluck( $settings['schedule']['recipients'], 'id' );
		$existing_recipients                = array();

		/*
		 * We need a token/key to use for unsubscribe link.
		 *
		 * TODO Create a unified way to manage both type or recipients, registered users and recipients by email.
		 */
		if ( ! empty( $settings['schedule']['recipients'] ) ) {
			foreach ( $settings['schedule']['recipients'] as $recipient_user_id ) {
				if ( ! isset( $settings['schedule']['registered_recipients_data'][ $recipient_user_id ] ) ) {
					$user = get_userdata( $recipient_user_id );

					if ( $user instanceof \WP_User ) {
						$length = 32;
						$key    = 'registered-recipient-' . bin2hex( random_bytes( $length ) );

						$settings['schedule']['registered_recipients_data'][ $recipient_user_id ] = array(
							'name'  => $user->display_name,
							'email' => $user->user_email,
							'key'   => "{$key}|{$recipient_user_id}",
						);
					}
				}
			}
		}

		if ( ! empty( $existing_settings['schedule']['emailrecipients'] ) ) {
			$existing_recipients = wp_list_pluck( $existing_settings['schedule']['emailrecipients'], 'email' );
		}

		$settings['schedule']['emailRecipients'] = array_map(
			function ( $recipient ) use ( $existing_recipients ) {
				/*
				There is no need to store avatars as those can change at any given time.
				Removing avatars from email recipients. We can fetch the avatars when preparing recipient data for view.
				*/
				unset( $recipient['avatar'] );

				/*
				 * Generate activation code and send activation links to recipients if not already.
				 */
				if ( isset( $recipient['key'] ) && isset( $recipient['email'] ) && ! in_array(
						$recipient['email'],
						$existing_recipients
					) ) {
					/**
					 * To send confirmation email to email recipient we need to add him in a collection/list.
					 */
					$name = $recipient['name'] ?? '';
					// $length          = 32;
					// $activation_code = bin2hex( random_bytes( $length ) );
					$activation_code = base64_encode( md5( $recipient['email'] ) . '_' . $recipient['key'] );

					$this->email_collection[] = array(
						'name'              => $name,
						'email'             => filter_var( $recipient['email'], FILTER_SANITIZE_EMAIL ),
						'activation_code'   => $activation_code,
						'activation_link'   => $this->generate_activation_link( $activation_code ),
						'cancellation_link' => $this->generate_cancellation_link( $activation_code ),
					);

				}

				return $recipient;
			},
			$settings['schedule']['emailRecipients']
		);

		return $settings;
	}

	/**
	 * Returns recipient activation url with activation code.
	 *
	 * @param string $activation_code
	 *
	 * @return mixed
	 */
	private function generate_activation_link( string $activation_code = '' ) {
		return add_query_arg(
			array(
				'action'          => 'activate',
				'activation_code' => sanitize_text_field( $activation_code ),
			),
			Activation_Webhook::instance()->webhook_url()
		);
	}

	private function generate_cancellation_link( string $activation_code = '' ) {
		return add_query_arg(
			array(
				'action'          => 'cancel',
				'activation_code' => sanitize_text_field( $activation_code ),
			),
			Activation_Webhook::instance()->webhook_url()
		);
	}

	/**
	 * Accepts a given array of settings and returns only settings with specific array keys.
	 *
	 * @param array Settings array.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function filter_settings_keys( $params = array() ) {
		return array_filter(
			$params,
			function ( $value, $key ) {
				return in_array( $key, $this->settings_keys );
			},
			ARRAY_FILTER_USE_BOTH
		);
	}

	/**
	 * Store schedule options in db.
	 *
	 * @param WP_REST_Request $request WP_REST_Request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed bool|WP_Error
	 */
	protected function save_schedule_settings( WP_REST_Request $request ) {
		$schedule_status = $request->get_param( 'schedule_status' );

		if ( ! $schedule_status ) {
			$existing_settings                       = Settings::instance()->get();
			$default_settings                        = Settings::instance()->default;
			$existing_settings['schedule']           = $default_settings['schedule'] ? $default_settings['schedule'] : $existing_settings['schedule'];
			$existing_settings['schedule']['active'] = false;

			\WPMUDEV_BLC\App\Scheduled_Events\Scan\Controller::instance()->deactivate_cron();

			return Settings::instance()->save( $existing_settings );
		}

		$settings = $this->filter_settings_keys( $request->get_params() );

		if ( empty( $settings['schedule']['frequency'] ) ) {
			return new WP_Error( 'E_SCHEDULE_NOT_SAVED', __( 'Schedule frequency was not set', 'broken-link-checker'
			) );
		}

		if (
			( 'weekly' === $settings['schedule']['frequency'] && empty( $settings['schedule']['days'] ) ) ||
			( 'monthly' === $settings['schedule']['frequency'] && empty( $settings['schedule']['monthdays'] ) )
		) {
			return new WP_Error( 'E_SCHEDULE_NOT_SAVED', __( 'Schedule days were not set', 'broken-link-checker' ) );
		}

		$settings                       = $this->set_schedule_settings( $settings );
		$settings['schedule']['active'] = true;

		Settings::instance()->set( $settings );

		Settings::instance()->save();

		do_action(
			'wpmudev_blc_rest_enpoints_after_save_schedule_settings',
			$settings,
			$request,
			$this
		);

		/**
		 * Condition to confirm that Cron Schedule was created for scan.
		 */
		/*
		$saved_response = Scehduled_Scan::instance()->set_scan_schedule( $settings );

		if ( ! $saved_response ) {
			$saved_response = new \WP_Error( 'E_SCHEDULE_CRON_NOT_CREATED', __( 'Schedule event not created. Please refresh page and try once more', 'broken-link-checker' ) );
		}

		if ( ! is_wp_error( $saved_response ) ) {
			$created_schedule_timestamp = wp_next_scheduled( Scehduled_Scan::instance()->cron_hook_name );
			$settings_timestamp = Scehduled_Scan::instance()->get_timestamp( $settings['schedule'] ?? array() );

		}

		return $saved_response;
		*/

		return true;
	}

	/**
	 * Sends the confirmation emails to the email recipients (not to the registered users).
	 *
	 * @return void
	 */
	private function send_confirmation_links() {
		RecipientMailer::instance()->clear_email_pool();

		if ( ! empty( $this->email_collection ) ) {
			RecipientMailer::instance()->send_multiple( $this->email_collection, true );
		}
	}

	/**
	 * Store legacy modal options in db.
	 *
	 * @param WP_REST_Request $request WP_REST_Request get data from request.
	 *
	 * @return mixed bool|WP_Error
	 */
	protected function save_legacy_modal_settings( WP_REST_Request $request ) {
		$legacy_flag                        = boolval( $request->get_param( 'use_legacy_blc_version' ) );
		$settings                           = Settings::instance()->get();
		$settings['use_legacy_blc_version'] = $legacy_flag;
		$settings['v2_activation_request']  = ! $legacy_flag;
		$settings['activation_modal_shown'] = true;

		do_action(
			'wpmudev_blc_rest_enpoints_switch_version_mode',
			$legacy_flag,
			$request,
			$this
		);

		return Settings::instance()->save( $settings );
	}

	/**
	 * Check permissions for saving options.
	 *
	 * @param object $request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|object Boolean or WP_Error.
	 */
	public function get_settings_permissions( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'You cannot view settings.', 'broken-link-checker' ),
				array( 'status' => $this->authorization_status_code() )
			);
		}

		return true;
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function authorization_status_code() {
		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	/**
	 * Check permissions for the update
	 *
	 * @param WP_REST_Request $request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|WP_Error
	 */
	public function save_settings_permissions( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'Action forbidden.', 'broken-link-checker' ),
				array( 'status' => $this->authorization_status_code() )
			);
		}

		return true;
	}

	/**
	 * Grabs all the category list.
	 *
	 * @param WP_REST_Request $request get data from request.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed|WP_REST_Response
	 */
	public function get_settings( WP_REST_Request $request ) {
		// Return settings fetched from Settings.
		return rest_ensure_response( Settings::instance()->get() );
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 2.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = Schema::get_schema( $this->request_action, array( 'rest_base' => $this->rest_base ) );

		return $this->add_additional_fields_schema( $this->schema );
	}

}

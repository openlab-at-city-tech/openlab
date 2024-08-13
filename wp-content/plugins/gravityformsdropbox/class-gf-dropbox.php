<?php

defined( 'ABSPATH' ) or die();

use Gravity_Forms\Gravity_Forms_Dropbox\Account;

// Load Feed Add-On Framework.
GFForms::include_feed_addon_framework();

/**
 * Dropbox integration using the Add-On Framework.
 *
 * @see GFFeedAddOn
 */
class GF_Dropbox extends GFFeedAddOn {

	/**
	 * Defines the version of the Dropbox Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_version Contains the version, defined in dropbox.php
	 */
	protected $_version = GF_DROPBOX_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '2.0.8';

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravityformsdropbox';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravityformsdropbox/dropbox.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string
	 */
	protected $_url = 'http://www.gravityforms.com';

	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Gravity Forms Dropbox Add-On';

	/**
	 * Defines the short title of this Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_title The short title of the Add-On.
	 */
	protected $_short_title = 'Dropbox';

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_dropbox';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_dropbox';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_dropbox_uninstall';

	/**
	 * Defines the capabilities to add to roles by the Members plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array $_capabilities Capabilities to add to roles by the Members plugin.
	 */
	protected $_capabilities = array( 'gravityforms_dropbox', 'gravityforms_dropbox_uninstall' );

	/**
	 * Contains an instance of the Dropbox API library, if available.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    GF_Dropbox_API $api If available, contains an instance of the Dropbox API library.
	 */
	protected $api = null;

	/**
	 * Contains a queue of Dropbox feeds that need to be processed on shutdown.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array $feeds_to_process A queue of Dropbox feeds that need to be processed on shutdown.
	 */
	protected $feeds_to_process = array();

	/**
	 * Contains a queue of files to delete after feed processing is complete.
	 *
	 * @since  2.0
	 * @access protected
	 * @var    array $files_to_delete A queue of files to delete after feed processing is complete.
	 */
	protected $files_to_delete = array();

	/**
	 * Defines the nonce action used when processing Dropbox feeds.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $nonce_action Nonce action for processing Dropbox feeds.
	 */
	protected $nonce_action = 'gform_dropbox_upload';

	/**
	 * The notification events which should be triggered once the last feed has been processed.
	 *
	 * @since  1.2.2
	 * @access protected
	 * @var    array $_notification_events The notification events which should be triggered once the last feed has been processed.
	 */
	protected $_notification_events = array();


	/**
	 * The authenticated Dropbox account.
	 *
	 * @var object
	 */
	protected $account;

	/**
	 * Get instance of this class.
	 *
	 * @since  1.0
	 *
	 * @return GF_Dropbox
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	 * Autoload the required libraries.
	 *
	 * @since  1.0
	 */
	public function pre_init() {

		parent::pre_init();

		if ( $this->is_gravityforms_supported() ) {

			// Load Dropbox API class.
			if ( ! class_exists( 'GF_Dropbox_API' ) ) {
				require_once( $this->get_base_path() . '/includes/class-gf-dropbox-api.php' );
			}

			// If custom app is enabled, load Dropbox field class.
			if ( $this->get_plugin_setting( 'customAppEnable' ) ) {
				require_once 'includes/class-gf-field-dropbox.php';
			} else {
				require_once 'includes/class-gf-field-dropbox-incompatible.php';
			}

		}

	}

	/**
	 * Add Dropbox feed processing hooks.
	 *
	 * @since  1.0
	 */
	public function init() {

		parent::init();

		add_filter( 'gform_settings_header_buttons', array( $this, 'filter_gform_settings_header_buttons' ), 99 );

		// Setup feed processing on shutdown.
		add_action( 'shutdown', array( $this, 'maybe_process_feed_on_shutdown' ), 10 );

		// Prevent Dropbox Upload field from appearing if custom Dropbox app is not configured.
		add_filter( 'gform_pre_render', array( $this, 'remove_dropbox_field' ) );
		add_filter( 'gform_pre_validation', array( $this, 'remove_dropbox_field' ) );

		// Attach files to notification
		add_filter( 'gform_pre_send_email', array( $this, 'filter_gform_pre_send_email' ), 10, 4 );

	}

	/**
	 * Add AJAX callback for retrieving folder contents.
	 *
	 * @since  1.0
	 */
	public function init_ajax() {

		parent::init_ajax();

		// Add AJAX callback for retreiving folder contents.
		add_action( 'wp_ajax_gfdropbox_folder_contents', array( $this, 'ajax_get_folder_contents' ) );

		// Add AJAX callback for de-authorizing with Dropbox.
		add_action( 'wp_ajax_gfdropbox_deauthorize', array( $this, 'ajax_deauthorize' ) );

		// Add AJAX callback for removing the access token so that the user can re-authorize.
		add_action( 'wp_ajax_gfdropbox_remove_access_token', array( $this, 'ajax_remove_access_token' ) );

		// Add AJAX callback for checking app key/secret validity.
		add_action( 'wp_ajax_gfdropbox_valid_app_key_secret', array( $this, 'ajax_is_valid_app_key_secret' ) );

		// Process feeds upon admin POST request.
		add_action( 'wp_ajax_nopriv_' . $this->nonce_action, array( $this, 'maybe_process_feed_on_post_request' ) );

	}

	/**
	 * Add required hooks.
	 *
	 * @since  2.0
	 */
	public function init_admin() {

		parent::init_admin();

		add_action( 'admin_init', array( $this, 'start_session' ) );
		add_action( 'admin_init', array( $this, 'maybe_update_auth_tokens' ) );

		add_action( 'gform_field_standard_settings', array( $this, 'add_field_settings' ), 10, 2 );
		add_action( 'gform_editor_js', array( $this, 'add_field_settings_js' ) );

	}

	/**
	 * Requirements needed to use Dropbox Add-On.
	 *
	 * @since  2.0
	 *
	 * @return array
	 */
	public function minimum_requirements() {

		return array( 'php' => array( 'version' => '5.5' ) );

	}

	/**
	 * Start a new session on the plugin settings page.
	 *
	 * @since  1.0
	 */
	public function start_session() {

		if ( $this->is_plugin_settings( $this->_slug ) ) {
			session_start();
		}

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'  => 'gform_dropbox_jstree',
				'deps'    => array( 'jquery' ),
				'src'     => $this->get_base_url() . '/js/vendor/jstree.min.js',
				'version' => '3.0.4',
			),
			array(
				'handle'  => 'gform_dropbox_formeditor',
				'deps'    => array( 'jquery', 'gform_dropbox_jstree' ),
				'src'     => $this->get_base_url() . "/js/form_editor{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings', 'form_editor' ),
						'tab'        => $this->_slug,
					),
				),
				'strings' => array(
					'nonce_folder' => wp_create_nonce( 'gfdropbox_folder_contents' ),
				),
			),
			array(
				'handle'  => 'gform_dropbox_pluginsettings',
				'deps'    => array( 'jquery' ),
				'src'     => $this->get_base_url() . "/js/plugin_settings{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'plugin_settings' ),
						'tab'        => $this->_slug,
					),
				),
				'strings' => array(
					'nonce_deauthorize' => wp_create_nonce( 'gfdropbox_deauthorize' ),
					'nonce_validation'  => wp_create_nonce( 'gfdropbox_valid_app_key_secret' ),
					'settings_url'      => admin_url( 'admin.php?page=gf_settings&subview=' . $this->_slug ),
				),
			),
			array(
				'handle'  => 'gform_dropbox_frontend',
				'deps'    => array( 'jquery' ),
				'src'     => $this->get_base_url() . "/js/frontend{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'field_types' => array( 'dropbox' )
					),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );

	}

	/**
	 * Enqueue folder tree styling.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function styles() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(
			array(
				'handle'  => 'gform_dropbox_jstree',
				'src'     => $this->get_base_url() . '/css/jstree/style.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => $this->_slug,
					),
				),
			),
			array(
				'handle'  => 'gform_dropbox_admin',
				'src'     => $this->get_base_url() . "/css/admin{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'plugin_settings', 'form_editor' ),
						'tab'        => $this->_slug,
					),
				),
			),
			array(
				'handle'  => 'gform_dropbox_frontend',
				'src'     => $this->get_base_url() . "/css/frontend{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'field_types' => array( 'dropbox' )
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @since 2.6
	 *
	 * @return string
	 */
	public function get_menu_icon() {

		return file_get_contents( $this->get_base_path() . '/images/menu-icon.svg' );

	}





	// # PLUGIN SETTINGS -----------------------------------------------------------------------------------------------

	/**
	 * Maybe update the Dropbox authentication tokens.
	 *
	 * @since 2.9
	 */
	public function maybe_update_auth_tokens() {
		if ( rgget( 'subview' ) !== $this->get_slug() ) {
			return;
		}

		$payload = $this->get_oauth_payload();

		// If access token is provided, save it.
		if ( ! empty( $payload['access_token'] ) ) {

			// If state does not match, do not save.
			if ( rgpost( 'state' ) && ! wp_verify_nonce( rgar( $payload, 'state' ), $this->get_authentication_state_action() ) ) {

				// Add error message.
				GFCommon::add_error_message( esc_html__( 'Unable to connect to Dropbox due to mismatched state.', 'gravityformsdropbox' ) );

				return parent::plugin_settings_page();

			}

			$previous_settings = $this->get_plugin_settings();
			$settings          = array_merge(
				is_array( $previous_settings ) ? $previous_settings : array(),
				array(
					'accessToken'          => rgar( $payload, 'access_token' ),
					'refresh_token'        => rgar( $payload, 'refresh_token' ),
					'access_token_expires' => $this->get_access_token_expiration_time( $payload ),
				)
			);

			// Save plugin settings.
			$this->update_plugin_settings( $settings );

			// Initialize API.
			if ( $this->initialize_api( $settings ) ) {

				// Get default folder path.
				$site_url    = parse_url( get_option( 'home' ) );
				$folder_path = '/' . rgar( $site_url, 'host' );

				try {

					// Check for default folder.
					$folder_metadata = $this->api->get_metadata( $folder_path );

				} catch ( Exception $e ) {

					try {

						// Create folder.
						$folder = $this->api->create_folder( $folder_path );

					} catch ( Exception $e ) {

						// Log that default folder could not be created.
						$this->log_error( __METHOD__ . '(): Unable to create default folder (' . $folder_path . '); ' . $e->getMessage() );

					}

				}

			}

		}

		// If authorization state and code are provided, attempt to create an access token.
		if ( rgget( 'code' ) ) {

			// Get current plugin settings.
			$settings = $this->get_plugin_settings();

			// Initialize Dropbox API.
			$dropbox = new GF_Dropbox_API( array(), $settings['customAppKey'], $settings['customAppSecret'] );

			try {

				// Get access token.
				$access_token = $dropbox->get_access_token( $_GET['code'], $this->get_redirect_uri() );

				// Add access token to plugin settings.
				$settings['accessToken']   = $access_token->access_token;
				$settings['refresh_token'] = $access_token->refresh_token;

				// Save plugin settings.
				$this->update_plugin_settings( $settings );

			} catch ( Exception $e ) {

				// Add error message.
				GFCommon::add_error_message( esc_html__( 'Unable to connect to Dropbox.', 'gravityformsdropbox' ) . ' ' . esc_html( $e->getMessage() ) );

			}

		}

		// If error is provided, display message.
		if ( rgpost( 'auth_error' ) ) {

			// Add error message.
			GFCommon::add_error_message( esc_html__( 'Unable to connect to Dropbox.', 'gravityformsdropbox' ) );

		}
	}

	/**
	 * Get the authorization payload data.
	 *
	 * Returns the auth POST request if it's present, otherwise attempts to return a recent transient cache.
	 *
	 * @since 2.9
	 *
	 * @return array
	 */
	private function get_oauth_payload() {
		// First try to retrieve the posted payload.
		$posted_data = array(
			'auth_payload' => rgpost( 'auth_payload' ),
			'auth_error'   => rgpost( 'auth_error' ),
			'state'        => rgpost( 'auth_state' ),
		);

		$posted_payload = $this->get_decoded_auth_payload( $posted_data );

		if ( isset( $posted_payload['access_token'], $posted_payload['state'] ) || ! empty( $posted_payload['auth_error'] ) ) {
			delete_transient( "gravityapi_response_{$this->_slug}" );
			return $posted_payload;
		}

		// Fallback to getting the transient payload, on the chance the user was logged out during the request.
		$transient_payload = get_transient( "gravityapi_response_{$this->_slug}" );

		delete_transient( "gravityapi_response_{$this->_slug}" );

		if (
			rgar( $transient_payload, 'state' ) !== get_transient( "gravityapi_request_{$this->_slug}" )
			|| ! is_array( $transient_payload )
		) {
			return array();
		}

		return $this->get_decoded_auth_payload( $transient_payload );
	}

	/**
	 * This method normalizes the payload that was received from the Gravity API into a structure required by the
	 * add-on.
	 *
	 * @param array $payload_data The payload data received from the Gravity API, either via an active $_POST
	 *                            or a recent transient.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	private function get_decoded_auth_payload( $payload_data ) {
		$parsed = json_decode( rgar( $payload_data, 'auth_payload' ), JSON_OBJECT_AS_ARRAY );

		return array_filter(
			array(
				'access_token'  => rgar( $parsed, 'access_token' ),
				'refresh_token' => rgar( $parsed, 'refresh_token' ),
				'expires_in'    => rgar( $parsed, 'expires_in' ),
				'auth_error'    => rgar( $payload_data, 'auth_error' ),
				'state'         => rgar( $payload_data, 'state' ),
			)
		);
	}

	/**
	 * Setup plugin settings fields.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		// Prepare base fields.
		$fields = array(
			array(
				'name'  => 'accessToken',
				'type'  => 'hidden',
			),
			array(
				'name'  => 'customAppEnable',
				'type'  => 'hidden',
			),
			array(
				'name'  => null,
				'label' => null,
				'type'  => 'auth_token_button',
			),
		);

		// If API is initialized, add custom app key/secret fields.
		if ( $this->initialize_api() ) {
			$fields[] = array( 'name' => 'customAppKey', 'type' => 'hidden' );
			$fields[] = array( 'name' => 'customAppSecret', 'type' => 'hidden' );
		}

		// Setup base fields.
		return array( array( 'fields' => $fields ) );

	}

	/**
	 * Hide submit button on plugin settings page.
	 *
	 * @since 2.6
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	public function filter_gform_settings_header_buttons( $html = '' ) {

		// If this is not the plugin settings page, return.
		if ( ! $this->is_plugin_settings( $this->get_slug() ) ) {
			return $html;
		}

		// Do not display button if already initialized.
		if ( $this->initialize_api() ) {
			return '';
		}

		if ( ! $this->initialize_api() && $this->get_setting( 'customAppEnable' ) !== '1' ) {
			return str_replace( '<button', '<button style="display:none;"', $html );
		}

		return $html;

	}

	/**
	 * Create Generate Auth Token settings field.
	 *
	 * @since  2.0
	 *
	 * @param array $field Field settings.
	 * @param bool  $echo  Display field. Defaults to true.
	 *
	 * @return string
	 */
	public function settings_auth_token_button( $field, $echo = true ) {

		// Initialize return HTML.
		$html = '<div class="gform-settings-description kitchen-sink gform-dropbox-settings-description">';

		// Get plugin settings.
		$settings = $this->get_plugin_settings();

		// If Dropbox is authenticated, display de-authorize button.
		if ( $this->initialize_api() ) {

			try {

				// Get account information.
				$account = $this->api->get_current_account();

				$is_account_disabled = method_exists( $account, 'getCode' ) && $account->getCode() === 400;

				if ( $is_account_disabled ) {
					$html .= $this->get_error_indicator();
				} else {

					$html .= '<p>';
					$html .= sprintf(
					// Translators: 1. Link to forms list page, 2. Closing </a> tag.
						esc_html__( 'To configure Dropbox with your form(s), choose a form you wish to use Dropbox with from the %sforms list page%s, and select Dropbox from the form settings menu.', 'gravityformsdropbox' ),
						'<a href="?page=gf_edit_forms">',
						'</a>'
					);
					$html .= '</p>';
					$html .= '<p><strong>' . esc_html__( 'Dropbox Account Status' ) . '</strong></p>';
					$html .= $this->get_status_indicator( $account );

					$html .= sprintf(
						' <a href="#" class="button primary" id="gform_dropbox_deauth_button">%1$s</a>',
						esc_html__( 'Disconnect from Dropbox', 'gravityformsdropbox' )
					);
				}

			} catch ( Exception $e ) {

				// Log that we could not get account information.
				$this->log_error( __METHOD__ . '(): Unable to get account information; ' . $e->getMessage() );

				// Display error message.
				$html .= sprintf( '<p>%s</p>', esc_html__( 'Unable to get account information.', 'gravityformsdropbox' ) );

			}

		} else {

			if ( '1' == rgar( $settings, 'customAppEnable' ) ) {

				// If SSL is available, display custom app settings.
				if ( is_ssl() ) {
					$html .= $this->custom_app_settings();
				} else {
					$html .= sprintf( '<p>%s</p>', esc_html__( 'To use a custom Dropbox app, you must have an SSL certificate installed and enabled. Visit this page after configuring your SSL certificate to use a custom Dropbox app.', 'gravityformsdropbox' ) );
				}

				$html .= sprintf(
					'<p>%s</p>',
					sprintf( esc_html__( '%sI do not want to use a custom Dropbox app.%s', 'gravityformsdropbox' ), '<a href="#" id="gform_dropbox_disable_customApp">', '</a>' )
				);

			} else {

				// Prepare authorization URL.
				$license_key  = GFCommon::get_key();
				$settings_url = urlencode( admin_url( 'admin.php?page=gf_settings&subview=' . $this->_slug ) );
				$nonce        = wp_create_nonce( $this->get_authentication_state_action() );
				$auth_url     = add_query_arg( array(
					'redirect_to' => $settings_url,
					'license'     => $license_key,
					'version'     => $this->_version,
					'state'       => $nonce,
				), $this->get_gravity_api_url( '/auth/dropbox' ) );

				if ( get_transient( "gravityapi_request_{$this->_slug}" ) ) {
					delete_transient( "gravityapi_request_{$this->_slug}" );
				}

				set_transient( "gravityapi_request_{$this->_slug}", $nonce, 10 * MINUTE_IN_SECONDS );

				$html .= '<p>' . esc_html__( 'The Dropbox Add-On allows you to quickly integrate Gravity Forms with Dropbox, one of the industry leaders in file storage solutions in the cloud.  Allow users to upload files from their Dropbox account or send files uploaded via Gravity Forms to your own Dropbox account.', 'gravityformsdropbox' ) . '</p>';
				$html .= '<p>';
				$html .= sprintf(
					// Translators: 1. Link to Dropbox, 2. Closing </a> tag.
					esc_html__( 'If you don\'t have a Dropbox account, you can %ssign up for one here%s.', 'gravityformsdropbox' ),
					'<a href="https://www.dropbox.com/" target="_blank">',
					'</a>'
				);
				$html .= '</p>';

				$html .= sprintf(
					'<p>%s</p>',
					sprintf( esc_html__( '%sI want to use a custom Dropbox app.%s (Recommended for advanced users only.)', 'gravityformsdropbox' ), '<a href="#" id="gform_dropbox_enable_customApp">', '</a>' )
				);

				$html .= sprintf(
					'<a href="%2$s" class="button primary" id="gform_dropbox_auth_button">%1$s</a>',
					esc_html__( 'Connect to Dropbox', 'gravityformsdropbox' ),
					$auth_url
				);

			}

		}

		$html .= '</div>';

		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	/**
	 * Get the correct status indicator markup for the version of Core.
	 *
	 * @since 3.1.2
	 *
	 * @param $account The account being evaluated.
	 *
	 * @return string
	 */
	private function get_status_indicator( $account ) {

		// Indicator styles were updated in 2.8.8
		if ( $this->is_gravityforms_supported( '2.8.8' ) ) {
			$html = '<p><span class="gform-status-indicator gform-status-indicator--size-sm gform-status-indicator--theme-cosmos gform-status--active gform-status--no-icon gform-status--no-hover">';
			$html .= '<span class="gform-status-indicator-status gform-typography--weight-medium gform-typography--size-text-xs">';

			if ( isset( $account->name ) ) {
				$html .= esc_html__( 'Connected to Dropbox as: ', 'gravityformsdropbox' );
				$html .= esc_html( $account->name->display_name );
			} else {
				$html .= esc_html__( 'Connected to Dropbox.', 'gravityformsdropbox' );
			}

			$html .= '</span></span></p>';

			return $html;
		}

		$html = '<p><span class="gform-status-indicator gform-status--static gform-status--active">';
		if ( $this->is_gravityforms_supported( '2.5' ) ) {
			$html .= '<svg viewBox="0 0 6 6" xmlns="http://www.w3.org/2000/svg"><circle cx="3" cy="2" r="1" stroke-width="2"/></svg>';
		}
		if ( isset( $account->name ) ) {
			$html .= esc_html__( 'Connected to Dropbox as: ', 'gravityformsdropbox' );
			$html .= esc_html( $account->name->display_name ) . '</span></p>';
		} else {
			$html .= esc_html__( 'Connected to Dropbox.', 'gravityformsdropbox' ) . '</span></p>';
		}

		return $html;
	}

	/**
	 * Get the correct error indicator markup for the version of Core.
	 *
	 * @since 3.1.2
	 *
	 * @return string
	 */
	private function get_error_indicator() {

		// Indicator styles were updated in 2.8.8
		if ( $this->is_gravityforms_supported( '2.8.8' ) ) {
			$html = '<p><span class="gform-status-indicator gform-status-indicator--size-sm gform-status-indicator--theme-cosmos gform-status--error gform-status--no-icon gform-status--no-hover">';
			$html .= '<span class="gform-status-indicator-status gform-typography--weight-medium gform-typography--size-text-xs">';
			$html .= esc_html__( 'The connected Dropbox app is disabled or has been deleted. Please reconnect.', 'gravityformsdropbox' );
			$html .= '</span></span></p>';

			$html .= sprintf(
				' <a href="#" class="button primary" id="gform_dropbox_reconnect_button">%1$s</a>',
				esc_html__( 'Reconnect to Dropbox', 'gravityformsdropbox' )
			);

			return $html;
		}

		$html = '<p> <span class="gform-status-indicator gform-status--static gform-status--error">' . esc_html__( 'The connected Dropbox app is disabled or has been deleted. Please reconnect.', 'gravityformsdropbox' ) . ' </span></p>';
		$html = sprintf(
			' <a href="#" class="button primary" id="gform_dropbox_reconnect_button">%1$s</a>',
			esc_html__( 'Reconnect to Dropbox', 'gravityformsdropbox' )
		);

		return $html;
	}

	/**
	 * Renders settings section for custom Dropbox app.
	 *
	 * @since  2.0
	 *
	 * @return string
	 */
	public function custom_app_settings() {
		$html = '';

		// Get plugin settings.
		$settings = $this->get_plugin_settings();

		// Get valid app key/secret state.
		$valid_app_key_secret = $this->is_valid_app_key_secret();

		// Open custom app table.
		if ( version_compare( GFForms::$version, '2.5-dev-1', '<' ) ) {
			$html = '<table class="form-table">';
		}

		ob_start();

		// Display redirect URI.
		$this->single_setting_row(
			array(
				'name'     => 'oauthRedirectURI',
				'type'     => 'text',
				'label'    => esc_html__( 'OAuth Redirect URI', 'gravityformsdropbox' ),
				'class'    => 'large',
				'value'    => $this->get_redirect_uri(),
				'readonly' => true,
				'onclick'  => 'this.select();',
			)
		);

		// Display custom app key.
		$this->single_setting_row(
			array(
				'name'    => 'customAppKey',
				'type'    => 'text',
				'label'   => esc_html__( 'App Key', 'gravityformsdropbox' ),
				'class'   => 'medium',
				'onclick' => 'jQuery(this).focus();', // This bypasses our unsaved changes handler, and can be removed later if we update that handler.
			)
		);

		// Display custom app secret.
		$this->single_setting_row(
			array(
				'name'    => 'customAppSecret',
				'type'    => 'text',
				'label'   => esc_html__( 'App Secret', 'gravityformsdropbox' ),
				'class'   => 'medium',
				'onclick' => 'jQuery(this).focus();', // This bypasses our unsaved changes handler, and can be removed later if we update that handler.
			)
		);

		$html .= ob_get_contents();
		ob_end_clean();

		if ( version_compare( GFForms::$version, '2.5-dev-1', '<' ) ) {
			$html .= '<tr><td></td><td>';
		}

		// Display auth button.
		$html .= sprintf(
			'<a href="%3$s" class="button" id="gform_dropbox_auth_button" style="%2$s">%1$s</a>',
			esc_html__( 'Click here to connect to Dropbox.', 'gravityformsdropbox' ),
			! rgar( $settings, 'customAppEnable' ) || ( rgar( $settings, 'customAppEnable' ) && ! $valid_app_key_secret ) ? 'display:none' : null,
			rgar( $settings, 'customAppEnable' ) && $valid_app_key_secret ? $this->get_auth_url() : '#'
		);

		// Close table.
		if ( version_compare( GFForms::$version, '2.5-dev-1', '<' ) ) {
			$html .= '</td></tr></table>';
		}

		return $html;

	}

	/**
	 * Removes access token and custom app settings to deauthorize.
	 *
	 * @since 3.2
	 *
	 * @param array $settings Existing settings.
	 *
	 * @return void
	 */
	public function remove_access_token( $settings = null ) {

		if ( ! $settings ) {
			$settings = $this->get_plugin_settings();
		}

		// Remove access token from settings to deauthorize.
		unset( $settings['accessToken'] );
		unset( $settings['access_token_expires'] );
		unset( $settings['refresh_token'] );

		// Removing custom app settings if they exist.
		unset( $settings['customAppKey'] );
		unset( $settings['customAppSecret'] );

		// Save settings.
		$this->update_plugin_settings( $settings );
	}

	/**
	 * Deauthorize with Dropbox.
	 *
	 * @since  2.0
	 */
	public function ajax_deauthorize() {

		// Verify nonce.
		if ( false === wp_verify_nonce( rgget( 'nonce' ), 'gfdropbox_deauthorize' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityformsdropbox' ) ) );
		}

		// If user is not authorized, exit.
		if ( ! GFCommon::current_user_can_any( $this->_capabilities_settings_page ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityformsdropbox' ) ) );
		}

		// If API cannot be initialized, exit.
		if ( ! $this->initialize_api() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unable to connect to Dropbox.', 'gravityformsdropbox' ) ) );
		}

		// Get plugin settings.
		$settings = $this->get_plugin_settings();

		try {

			// Revoke access token.
			$revoke = $this->api->revoke_token();

			// Log that we revoked the access token.
			$this->log_debug( __METHOD__ . '(): Access token revoked.' );

			$this->remove_access_token( $settings );

			// Return success response.
			wp_send_json_success();

		} catch ( \Exception $e ) {

			// Log that we could not revoke the access token.
			$this->log_debug( __METHOD__ . '(): Unable to revoke access token; ' . $e->getMessage() );

			if ( $e->getCode() === 401 ) {

				$this->remove_access_token( $settings );

				// Return success response.
				wp_send_json_success();

			} else {

				// Return error response.
				wp_send_json_error(
					array(
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
					)
				);

			}
		}
	}

	/**
	 * Handles the AJAX request to remove the access token .
	 *
	 * @since  3.2
	 */
	public function ajax_remove_access_token() {

		// Verify nonce.
		if ( false === wp_verify_nonce( rgget( 'nonce' ), 'gfdropbox_deauthorize' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityformsdropbox' ) ) );
		}

		// If user is not authorized, exit.
		if ( ! GFCommon::current_user_can_any( $this->_capabilities_settings_page ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityformsdropbox' ) ) );
		}

		// Remove access token.
		$this->remove_access_token();

		// Return success response.
		wp_send_json_success();
	}





	// # FEED SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Setup fields for feed settings.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function feed_settings_fields() {

		return array(
			array(
				'title'  => esc_html__( 'Dropbox Feed Settings', 'gravityformsdropbox' ),
				'fields' => array(
					array(
						'name'          => 'feedName',
						'type'          => 'text',
						'class'         => 'medium',
						'required'      => true,
						'label'         => esc_html__( 'Name', 'gravityformsdropbox' ),
						'save_callback' => array( $this, 'sanitize_settings_value' ),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Name', 'gravityformsdropbox' ),
							esc_html__( 'Enter a feed name to uniquely identify this setup.', 'gravityformsdropbox' )
						),
					),
					array(
						'name'          => 'fileUploadField',
						'type'          => 'select',
						'required'      => true,
						'label'         => esc_html__( 'File Upload Field', 'gravityformsdropbox' ),
						'choices'       => $this->get_file_upload_field_choices(),
						'save_callback' => array( $this, 'sanitize_settings_value' ),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'File Upload Field', 'gravityformsdropbox' ),
							esc_html__( 'Select the specific File Upload field that you want to be uploaded to Dropbox.', 'gravityformsdropbox' )
						),
					),
					array(
						'name'          => 'destinationFolder',
						'type'          => 'folder',
						'required'      => true,
						'label'         => esc_html__( 'Destination Folder', 'gravityformsdropbox' ),
						'save_callback' => array( $this, 'sanitize_settings_value' ),
						'description'   => esc_html__( 'Select the folder in your Dropbox account where the files will be uploaded to.  By default, all files are stored in the "Gravity Forms Add-On" folder within the Dropbox Apps folder in your Dropbox account.', 'gravityformsdropbox' ),
					),
					array(
						'name'           => 'feedCondition',
						'type'           => 'feed_condition',
						'label'          => esc_html__( 'Upload Condition', 'gravityformsdropbox' ),
						'checkbox_label' => esc_html__( 'Enable Condition', 'gravityformsdropbox' ),
						'instructions'   => esc_html__( 'Upload to Dropbox if', 'gravityformsdropbox' ),
					),
				),
			),
		);

	}

	/**
	 * Sanitize feed settings field value on save.
	 *
	 * @since  2.0.6
	 *
	 * @param array  $field       Settings field properties.
	 * @param string $field_value Field value.
	 *
	 * @return string
	 */
	public function sanitize_settings_value( $field, $field_value ) {

		return sanitize_text_field( $field_value );

	}

	/**
	 * Create folder tree settings field.
	 *
	 * @since  1.0
	 *
	 * @param array $field Field settings.
	 * @param bool  $echo  Display field. Defaults to true.
	 *
	 * @return string
	 */
	public function settings_folder( $field, $echo = true ) {

		$attributes    = $this->get_field_attributes( $field );
		$site_url      = parse_url( get_option( 'home' ) );
		$default_value = '/' . rgar( $site_url, 'host' );
		$value         = $this->get_setting( $field['name'], $default_value );
		$name          = esc_attr( $field['name'] );

		$hidden_field         = $field;
		$hidden_field['type'] = 'hidden';
		unset( $hidden_field['callback'] );

		$html = $this->settings_hidden( $hidden_field, false );

		if ( rgar( $field, 'description' ) ) {
			$html .= '<p>' . esc_html( $field['description'] ) . '</p>';
		}

		$html .= sprintf(
			'<div data-target="%1$s" class="folder_tree"></div>',
			( version_compare( GFForms::$version, '2.5-dev-1', '<' ) ? '_gaddon_setting_' : '_gform_setting_' ) . $name
		);

		if ( $this->field_failed_validation( $field ) ) {
			$html .= $this->get_error_icon( $field );
		}

		// Prepare options.
		$options = array( 'initialPath' => esc_js( $value ) );

		// Initialize destination folder script.
		$html .= sprintf(
			'<script type="text/javascript">new GFDropboxFolder(%1$s);</script>',
			wp_json_encode( $options )
		);

		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	/**
	 * Set if feeds can be created.
	 *
	 * @since  1.0
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		return $this->initialize_api();

	}

	/**
	 * Enable feed duplication.
	 *
	 * @since  1.0
	 *
	 * @param string $id Feed ID requesting duplication.
	 *
	 * @return bool
	 */
	public function can_duplicate_feed( $id ) {

		return true;

	}

	/**
	 * Setup columns for feed list table.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName'          => esc_html__( 'Name', 'gravityformsdropbox' ),
			'destinationFolder' => esc_html__( 'Destination Folder', 'gravityformsdropbox' ),
		);

	}

	/**
	 * Notify user form requires file upload fields if not present.
	 *
	 * @since  1.0
	 *
	 * @return string|bool
	 */
	public function feed_list_message() {

		// If form does not have a file upload field, return error message.
		if ( ! $this->has_file_upload_fields() ) {
			return $this->requires_file_upload_message();
		}

		return parent::feed_list_message();

	}

	/**
	 * Link user to form editor to add file upload fields.
	 *
	 * @since  1.0
	 *
	 * @return string
	 */
	public function requires_file_upload_message() {

		// Generate form editor URL.
		$url = add_query_arg( array( 'view' => null, 'subview' => null ) );

		return sprintf(
			esc_html__( "You must add a File Upload field to your form before creating a feed. Let's go %sadd one%s!", 'gravityformsdropbox' ),
			'<a href="' . esc_url( $url ) . '">', '</a>'
		);

	}

	/**
	 * Get file upload fields for feed setting.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function get_file_upload_field_choices() {

		// Initialize choices.
		$choices = array(
			array(
				'label' => esc_html__( 'All File Upload Fields', 'gravityformsdropbox' ),
				'value' => 'all',
			),
		);

		// Get file upload fields for form.
		$fields = $this->has_file_upload_fields();

		// Add fields to choices.
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$choices[] = array(
					'label' => RGFormsModel::get_label( $field ),
					'value' => $field->id,
				);
			}
		}

		return $choices;

	}

	/**
	 * Get file upload fields for form.
	 *
	 * @since  1.0
	 *
	 * @param int $form_id Form ID. Defaults to null.
	 *
	 * @return array
	 */
	public function has_file_upload_fields( $form_id = null ) {

		// Get form.
		$form = rgblank( $form_id ) ? $this->get_current_form() : GFAPI::get_form( $form_id );

		// Get file upload fields for form.
		return GFAPI::get_fields_by_type( $form, array( 'fileupload', 'dropbox' ), true );

	}

	/**
	 * Get folder tree for feed settings field.
	 *
	 * @since  1.0
	 *
	 * @param string $path       Dropbox folder path.
	 * @param bool   $first_load If this is the first load of tree path. Defaults to false.
	 *
	 * @return array
	 */
	public function get_folder_tree( $path, $first_load = false ) {

		// If the Dropbox instance isn't initialized, return an empty folder array.
		if ( ! $this->initialize_api() ) {

			// Log that API is not initialized.
			$this->log_error( __METHOD__ . '(): Unable to get folder tree because API is not initialized.' );

			return array();

		}

		// Get base folder.
		$base_folder = $this->get_folder( $path );

		// If this is the first load of the tree, we need to get all the parent items.
		if ( 'true' === $first_load  ) {

			// Initialize folders array with base folder.
			$folders = array(
				array(
					'id'            => strtolower( $base_folder['id'] ),
					'text'          => $base_folder['text'],
					'parent'        => ( '/' === $base_folder['id'] ) ? '#' : strtolower( dirname( $base_folder['id'] ) ),
					'children'      => $base_folder['children'],
					'child_folders' => $base_folder['child_folders'],
					'state'         => array(
						'selected' => true,
						'opened'   => true,
					),
				),
			);

			// Set the current path.
			$current_path = $base_folder['id'];

			// Go up the path until we reach the root folder.
			while ( '/' !== $current_path ) {

				$current_path = dirname( $current_path );
				$folder       = $this->get_folder( $current_path );

				if ( rgar( $folder, 'children' ) ) {
					foreach ( $folder['child_folders'] as $index => $child ) {
						if ( $child['id'] !== $folders[0]['id'] ) {
							unset( $child['child_folders'] );
							$folders[] = $child;
						}
					}
				}

				$folders[] = array(
					'id'     => $folder['id'],
					'text'   => $folder['text'],
					'parent' => ( '/' === $folder['id'] ) ? '#' : $folder['parent'],
				);

			}

			// Make sure only unique items are in the array.
			$folders = $this->unique_folder_tree( $folders );

		} else {

			// Get child folders.
			$folders = rgar( $base_folder, 'children' ) ? $base_folder['child_folders'] : array();

			// Loop through child folders; if folder does not have children, set children parameter to empty string.
			foreach ( $folders as &$folder ) {
				if ( ! rgar( $folder, 'children' ) ) {
					$folder['children'] = '';
				}
			}

		}

		// Sort folder tree in alphabetical order.
		usort( $folders, array( $this, 'sort_folder_tree' ) );

		return $folders;

	}

	/**
	 * Ensure folder tree does not contain any duplicate folders.
	 *
	 * @since  1.0
	 *
	 * @param array $tree Dropbox folders.
	 *
	 * @return array $tree
	 */
	public function unique_folder_tree( $tree ) {

		for ( $i = 0; $i < count( $tree ); $i ++ ) {

			$duplicate = null;

			for ( $ii = $i + 1; $ii < count( $tree ); $ii ++ ) {
				if ( strcmp( $tree[ $ii ]['id'], $tree[ $i ]['id'] ) === 0 ) {
					$duplicate = $ii;
					break;
				}
			}

			if ( ! is_null( $duplicate ) ) {
				array_splice( $tree, $duplicate, 1 );
			}

		}

		return $tree;

	}

	/**
	 * Sort folder tree in alphabetical order.
	 *
	 * @since  1.0
	 *
	 * @param array $a First item.
	 * @param array $b Second item.
	 *
	 * @return int
	 */
	public function sort_folder_tree( $a, $b ) {

		return strcmp( $a['text'], $b['text'] );

	}

	/**
	 * Get Dropbox folder tree for AJAX requests.
	 *
	 * @since  1.0
	 */
	public function ajax_get_folder_contents() {

		// Verify nonce.
		if ( false === wp_verify_nonce( rgget( 'nonce' ), 'gfdropbox_folder_contents' ) ) {
			wp_send_json( array() );
		}

		// If user is not authorized, exit.
		if ( ! GFCommon::current_user_can_any( $this->_capabilities_settings_page ) ) {
			wp_send_json( array() );
		}

		$path = '#' === rgget( 'path' ) ? '/' : rgget( 'path' );

		wp_send_json( $this->get_folder_tree( $path, rgget( 'first_load' ) ) );

	}





	// # FORM DISPLAY --------------------------------------------------------------------------------------------------

	/**
	 * Remove Dropbox Upload field from form if custom app is not configured or Chooser is not available.
	 *
	 * @since  2.0
	 *
	 * @param array $form Form object.
	 *
	 * @return array
	 */
	public function remove_dropbox_field( $form ) {

		// Get Dropbox Upload fields.
		$dropbox_fields = GFAPI::get_fields_by_type( $form, 'dropbox' );

		// If this form does not have any Dropbox Upload fields, return.
		if ( empty( $dropbox_fields ) ) {
			return $form;
		}

		// Check if a custom Dropbox app is enabled.
		$custom_app_enabled = $this->get_plugin_setting( 'customAppEnable' );

		// Check if Dropbox Chooser is available.
		$chooser_available = $this->is_chooser_available();

		// If a custom Dropbox app is enabled and the Chooser is available, return.
		if ( $custom_app_enabled && $chooser_available ) {
			return $form;
		}

		// If Dropbox Chooser is not available, log it.
		if ( ! $chooser_available && $custom_app_enabled ) {
			$this->log_debug( __METHOD__ . '(): Dropbox Chooser field is not available. Chooser domain must be configured in Dropbox app.' );
		}

		// Loop through form fields.
		foreach ( $form['fields'] as $i => $field ) {

			// If this is not a Dropbox Upload field, skip it.
			if ( 'dropbox' !== $field->type ) {
				continue;
			}

			// Remove field.
			unset( $form['fields'][ $i ] );

		}

		return $form;

	}





	// # FORM EDITOR ---------------------------------------------------------------------------------------------------

	/**
	 * Add settings fields for Dropbox field.
	 *
	 * @since  1.0
	 *
	 * @param int $position The position that the settings should be displayed.
	 * @param int $form_id  The ID of the form being edited.
	 */
	public function add_field_settings( $position, $form_id ) {

		if ( 20 !== $position ) {
			return;
		}

		?>

		<li class="link_type_setting field_setting">
			<label class="section_label"><?php esc_html_e( 'Link Type', 'gravityformsdropbox' ); ?></label>
			<div>
				<input type="radio" name="link_type" id="field_link_type_preview" size="10" onclick="SetFieldProperty( 'linkType', 'preview' );" />
				<label for="field_link_type_preview" class="inline"><?php esc_html_e( 'Preview', 'gravityformsdropbox' ); ?></label>
				&nbsp;&nbsp;
				<input type="radio" name="link_type" id="field_link_type_direct" size="10" onclick="SetFieldProperty( 'linkType', 'direct' );" />
				<label for="field_link_type_direct" class="inline"><?php esc_html_e( 'Direct', 'gravityformsdropbox' ); ?></label>
			</div>
		</li>

		<li class="multiselect_setting field_setting">
			<input type="checkbox" id="field_multiselect" onclick="SetFieldProperty( 'multiselect', this.checked );" />
			<label for="field_multiselect" class="inline"><?php esc_html_e( 'Allow multiple files to be selected', 'gravityformsdropbox' ); ?></label>
			<br class="clear" />
		</li>

		<?php

	}

	/**
	 * Add Javascript for Dropbox field settings.
	 *
	 * @since  1.0
	 */
	public function add_field_settings_js() {

		?>

		<script type="text/javascript">
			jQuery( document ).bind( 'gform_load_field_settings', function ( e, field, form ) {
				document.getElementById( 'field_link_type_preview' ).checked = ( 'preview' === field[ 'linkType' ] );
				document.getElementById( 'field_link_type_direct' ).checked = ( 'direct' === field[ 'linkType' ] );
				document.getElementById( 'field_multiselect' ).checked = ( true === field[ 'multiselect' ] );
			} );
		</script>

		<?php

	}





	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Add feed to processing queue.
	 *
	 * @since  1.0
	 *
	 * @param array $feed  Feed object.
	 * @param array $entry Entry object.
	 * @param array $form  Form object.
	 */
	public function process_feed( $feed, $entry, $form ) {

		// If the Dropbox instance isn't initialized, do not process the feed.
		if ( ! $this->initialize_api() ) {

			// Log that we cannot process the feed.
			$this->add_feed_error( esc_html__( 'Feed was not processed because API was not initialized.', 'gravityformsdropbox' ), $feed, $entry, $form );

			return;

		}

		// Set flag for adding feed to processing queue.
		$process_feed = false;

		// Loop through the form fields and work with just the file upload fields.
		foreach ( $form['fields'] as $field ) {

			// Get field input type.
			$input_type = $field->get_input_type();

			// If field is not a file upload field, skip it.
			if ( ! in_array( $input_type, array( 'dropbox', 'fileupload' ) ) ) {
				continue;
			}

			// If feed is not uploading all file upload fields or this specifc field, skip it.
			if ( 'all' !== rgars( $feed, 'meta/fileUploadField' ) && $field->id != rgars( $feed, 'meta/fileUploadField' ) ) {
				continue;
			}

			// If entry value is not empty, flag for processing.
			if ( ! rgempty( $field->id, $entry ) ) {
				$process_feed = true;
			}

		}

		// If this feed is being added to the processing queue, add it and disable form notifications.
		if ( $process_feed ) {

			// Log that we're adding the feed to the queue.
			$this->log_debug( __METHOD__ . '(): Adding feed #' . $feed['id'] . ' to the processing queue.' );

			// Add the feed to the queue.
			$this->feeds_to_process[] = array( $feed['id'], $entry['id'], $form['id'] );

			// Disable notifications.
			add_filter( 'gform_disable_notification_' . $form['id'], array( $this, 'disable_notification' ), 10, 2 );

		}

	}

	/**
	 * Disable the notification and stash the event for processing later.
	 *
	 * @since 1.2.2
	 *
	 * @param bool  $is_disabled  Notification disable status.
	 * @param array $notification Notification object.
	 *
	 * @return bool
	 */
	public function disable_notification( $is_disabled, $notification ) {

		// Get notification event.
		$event = rgar( $notification, 'event' );

		// If notification is not disabled and not in notification events array, add it.
		if ( ! $is_disabled && ! in_array( $event, $this->_notification_events ) ) {
			$this->_notification_events[] = $event;
		}

		return true;

	}

	/**
	 * Process queued feeds on shutdown.
	 *
	 * @since  1.0
	 */
	public function maybe_process_feed_on_shutdown() {

		// If there are no feeds to process, exit.
		if ( empty( $this->feeds_to_process ) ) {
			return;
		}

		// Loop through feeds.
		foreach ( $this->feeds_to_process as $index => $feed_to_process ) {

			// Log that we're sending this feed to processing.
			$this->log_debug( __METHOD__ . '(): Sending processing request for feed #' . $feed_to_process[0] . '.' );

			// Encode feed.
			$feed_to_process = base64_encode( json_encode( $feed_to_process ) );

			// Prepare the request.
			$post_request = array(
				'action'       => $this->nonce_action,
				'data'         => $feed_to_process,
				'hash'         => wp_hash( $feed_to_process, 'nonce' ),
				'is_last_feed' => ( ( count( $this->feeds_to_process ) - 1 ) === $index ),
				'nonce'        => $this->create_nonce(),
			);

			// If this is the last feed to be processed, add notification events to send to the request.
			if ( $post_request['is_last_feed'] ) {
				$post_request['notification_events'] = $this->_notification_events;
			}

			/**
			 * Request timeout length for feed processing request.
			 *
			 * @since 2.0
			 * @param int $timeout The time in seconds before the connection is dropped and an error returned.
			 */
			$timeout = apply_filters( 'gform_dropbox_request_timeout', 0.01 );

			// Send feed processing request.
			$response = wp_remote_post(
				admin_url( 'admin-ajax.php' ),
				array(
					'timeout'   => $timeout,
					'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
					'body'      => $post_request,
				)
			);

			// Log error is feed processing request failed.
			if ( is_wp_error( $response ) ) {
				$this->log_error( __METHOD__ . '(): Aborting. ' . $response->get_error_message() );
			}
		}

	}

	/**
	 * Process queued feed.
	 *
	 * @since  1.0
	 */
	public function maybe_process_feed_on_post_request() {

		// Verify nonce.
		if ( ! $this->verify_nonce( rgpost( 'nonce' ) ) ) {
			$this->log_error( __METHOD__ . '(): Unable to verify nonce; ignoring processing request.' );
			wp_die( 'Unable to verify nonce; ignoring processing request.' );
		}

		// Get request data.
		$data = rgpost( 'data' );

		// Verify hash.
		if ( ! hash_equals( wp_hash( $data, 'nonce' ), rgpost( 'hash' ) ) ) {
			$this->log_error( __METHOD__ . '(): Invalid request data; bailing.' );
			wp_die( 'Invalid request data.' );
		}

		// Get feed, entry and form data.
		$data  = json_decode( base64_decode( $data ), true );
		$feed  = $this->get_feed( (int) $data[0] );
		$entry = GFAPI::get_entry( (int) $data[1] );
		$form  = GFAPI::get_form( (int) $data[2] );

		// Run feed through pre-processor.
		$feeds = $this->pre_process_feeds( array( $feed ), $entry, $form );
		$feed  = $feeds[0];

		// Verify feed is not an array.
		if ( ! is_array( $feed ) ) {
			$this->log_error( __METHOD__ . '(): Provided feed data is not an array; bailing.' );
			wp_die( 'Invalid feed data.' );
		}

		// Log feed we are processing.
		$this->log_debug( __METHOD__ . '(): Processing request for entry #' . $entry['id'] .' , feed #' . $feed['id'] );

		// Process feed.
		$this->process_feed_files( $feed, $entry, $form );

		// Update entry links and send notifications if last feed being processed.
		if ( rgpost( 'is_last_feed' ) ) {

			// Get a fresh entry object to be sure we send out the right file links after feed processing.
			$entry = GFAPI::get_entry( $entry['id'] );

			// Get notification events.
			$notification_events = rgpost( 'notification_events' );

			// If notification events are defined, send notifications for each event.
			if ( is_array( $notification_events ) ) {
				foreach ( $notification_events as $event ) {
					GFAPI::send_notifications( $form, $entry, $event );
				}
			}

		}

		// Delete any local file that are not being stored.
		$this->maybe_delete_files();

		// Run action.
		gf_do_action( array( 'gform_dropbox_post_upload', $form['id'] ), $feed, $entry, $form );

	}

	/**
	 * Process feed.
	 *
	 * @since  1.0
	 *
	 * @param array $feed  Feed object.
	 * @param array $entry Entry object.
	 * @param array $form  Form object.
	 */
	public function process_feed_files( $feed, $entry, $form ) {

		// If the Dropbox instance isn't initialized, do not process the feed.
		if ( ! gf_dropbox()->initialize_api() ) {

			// Log that feed cannot be processed.
			$this->add_feed_error( esc_html__( 'Feed was not processed because API was not initialized.', 'gravityformsdropbox' ), $feed, $entry, $form );

			return;

		}

		// Log that we are processing form fields.
		$this->log_debug( __METHOD__ . '(): Checking form fields for files to process.' );

		// Loop through form fields.
		foreach ( $form['fields'] as $field ) {

			// Get field input type.
			$input_type = $field->get_input_type();

			// If feed is not a file upload field, skip it.
			if ( ! in_array( $input_type, array( 'dropbox', 'fileupload' ) ) ) {
				continue;
			}

			// If feed is not uploading all file upload fields or this specifc field, skip it.
			if ( rgars( $feed, 'meta/fileUploadField' ) !== 'all' && rgars( $feed, 'meta/fileUploadField' ) != $field->id ) {
				continue;
			}

			// Log that we are processing this field.
			$this->log_debug( __METHOD__ . "(): Processing field: {$field->label}(#{$field->id} - {$field->type})." );

			// Call the processing method for input type.
			call_user_func_array( array( $this, 'process_' . $input_type . '_fields' ), array( $field, $feed, $entry, $form ) );

		}

	}

	/**
	 * Passes the folder path through the gform_dropbox_folder_path filter.
	 *
	 * @since 3.2
	 *
	 * @param string     $folder_path The folder in the Dropbox account where the files will be stored.
	 * @param array      $form        Form object.
	 * @param int|string $field_id    The ID of the field currently being processed.
	 * @param array      $entry       Entry object.
	 * @param array      $feed        Feed object.
	 * @param string     $file_url    The URL of the file being processed.
	 *
	 * @return string
	 */
	public function filter_folder_path( $folder_path, $form, $field_id, $entry, $feed, $file_url ) {
		/**
		 * Modify the destination folder configured on the Dropbox feed.
		 *
		 * @since 1.0
		 * @since 3.2 Added the $file_url param.
		 *
		 * @param string     $folder_path The folder in the Dropbox account where the files will be stored.
		 * @param array      $form        Form object.
		 * @param int|string $field_id    The ID of the field currently being processed.
		 * @param array      $entry       Entry object.
		 * @param array      $feed        Feed object.
		 * @param string     $file_url    The URL of the file being processed.
		 */
		return gf_apply_filters( array(
			'gform_dropbox_folder_path',
			rgar( $form, 'id' ),
		), $folder_path, $form, $field_id, $entry, $feed, $file_url );
	}

	/**
	 * Process Dropbox upload fields.
	 *
	 * @since  1.0
	 *
	 * @param GF_Field_Dropbox $field Field object.
	 * @param array            $feed  Feed object.
	 * @param array            $entry Entry object.
	 * @param array            $form  Form object.
	 */
	public function process_dropbox_fields( $field, $feed, $entry, $form ) {

		// Get field value.
		$field_value = rgar( $entry, $field->id );

		// If no files were uploaded, exit.
		if ( rgblank( $field_value ) ) {
			$this->log_debug( __METHOD__ . '(): Aborting; field value is empty.' );

			return;
		}

		// Log beginning of file upload for field.
		$this->log_debug( __METHOD__ . '(): Beginning upload of Dropbox Upload field #' . $field->id . '.' );

		$meta_key     = $this->get_existing_files_meta_key( $field );
		$existing     = gform_get_meta( $entry['id'], $meta_key );
		$files        = json_decode( $field_value, true );
		$update_entry = false;

		// Copy files to Dropbox.
		foreach ( $files as &$file ) {
			if ( ! $this->should_upload_file( $file, $existing, $field, $form, $entry, $feed ) ) {
				$this->log_debug( __METHOD__ . '(): Not uploading file: ' . $file );
				continue;
			}

			// Get destination path.
			$folder_path = $this->filter_folder_path( rgars( $feed, 'meta/destinationFolder' ), $form, $field->id, $entry, $feed, $file );

			// Add starting slash to folder path.
			$folder_path = strpos( $folder_path, '/' ) !== 0 ? '/' . $folder_path : $folder_path;

			// Prepare file name.
			$file_name = basename( $file );
			$file_name = explode( '?dl=', $file_name );
			$file_name = $file_name[0];

			/**
			 * Modify the filename before the file is uploaded to Dropbox.
			 *
			 * @since 1.0
			 * @param string $file_name The file name, including extension.
			 * @param array  $form      Form object.
			 * @param string $field_id  The ID of the field currently being processed.
			 * @param array  $entry     Entry object.
			 * @param array  $feed      Feed object.
			 */
			$file_name = gf_apply_filters( array( 'gform_dropbox_file_name', $form['id'] ), $file_name, $form, $field->id, $entry, $feed );

			try {

				// Save the URL to Dropbox.
				$saved_file = $this->api->save_url( trailingslashit( $folder_path ) . $file_name, $file );

			} catch ( Exception $e ) {

				// Log that file could not be saved to Dropbox.
				$this->add_feed_error( sprintf( esc_html__( 'Unable to upload file: %s', 'gravityformsdropbox' ), $file ), $feed, $entry, $form );
				$this->log_error( __METHOD__ . "(): code: {$e->getCode()}; message: {$e->getMessage()}" );
				continue;

			}

			try {

				// Get shareable link.
				$shareable_link = $this->api->create_shared_link_with_settings( $saved_file->path_display );

				// Set file URL.
				$file = $shareable_link->url;

			} catch ( Exception $e ) {

				// Log that we could not create a public link.
				$this->add_feed_error( sprintf( esc_html__( 'Unable to create shareable link for file: %s', 'gravityformsdropbox' ), $e->getMessage() ), $feed, $entry, $form );
				$this->log_error( __METHOD__ . "(): code: {$e->getCode()}; message: {$e->getMessage()}" );
				continue;

			}

			$update_entry = true;
		}

		if ( ! $update_entry ) {
			return;
		}

		gform_update_meta( $entry['id'], $meta_key, $files, $form['id'] );
		$entry_value = json_encode( $files );
		$this->log_debug( __METHOD__ . '(): Updated field entry value: ' . $entry_value );
		GFAPI::update_entry_field( $entry['id'], $field->id, $entry_value );

	}

	/**
	 * Process file upload fields.
	 *
	 * @since  1.0
	 *
	 * @param GF_Field_FileUpload $field Field object.
	 * @param array               $feed  Feed object.
	 * @param array               $entry Entry object.
	 * @param array               $form  Form object.
	 */
	public function process_fileupload_fields( $field, $feed, $entry, $form ) {

		$field_value = rgar( $entry, $field->id );

		if ( rgblank( $field_value ) ) {
			$this->log_debug( __METHOD__ . '(): Aborting; field value is empty.' );

			return;
		}

		$this->log_debug( __METHOD__ . '(): Beginning upload of file upload field #' . $field->id . '.' );

		$meta_key     = $this->get_existing_files_meta_key( $field );
		$existing     = gform_get_meta( $entry['id'], $meta_key );
		$urls         = $field->multipleFiles ? json_decode( $field_value, true ) : array( $field_value );
		$update_entry = false;

		foreach ( $urls as &$url ) {
			if ( ! $this->should_upload_file( $url, $existing, $field, $form, $entry, $feed ) ) {
				$this->log_debug( __METHOD__ . '(): Not uploading file: ' . $url );
				continue;
			}

			$get_path_args = array( $url );
			if ( $this->is_gravityforms_supported( '2.5.15.2' ) ) {
				$get_path_args[] = $entry['id'];
			}

			$file_info = array(
				'name'        => basename( $url ),
				'path'        => call_user_func_array( 'GFFormsModel::get_physical_file_path', $get_path_args ),
				'url'         => $url,
				'destination' => rgars( $feed, 'meta/destinationFolder' ),
			);

			$this->log_debug( __METHOD__ . '(): File to upload: ' . print_r( $file_info, true ) );
			$new_url = gf_dropbox()->upload_file( $file_info, $form, $field->id, $entry, $feed );

			if ( $new_url === $url ) {
				continue;
			}

			$url          = $new_url;
			$update_entry = true;
		}

		if ( ! $update_entry ) {
			return;
		}

		gform_update_meta( $entry['id'], $meta_key, $urls, $form['id'] );
		$entry_value = $field->multipleFiles ? json_encode( $urls ) : $urls[0];
		$this->log_debug( __METHOD__ . '(): Updated field entry value: ' . $entry_value );
		GFAPI::update_entry_field( $entry['id'], $field->id, $entry_value );

	}

	/**
	 * Returns the entry meta key used to store the array of files previously uploaded to Dropbox.
	 *
	 * @since 3.1
	 *
	 * @param GF_Field $field The current fileupload or dropbox field object.
	 *
	 * @return string
	 */
	public function get_existing_files_meta_key( $field ) {
		return $this->get_slug() . '_field_' . $field->id . '_files';
	}

	/**
	 * Determines if the given file should be uploaded to Dropbox.
	 *
	 * @since 3.1
	 *
	 * @param string      $url      The file URL from the entry.
	 * @param false|array $existing False or an array of files previously uploaded to Dropbox for the current field and entry.
	 * @param GF_Field    $field    The current fileupload or dropbox field object.
	 * @param array       $form     The current form object.
	 * @param array       $entry    The current entry object.
	 * @param array       $feed     The current feed object.
	 *
	 * @return bool
	 */
	public function should_upload_file( $url, $existing, $field, $form, $entry, $feed ) {
		if ( ! empty( $existing ) ) {
			$should_upload_file = ! in_array( $url, $existing );
		} else {
			// The preview link type uses www.dropbox.com.
			$should_upload_file = $field instanceof GF_Field_Dropbox && $field->get_link_type( $form ) === 'preview' || stripos( $url, 'www.dropbox.com' ) === false;
		}

		$filter_args = array( 'gform_dropbox_should_upload_file', $form['id'], $field->id );

		if ( function_exists( 'gf_has_filters' ) && gf_has_filters( $filter_args ) ) {
			$this->log_debug( __METHOD__ . '(): Executing functions hooked to gform_dropbox_should_upload_file.' );
		}

		/**
		 * Provides a way to prevent the uploading of a file to Dropbox.
		 *
		 * @since 3.1
		 *
		 * @param bool        $should_upload_file Indicates if the file should be uploaded to Dropbox.
		 * @param string      $url                The URL of the file from the entry.
		 * @param false|array $existing           False or an array of files previously uploaded to Dropbox for the current field and entry.
		 * @param GF_Field    $field              The current fileupload or dropbox field object.
		 * @param array       $form               The current form object.
		 * @param array       $entry              The current entry object.
		 * @param array       $feed               The current feed object.
		 */
		return (bool) gf_apply_filters( $filter_args, $should_upload_file, $url, $existing, $field, $form, $entry, $feed );
	}

	/**
	 * Upload file to Dropbox.
	 *
	 * @since  1.0
	 *
	 * @param array $file     File to be uploaded.
	 * @param array $form     Form object.
	 * @param int   $field_id Field ID being uploaded.
	 * @param array $entry    Entry object.
	 * @param array $feed     Feed object.
	 *
	 * @return string
	 */
	public function upload_file( $file, $form, $field_id, $entry, $feed ) {

		// If the Dropbox instance isn't initialized, do not upload the file.
		if ( ! $this->initialize_api() ) {

			// Return file URL.
			return rgar( $file, 'url' );

		}

		$folder_path = $this->filter_folder_path( rgar( $file, 'destination' ), $form, $field_id, $entry, $feed, rgar( $file, 'url' ) );

		// If destination folder is not the root folder, ensure the folder exists.
		if ( '/' !== $folder_path ) {

			try {

				// Get folder metadata.
				$destination_folder = $this->api->get_metadata( $folder_path );

				// If destination folder is not a folder, return current file URL.
				if ( 'folder' !== $destination_folder->{'.tag'} ) {

					// Log that folder is not a folder.
					$this->add_feed_error( esc_html__( 'Unable to upload file because destination is not a folder.', 'gravityformsdropboox' ), $feed, $entry, $form );

					// Return file URL.
					return rgar( $file, 'url' );

				}

			} catch ( Exception $e ) {

				// Folder does not exist. Try to create it.
				try {

					// Create folder.
					$destination_folder = $this->api->create_folder( $folder_path );

				} catch ( Exception $e ) {

					// Log that folder could not be created.
					$this->add_feed_error( esc_html__( 'Unable to upload file because destination folder could not be created.', 'gravityformsdropboox' ), $feed, $entry, $form );
					$this->log_error( __METHOD__ . "(): code: {$e->getCode()}; message: {$e->getMessage()}" );

					// Return file URL.
					return rgar( $file, 'url' );

				}

			}

		}

		/**
		 * Modify the filename before the file is uploaded to Dropbox.
		 *
		 * @since 1.0
		 * @param string $file_name The file name, including extension.
		 * @param array  $form      Form object.
		 * @param string $field_id  The ID of the field currently being processed.
		 * @param array  $entry     Entry object.
		 * @param array  $feed      Feed object.
		 */
		$file['name'] = gf_apply_filters( array( 'gform_dropbox_file_name', $form['id'] ), $file['name'], $form, $field_id, $entry, $feed );

		// If file name is empty, set it to the base name of the file path.
		if ( rgblank( $file['name'] ) ) {
			$file['name'] = basename( $file['path'] );
		}

		// Upload file to dropbox.
		try {

			// Upload file.
			$dropbox_file = $this->api->upload( $file['path'], trailingslashit( $folder_path ) . $file['name'], array( 'autorename' => true ) );

			// Log that file was uploaded.
			$this->log_debug( __METHOD__ . '(): File "' . $dropbox_file->name . '" was successfully uploaded.' );

		} catch ( Exception $e ) {

			// Log that file could not be uploaded.
			$this->add_feed_error( sprintf( esc_html__( 'Unable to upload file: %s', 'gravityformsdropbox' ), $e->getMessage() ), $feed, $entry, $form );

			// Return file URL.
			return rgar( $file, 'url' );

		}

		/**
		 * Modify the filename before the file is uploaded to Dropbox.
		 *
		 * @since 1.0
		 * @param bool   $store_local_version Should a local copy of the file be retained? Default is false.
		 * @param string $field_id            The ID of the field currently being processed.
		 * @param array  $form                Form object.
		 * @param array  $entry               Entry object.
		 * @param array  $feed                Feed object.
		 */
		$store_local_version = gf_apply_filters( 'gform_dropbox_store_local_version', array( $form['id'], $field_id ), false, $file, $field_id, $form, $entry, $feed );

		// If local copy should not be saved, set this file for deletion.
		if ( ! $store_local_version && ! in_array( $file['path'], gf_dropbox()->files_to_delete ) ) {

			// Log that we're setting the file to be deleted.
			$this->log_debug( __METHOD__ . '(): Preparing local file for deletion.' );

			// Add to deletion array.
			gf_dropbox()->files_to_delete[] = $file['path'];

		}

		// If we are saving a local copy, remove the file from the deletion array.
		if ( $store_local_version && ( $file_array_key = array_search( $file['path'], gf_dropbox()->files_to_delete ) ) !== false ) {

			// Remove the file from the deletion array.
			unset( gf_dropbox()->files_to_delete[ $file_array_key ] );

		}

		// Create public link.
		try {

			/**
			 * Modify the shareable link settings before the shareable link is generated.
			 * (If "requested_visibility" is set to "password", "link_password" should be defined.)
			 *
			 * @since 2.0.5
			 *
			 * @param array  $shareable_link_settings Sharable link settings.
			 * @param string $field_id                The ID of the field currently being processed.
			 * @param array  $form                    Form object.
			 * @param array  $entry                   Entry object.
			 * @param array  $feed                    Feed object.
			 */
			$shareable_link_settings = gf_apply_filters( array(
				'gform_dropbox_shareable_link_settings',
				$form['id'],
				$field_id,
			), array( 'requested_visibility' => 'public' ), $field_id, $form, $entry, $feed );

			// Execute request.
			$shareable_link = $this->api->create_shared_link_with_settings( $dropbox_file->path_display, $shareable_link_settings );

			// Return shareable file link.
			return $shareable_link->url;

		} catch ( Exception $e ) {

			try {

				// Execute request.
				$shareable_links = $this->api->list_shared_links( $dropbox_file->path_display, null, true );

				// Get existing sharable links.
				$links = $shareable_links->links;

				// If links were found, return first link.
				if ( ! empty( $links ) ) {
					return $links[0]->url;
				} else {
					return rgar( $file, 'url' );
				}

			} catch ( Exception $f ) {

				// Log that we could not create a public link.
				$this->add_feed_error( sprintf( esc_html__( 'Unable to create shareable link for file: %s', 'gravityformsdropbox' ), $f->getMessage() ), $feed, $entry, $form );

				// Return original file url.
				return rgar( $file, 'url' );

			}

		}

	}

	/**
	 * Delete files that do not need a local version.
	 *
	 * @since  1.0
	 */
	public function maybe_delete_files() {

		// If there are files to delete, delete them.
		if ( ! empty( gf_dropbox()->files_to_delete ) ) {

			// Log files being deleted.
			$this->log_debug( __METHOD__ . '(): Deleting local files => ' . print_r( gf_dropbox()->files_to_delete, 1 ) );

			// Delete files.
			array_map( 'unlink', gf_dropbox()->files_to_delete );

		}

	}

	/**
	 * Replace attached Dropbox URLs with path to local file.
	 *
	 * @since 2.5
	 *
	 * @param array  $email          An array containing the email to address, subject, message, headers, attachments and abort email flag.
	 * @param string $message_format The message format: html or text.
	 * @param array  $notification   The current Notification object.
	 * @param array  $entry          The current Entry object.
	 *
	 * @return array
	 */
	public function filter_gform_pre_send_email( $email, $message_format, $notification, $entry ) {

		// If email does not have any attachments, return.
		if ( rgempty( 'attachments', $email ) || ! is_array( $email['attachments'] ) ) {
			return $email;
		}

		// Loop through attachments, set Dropbox files to local files.
		foreach ( $email['attachments'] as $a => $attachment_path ) {

			// If this is not a Dropbox file, skip.
			if ( strpos( $attachment_path, 'dropbox.com' ) === false ) {
				continue;
			}

			// Get file name without the Dropbox query arguments.
			$file_name = basename( strtok( $attachment_path, '?' ) );

			// Get path to local file.
			$local_path = GFFormsModel::get_upload_path( $entry['form_id'] ) . GFCommon::format_date( rgar( $entry, 'date_created' ), false, '/Y/m/', false ) . $file_name;

			// If file exists, attach. Otherwise, remove.
			if ( file_exists( $local_path ) ) {
				$email['attachments'][ $a ] = $local_path;
			} else {
				gf_dropbox()->log_debug( __METHOD__ . '(): Removing attachment from notification as file "' . $local_path . '" does not exist.' );
				unset( $email['attachments'][ $a ] );
			}

		}

		return $email;

	}





	// # HELPER FUNCTIONS ----------------------------------------------------------------------------------------------

	/**
	 * Initializes Dropbox API if credentials are valid.
	 *
	 * @since  1.0
	 *
	 * @param array|null $settings Optional array of plugin settings.
	 *                             In 1.0, this was $access_token, but we've updated this in 3.0 to allow the full
	 *                             set of posted settings to be passed for use by the API.
	 *
	 * @return bool|null
	 */
	public function initialize_api( $settings = null ) {
		// If API object is already setup, return true.
		if ( ! is_null( $this->api ) ) {
			return true;
		}

		$addon_settings = ! is_null( $settings ) ? $settings : $this->get_plugin_settings();

		// If access token is empty, return null.
		if ( rgblank( rgar( $addon_settings, 'accessToken' ) ) ) {
			return null;
		}

		$this->log_debug( __METHOD__ . '(): Testing API credentials.' );

		try {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-account.php';

			// Setup a new Dropbox API object.
			$this->api = new GF_Dropbox_API( $addon_settings, $this->get_app_key(), $this->get_app_secret() );
			$this->maybe_refresh_token( $addon_settings );

			// Attempt to get account info.
			$this->account = new Account( $this->api->get_current_account() );

		} catch ( Exception $e ) {
			$this->log_error( __METHOD__ . '(): API credentials are invalid; ' . $e->getMessage() );
			return false;
		}

		$this->log_debug( __METHOD__ . '(): API credentials are valid.' );
		return true;
	}

	/**
	 * Refreshes the add-on access_token if necessary.
	 *
	 * @since 3.0
	 *
	 * @param array $addon_settings The plugin settings.
	 */
	private function maybe_refresh_token( $addon_settings ) {
		if ( ! $this->is_access_token_expired( $addon_settings, strtotime( 'now' ) ) ) {
			return;
		}

		$response      = $this->api->refresh_access_token( $this, $addon_settings );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_wp_error( $response ) || rgar( $response_body, 'error' ) ) {
			$error_message = ( rgar( $response_body, 'error' ) ) ? rgar( $response_body, 'error' ) : $response->get_error_message();
			$this->log_debug( __METHOD__ . '(): Could not refresh Dropbox access_token. Error: ' . $error_message );
			$this->api = null;
			throw new Exception( $error_message );
		}

		$access_token = rgar( $response_body, 'access_token' );

		if ( empty( $access_token ) || $access_token === rgar( $addon_settings, 'accessToken' ) ) {
			$this->log_debug( __METHOD__ . '(): Could not refresh Dropbox access_token.' );
			$this->api = null;
			return;
		}

		$this->log_debug( __METHOD__ . '(): Updating access token.' );

		$this->update_plugin_settings(
			array_merge(
				$addon_settings,
				array(
					'accessToken'          => $access_token,
					'access_token_expires' => $this->get_access_token_expiration_time( $response_body ),
				)
			)
		);

		$this->api->set_access_token( $access_token );
	}

	/**
	 * Get the expiration time for the access_token.
	 *
	 * @since 3.0
	 *
	 * @param array $token_data Array of data pertaining to the access tokens.
	 *
	 * @return int
	 */
	private function get_access_token_expiration_time( array $token_data ) {
		$expires_time = rgar( $token_data, 'expires_in' );

		if ( ! $expires_time ) {
			return 0;
		}

		// Set an offset for the expires time to force a token refresh a little before it expires.
		$offset = 120;

		if ( $expires_time <= $offset ) {
			return strtotime( 'now' ) + $expires_time;
		}

		return strtotime( 'now' ) + ( $expires_time - $offset );
	}

	/**
	 * Determines whether the current access token has expired and we should request a new one.
	 *
	 * @since 3.0
	 *
	 * @param array $addon_settings  The add-on settings.
	 * @param int   $comparison_time Time to compare against.
	 *
	 * @return bool
	 */
	private function is_access_token_expired( $addon_settings, $comparison_time ) {
		// Settings are not initialized so no token exists to refresh.
		if ( empty( $addon_settings ) ) {
			$this->log_debug( __METHOD__ . '(): No plugin settings found. No access token to refresh.' );
			return false;
		}

		// No refresh token exists in the database because the access_token is long-lived.
		if ( ! rgar( $addon_settings, 'refresh_token' ) ) {
			$this->log_debug( __METHOD__ . '(): No refresh token available; not refreshing access token.' );
			return false;
		}

		// The token expiration hasn't passed the current time yet.
		if ( rgar( $addon_settings, 'access_token_expires' ) > $comparison_time ) {
			$this->log_debug( __METHOD__ . '(): Access token not yet expired.' );
			return false;
		}

		return true;
	}

	/**
	 * Get Dropbox app key.
	 *
	 * @since  1.0
	 *
	 * @return string
	 */
	public function get_app_key() {

		// Get plugin settings.
		$settings = $this->get_plugin_settings();

		return rgar( $settings, 'customAppKey' ) ? rgar( $settings, 'customAppKey' ) : null;

	}

	/**
	 * Get Dropbox app secret.
	 *
	 * @since  1.0
	 *
	 * @return string
	 */
	public function get_app_secret() {

		// Get plugin settings.
		$settings = $this->get_plugin_settings();

		return rgar( $settings, 'customAppSecret' ) ? rgar( $settings, 'customAppSecret' ) : null;

	}

	/**
	 * Get OAuth Redirect URI for custom Dropbox app.
	 *
	 * @since  1.0
	 *
	 * @return string
	 */
	public function get_redirect_uri() {

		return admin_url( 'admin.php?page=gf_settings&subview=gravityformsdropbox', 'https' );

	}

	/**
	 * Get Dropbox authentication URL.
	 *
	 * @since  1.0
	 *
	 * @param string $app_key Dropbox app key.
	 * @param string $app_secret Dropbox app secret.
	 *
	 * @return string
	 */
	public function get_auth_url( $app_key = null, $app_secret = null ) {

		// Get app key and secret from setting if not provided.
		$app_key    = rgblank( $app_key ) ? $this->get_app_key() : $app_key;
		$app_secret = rgblank( $app_secret ) ? $this->get_app_secret() : $app_secret;

		// If app key or secret are empty, return null.
		if ( rgblank( $app_key ) || rgblank( $app_secret ) ) {
			return null;
		}

		// Setup a new Dropbox API object.
		$dropbox = new GF_Dropbox_API( array(), $app_key, $app_secret );

		return $dropbox->get_authorization_url( $this->get_redirect_uri() );

	}

	/**
	 * Check if Dropbox Chooser is available for use.
	 *
	 * @since  2.0
	 *
	 * @return bool
	 */
	public function is_chooser_available() {

		// If custom app is not enabled, return false.
		if ( ! $this->get_plugin_setting( 'customAppEnable' ) ) {
			return false;
		}

		// Get site URL.
		$site_url = parse_url( get_site_url() );

		// Get origin from site URL.
		$origin = $site_url['scheme'] . '://' . $site_url['host'];

		// Get app key.
		$app_key = $this->get_app_key();

		// Prepare chooser URL.
		$chooser_url = add_query_arg(
			array(
				'origin'  => $origin,
				'app_key' => $app_key,
			),
			'https://www.dropbox.com/chooser'
		);

		// Make a request to the chooser URL.
		$chooser_request = wp_remote_get( $chooser_url );

		return ! is_wp_error( $chooser_request ) && 200 === $chooser_request['response']['code'];

	}

	/**
	 * Check if Dropbox app key and secret are valid.
	 *
	 * TODO: We should probably rename this method. This doesn't actually determine of the app secret is valid.
	 *
	 * @since  1.0
	 *
	 * @param string $app_key    Dropbox app key.
	 * @param string $app_secret Dropbox app secret.
	 *
	 * @return bool|null
	 */
	public function is_valid_app_key_secret( $app_key = null, $app_secret = null ) {

		// Log that we are going to validate the app key and secret.
		$this->log_debug( __METHOD__ . '(): Beginning validation of app key and secret.' );

		// If app secret is an array, retrieve the app key and secret from plugin settings.
		if ( is_array( $app_secret ) ) {

			$app_key    = $this->get_app_key();
			$app_secret = $this->get_app_secret();

		} else {

			// Get app key and secret from setting if not provided.
			$app_key    = rgblank( $app_key ) ? $this->get_app_key() : $app_key;
			$app_secret = rgblank( $app_secret ) ? $this->get_app_secret() : $app_secret;

		}

		// If app key or secret are empty, return null.
		if ( rgblank( $app_key ) || rgblank( $app_secret ) ) {
			$this->log_debug( __METHOD__ . '(): App key or secret is missing. Ending validation.' );
			return null;
		}

		// Get Dropbox authentication URL.
		$auth_url = $this->get_auth_url( $app_key, $app_secret );

		// Make a request to the web auth URL.
		$auth_request = wp_remote_get( $auth_url );

		// Return result based on response code.
		if ( ! is_wp_error( $auth_request ) && 200 === $auth_request['response']['code'] ) {
			$this->log_debug( __METHOD__ . '(): App key and secret are valid.' );
			return true;
		} else {
			$this->log_error( __METHOD__ . '(): App key and/or secret are invalid.' );
			return false;
		}

	}

	/**
	 * Validate and save Dropbox app key and secret.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function ajax_is_valid_app_key_secret() {

		if ( $this->initialize_api() ) {
			return false;
		}

		// Verify nonce.
		if ( false === wp_verify_nonce( rgget( 'nonce' ), 'gfdropbox_valid_app_key_secret' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityformsdropbox' ) ) );
		}

		// If user is not authorized, exit.
		if ( ! GFCommon::current_user_can_any( $this->_capabilities_settings_page ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityformsdropbox' ) ) );
		}

		// Set initial auth URL.
		$auth_url = null;

		// Get app key and secret from request.
		$app_key    = sanitize_text_field( rgget( 'app_key' ) );
		$app_secret = sanitize_text_field( rgget( 'app_secret' ) );

		// Get plugin settings.
		$settings = $this->get_plugin_settings();

		// Set app key and secret.
		$settings['customAppKey']    = $app_key;
		$settings['customAppSecret'] = $app_secret;

		// Save plugin settings.
		$this->update_plugin_settings( $settings );


		// Test app key and secret validity.
		$dropbox = new GF_Dropbox_API( $app_key, $app_secret );
		// Verify the app key.
		$app_key_valid = $this->is_valid_app_key_secret( $app_key, $app_secret );
		// Verify the app secret if the key is valid.
		$app_key_and_secret_valid = $dropbox->verify_custom_app_key_and_secret( $app_key, $app_secret );

		// If both app key and secret are valid, save and get authentication URL.
		if ( $app_key_and_secret_valid ) {

			// Get authentication URL.
			$auth_url = $this->get_auth_url( $app_key, $app_secret );

			wp_send_json_success(
				array(
					'auth_url'     => $auth_url,
					'valid_key'    => $app_key_valid,
					'valid_secret' => $app_key_and_secret_valid,
				)
			);

		} else {
			wp_send_json_error(
				array(
					'valid_key'    => $app_key_valid,
					'valid_secret' => $app_key_and_secret_valid,
				)
			);
		}

	}

	/**
	 * Get folder contents.
	 *
	 * @since  1.0
	 *
	 * @param string $path Folder path.
	 *
	 * @return array
	 */
	public function get_folder( $path ) {

		// If the Dropbox instance isn't configured, return empty folder array.
		if ( ! $this->initialize_api() ) {

			// Log that API is not initialized.
			$this->log_error( __METHOD__ . '(): Unable to get contents of folder (' . $path . ') because API was not initialized.' );

			return array();

		}

		// Initialize folder path variable.
		$folder_path = null;

		// If path is not set, use root folder.
		if ( empty( $path ) || '/' === $path ) {

			// Set folder path, exploded folder path variables.
			$folder_path          = '/';
			$exploded_folder_path = array( '' );

		} else {

			// Try and get requested folder.
			try {

				// Get folder metadata.
				$folder_metadata = $this->api->get_metadata( $path );

				// Set folder path and exploded path.
				$folder_path          = $folder_metadata->path_lower;
				$exploded_folder_path = explode( '/', $folder_metadata->path_display );

			} catch ( Exception $e ) {

				// Log that folder could not be found.
				$this->log_error( __METHOD__ . '(): Unable to get contents of folder (' . $path . ') because folder could not be found.' );

				// If folder was not found, set folder path to root folder.
				$folder_path          = '/';
				$exploded_folder_path = array( '' );

			}

		}

		// Get folder contents.
		try {

			// Get folder metadata.
			$folder_contents = $this->api->list_folder( '/' === $folder_path ? '' : $folder_path );

		} catch ( \Exception $e ) {

			// Log that folder could not be found.
			$this->log_error( __METHOD__ . '(): Unable to get contents of folder (' . $folder_path . ') because folder could not be found.' );

		}

		// Setup folder object.
		$folder = array(
			'id'            => $folder_path,
			'text'          => end( $exploded_folder_path ),
			'parent'        => dirname( $folder_path ),
			'children'      => false,
			'child_folders' => array(),
		);

		// Loop through folder items.
		foreach ( $folder_contents->entries as $item ) {

			// If item is not a folder, skip it.
			if ( 'folder' !== $item->{'.tag'} ) {
				continue;
			}

			// Initialize has children variable.
			$has_children = false;

			// Get item contents.
			try {

				// Get item contents.
				$item_contents = $this->api->list_folder( $item->path_display );

			} catch ( \Exception $e ) {

				// Log that folder contents could not be retrieved.
				$this->log_error( __METHOD__ . '(): Unable to get contents of folder (' . $item->path_display . ').' );

			}

			// Loop through folder contents.
			foreach ( $item_contents->entries as $child_item ) {

				// If item is a folder, set has children flag to true.
				if ( 'folder' === $child_item->{'.tag'} ) {
					$has_children = true;
				}
			}

			// Add child folder.
			$folder['child_folders'][] = array(
				'id'       => $item->path_lower,
				'text'     => $item->name,
				'parent'   => dirname( $item->path_lower ),
				'children' => $has_children,
			);

		}

		// If folder has child folders, set children flag to true.
		if ( count( $folder['child_folders'] ) > 0 ) {
			$folder['children'] = true;
		}

		return $folder;

	}

	/**
	 * Create nonce for Dropbox upload request.
	 *
	 * @since  1.0
	 *
	 * @return string
	 */
	public function create_nonce() {

		$action = $this->nonce_action;
		$i      = wp_nonce_tick();

		return substr( wp_hash( $i . $action, 'nonce' ), - 12, 10 );

	}

	/**
	 * Verify nonce for Dropbox upload request.
	 *
	 * @since  1.0
	 *
	 * @param string $nonce Nonce to be verified.
	 *
	 * @return int|bool
	 */
	public function verify_nonce( $nonce ) {

		$action = $this->nonce_action;
		$i      = wp_nonce_tick();

		// Nonce generated 0-12 hours ago.
		if ( substr( wp_hash( $i . $this->nonce_action, 'nonce' ), - 12, 10 ) === $nonce ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago.
		if ( substr( wp_hash( ( $i - 1 ) . $this->nonce_action, 'nonce' ), - 12, 10 ) === $nonce ) {
			return 2;
		}

		// Log that nonce was unable to be verified.
		$this->log_error( __METHOD__ . '(): Aborting. Unable to verify nonce.' );

		return false;

	}

	/**
	 * Checks if a previous version was installed and enable default app flag if no custom app was used.
	 *
	 * @since  1.0.6
	 *
	 * @param string $previous_version The version number of the previously installed version.
	 */
	public function upgrade( $previous_version ) {

		// Was previous Add-On version before 1.0.6?
		$previous_is_pre_custom_app_only = ! empty( $previous_version ) && version_compare( $previous_version, '1.0.6', '<' );

		// Run 1.0.6 upgrade routine.
		if ( $previous_is_pre_custom_app_only ) {

			// Get plugin settings.
			$settings = $this->get_plugin_settings();

			// Set default app flag.
			if ( ! rgar( $settings, 'customAppEnable' ) && $this->initialize_api() ) {
				$settings['defaultAppEnabled'] = '1';
			}

			// Remove custom app flag.
			unset( $settings['customAppEnable'] );

			// Save plugin settings.
			$this->update_plugin_settings( $settings );

		}

		// Was previous Add-On version before 2.0?
		$previous_is_pre_20 = ! empty( $previous_version ) && version_compare( $previous_version, '2.0dev2', '<' );

		// Run 2.0 upgrade routine.
		if ( $previous_is_pre_20 ) {

			// Get plugin settings.
			$settings = $this->get_plugin_settings();

			// Set custom app state.
			if ( rgar( $settings, 'defaultAppEnabled' ) ) {
				unset( $settings['defaultAppEnabled'], $settings['customAppEnable'] );
			} else {
				$settings['customAppEnable'] = '1';
			}

			// Save plugin settings.
			$this->update_plugin_settings( $settings );

		}

	}

	/**
	 * Get Gravity API URL.
	 *
	 * @since 2.5
	 *
	 * @param string $path Path.
	 *
	 * @return string
	 */
	public function get_gravity_api_url( $path = '' ) {
		return ( defined( 'GRAVITY_API_URL' ) ? GRAVITY_API_URL : 'https://gravityapi.com/wp-json/gravityapi/v1' ) . $path;
	}

	/**
	 * Get action name for authentication state.
	 *
	 * @since 2.7
	 *
	 * @return string
	 */
	public function get_authentication_state_action() {

		return 'gform_dropbox_authentication_state';

	}
}

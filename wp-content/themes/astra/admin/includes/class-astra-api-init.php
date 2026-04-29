<?php
/**
 * Class Astra_API_Init.
 *
 * @package Astra
 * @since 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Bail if WP_REST_Controller class does not exist.
if ( ! class_exists( 'WP_REST_Controller' ) ) {
	return;
}
/**
 * Astra_API_Init.
 *
 * @since 4.1.0
 */
class Astra_API_Init extends WP_REST_Controller {
	/**
	 * Instance
	 *
	 * @var null $instance
	 * @since 4.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 4.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'astra/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/admin/settings/';

	/**
	 * Option name
	 *
	 * @var string $option_name DB option name.
	 * @since 4.0.0
	 */
	private static $option_name = 'astra_admin_settings';

	/**
	 * Admin settings dataset
	 *
	 * @var array $astra_admin_settings Settings array.
	 * @since 4.0.0
	 */
	private static $astra_admin_settings = array();

	/**
	 * Constructor
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		self::$astra_admin_settings = get_option( self::$option_name, array() );

		// REST API extensions init.
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register API routes.
	 *
	 * @since 4.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_admin_settings' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'                => array(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// Register learn chapters route.
		register_rest_route(
			$this->namespace,
			'get-learn-chapters',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_learn_chapters' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'                => array(),
				),
			)
		);

		// Register save learn progress route.
		register_rest_route(
			$this->namespace,
			'update-learn-progress',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_learn_progress' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'                => array(
						'chapterId' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'stepId'    => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'completed' => array(
							'required' => true,
							'type'     => 'boolean',
						),
					),
				),
			)
		);
	}

	/**
	 * Get common settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array $updated_option defaults + set DB option data.
	 *
	 * @since 4.0.0
	 */
	public function get_admin_settings( $request ) {
		$db_option = get_option( 'astra_admin_settings', array() );

		$defaults = apply_filters(
			'astra_dashboard_rest_options',
			array(
				'self_hosted_gfonts'    => self::get_admin_settings_option( 'self_hosted_gfonts', false ),
				'preload_local_fonts'   => self::get_admin_settings_option( 'preload_local_fonts', false ),
				'use_old_header_footer' => astra_get_option( 'is-header-footer-builder', false ),
				'use_upgrade_notices'   => astra_showcase_upgrade_notices(),
				'analytics_enabled'     => get_option( 'astra_usage_optin', 'no' ) === 'yes',
				'show_learn_tab'        => self::get_admin_settings_option( 'show_learn_tab', true ),
				'enable_abilities'      => self::get_admin_settings_option( 'enable_abilities', false ),
				'enable_edit_abilities' => self::get_admin_settings_option( 'enable_edit_abilities', true ),
				'enable_mcp_server'     => self::get_admin_settings_option( 'enable_mcp_server', false ),
			)
		);

		return wp_parse_args( $db_option, $defaults );
	}

	/**
	 * Get learn chapters data.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array Learn chapters data.
	 *
	 * @since 4.8.7
	 */
	public function get_learn_chapters( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if ( ! is_callable( 'Astra_Learn::get_learn_chapters' ) ) {
			return array();
		}

		// Use Astra_Learn helper to get chapters with progress.
		return Astra_Learn::get_learn_chapters();
	}

	/**
	 * Save learn progress.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 *
	 * @since 4.8.7
	 */
	public function save_learn_progress( $request ) {
		$chapter_id = $request->get_param( 'chapterId' );
		$step_id    = $request->get_param( 'stepId' );
		$completed  = $request->get_param( 'completed' );

		// Get current progress.
		$user_id        = get_current_user_id();
		$saved_progress = get_user_meta( $user_id, 'astra_learn_progress', true );
		if ( ! is_array( $saved_progress ) ) {
			$saved_progress = array();
		}

		// Initialize chapter array if it doesn't exist.
		if ( ! isset( $saved_progress[ $chapter_id ] ) || ! is_array( $saved_progress[ $chapter_id ] ) ) {
			$saved_progress[ $chapter_id ] = array();
		}

		// Update progress for this step in nested format.
		$saved_progress[ $chapter_id ][ $step_id ] = (bool) $completed;

		// Save to user meta.
		update_user_meta( $user_id, 'astra_learn_progress', $saved_progress );

		/**
		 * Fires after learn progress is saved.
		 *
		 * @param array $saved_progress Full progress data for the user.
		 * @since 4.12.7
		 */
		do_action( 'astra_learn_progress_saved', $saved_progress );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Progress saved successfully.', 'astra' ),
			),
			200
		);
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|bool
	 * @since 4.0.0
	 */
	public function get_permissions_check( $request ) {

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error( 'astra_rest_cannot_view', esc_html__( 'Sorry, you cannot list resources.', 'astra' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Returns an value,
	 * based on the settings database option for the admin settings page.
	 *
	 * @param  string $key     The sub-option key.
	 * @param  mixed  $default Option default value if option is not available.
	 * @return mixed            Return the option value based on provided key
	 * @since 4.0.0
	 */
	public static function get_admin_settings_option( $key, $default = false ) {
		return isset( self::$astra_admin_settings[ $key ] ) ? self::$astra_admin_settings[ $key ] : $default;
	}

	/**
	 * Update an value of a key,
	 * from the settings database option for the admin settings page.
	 *
	 * @param string $key       The option key.
	 * @param mixed  $value     The value to update.
	 * @return mixed            Return the option value based on provided key
	 * @since 4.0.0
	 */
	public static function update_admin_settings_option( $key, $value ) {
		$astra_admin_updated_settings         = get_option( self::$option_name, array() );
		$astra_admin_updated_settings[ $key ] = $value;
		update_option( self::$option_name, $astra_admin_updated_settings );
	}
}

Astra_API_Init::get_instance();

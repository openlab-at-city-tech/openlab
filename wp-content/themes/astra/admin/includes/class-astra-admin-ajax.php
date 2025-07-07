<?php
/**
 * Astra Admin Ajax Base.
 *
 * @package Astra
 * @since 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Astra_Admin_Ajax.
 *
 * @since 4.0.0
 */
class Astra_Admin_Ajax {
	/**
	 * Ajax action prefix.
	 *
	 * @var string
	 * @since 4.0.0
	 */
	private $prefix = 'astra';

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
			/** @psalm-suppress InvalidPropertyAssignmentValue */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			self::$instance = new self();
			/** @psalm-suppress InvalidPropertyAssignmentValue */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		}
		return self::$instance;
	}

	/**
	 * Errors class instance.
	 *
	 * @var array
	 * @since 4.0.0
	 */
	private $errors = array();

	/**
	 * Constructor
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_action(
			'init',
			function() {
				$this->errors = array(
					'permission' => esc_html__( 'Sorry, you are not allowed to do this operation.', 'astra' ),
					'nonce'      => esc_html__( 'Nonce validation failed', 'astra' ),
					'default'    => esc_html__( 'Sorry, something went wrong.', 'astra' ),
					'invalid'    => esc_html__( 'No post data found!', 'astra' ),
				);
			}
		);

		add_action( 'wp_ajax_ast_disable_pro_notices', array( $this, 'disable_astra_pro_notices' ) );
		add_action( 'wp_ajax_astra_recommended_plugin_install', array( $this, 'required_plugin_install' ) );
		add_action( 'wp_ajax_ast_migrate_to_builder', array( $this, 'migrate_to_builder' ) );
		add_action( 'wp_ajax_astra_update_admin_setting', array( $this, 'astra_update_admin_setting' ) );
		add_action( 'wp_ajax_astra_analytics_optin_status', array( $this, 'astra_analytics_optin_status' ) );
		add_action( 'wp_ajax_astra_recommended_plugin_activate', array( $this, 'required_plugin_activate' ) );
		add_action( 'wp_ajax_astra_recommended_plugin_deactivate', array( $this, 'required_plugin_deactivate' ) );
	}

	/**
	 * Return boolean settings for admin dashboard app.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	public function astra_admin_settings_typewise() {
		return apply_filters(
			'astra_admin_settings_datatypes',
			array(
				'self_hosted_gfonts'    => 'bool',
				'preload_local_fonts'   => 'bool',
				'use_old_header_footer' => 'bool',
			)
		);
	}

	/**
	 * Disable pro upgrade notice from all over in Astra.
	 *
	 * @since 4.0.0
	 */
	public function disable_astra_pro_notices() {

		$response_data = array( 'message' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'invalid' ) );
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification.
		 */
		if ( ! check_ajax_referer( 'astra_update_admin_setting', 'security', false ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the access', 'astra' ) );
		}

		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$migrate = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : '';
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$migrate = 'true' === $migrate ? true : false;
		astra_update_option( 'ast-disable-upgrade-notices', $migrate );

		wp_send_json_success();
	}

	/**
	 * Migrate to New Header Builder
	 *
	 * @since 4.0.0
	 */
	public function migrate_to_builder() {

		$response_data = array( 'message' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'invalid' ) );
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification.
		 */
		if ( ! check_ajax_referer( 'astra_update_admin_setting', 'security', false ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$migrate = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : '';
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$migrate = 'true' === $migrate ? true : false;
		/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$migration_flag = astra_get_option( 'v3-option-migration', false );
		astra_update_option( 'is-header-footer-builder', $migrate );

		if ( $migrate && false === $migration_flag ) {
			require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-builder-migration-updater.php';  // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			astra_header_builder_migration();
		}

		wp_send_json_success();
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function astra_update_admin_setting() {

		$response_data = array( 'message' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'invalid' ) );
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification.
		 */
		if ( ! check_ajax_referer( 'astra_update_admin_setting', 'security', false ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		$get_bool_settings = $this->astra_admin_settings_typewise();
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$sub_option_key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$sub_option_value = '';

		// @codingStandardsIgnoreStart
		if ( isset( $get_bool_settings[ $sub_option_key ] ) ) {
			if ( 'bool' === $get_bool_settings[ $sub_option_key ] ) {
				/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$val = isset( $_POST['value'] ) && 'true' === sanitize_text_field( $_POST['value'] ) ? true : false;
				/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$sub_option_value = $val;
			} else {
				/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$val = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';
				/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$sub_option_value = $val;
			}
		}
		// @codingStandardsIgnoreEnd

		Astra_API_Init::update_admin_settings_option( $sub_option_key, $sub_option_value );

		$response_data = array(
			'message' => esc_html__( 'Successfully saved data!', 'astra' ),
		);

		wp_send_json_success( $response_data );
	}

	/**
	 * Astra Analytics Opt-in.
	 *
	 * @return void
	 * @since 4.10.0
	 */
	public function astra_analytics_optin_status() {
		$response_data = array( 'message' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'invalid' ) );
			wp_send_json_error( $response_data );
		}

		/* Nonce verification */
		if ( ! check_ajax_referer( 'astra_update_admin_setting', 'security', false ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		$opt_in = filter_input( INPUT_POST, 'value', FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no';
		update_site_option( 'astra_analytics_optin', $opt_in );

		$response_data = array(
			'message' => esc_html__( 'Successfully saved data!', 'astra' ),
		);

		wp_send_json_success( $response_data );
	}

	/**
	 * Get ajax error message.
	 *
	 * @param string $type Message type.
	 * @return string
	 * @since 4.0.0
	 */
	public function get_error_msg( $type ) {

		if ( ! isset( $this->errors[ $type ] ) ) {
			$type = 'default';
		}

		return $this->errors[ $type ];
	}

	/**
	 * Handles the installation and saving of required plugins.
	 *
	 * This function is responsible for installing and saving required plugins for the Astra theme.
	 * It checks for the plugin slug in the AJAX request, verifies the nonce, and initiates the plugin installation process.
	 * If the plugin is successfully installed, it schedules a database update to map the plugin slug to a custom key for analytics tracking.
	 *
	 * @since 4.8.12
	 */
	public function required_plugin_install() {

		check_ajax_referer( 'updates', '_ajax_nonce' );

		// Fetching the plugin slug from the AJAX request.
		// @psalm-suppress PossiblyInvalidArgument
		$plugin_slug = isset( $_POST['slug'] ) && is_string( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

		if ( empty( $plugin_slug ) ) {
			wp_send_json_error( array( 'message' => __( 'Plugin slug is missing.', 'astra' ) ) );
		}

		// Schedule the database update if the plugin is installed successfully.
		add_action(
			'shutdown',
			static function () use ( $plugin_slug ) {
				// Iterate through all plugins to check if the installed plugin matches the current plugin slug.
				$all_plugins = get_plugins();
				foreach ( $all_plugins as $plugin_file => $_ ) {
					if ( is_callable( 'BSF_UTM_Analytics::update_referer' ) && strpos( $plugin_file, $plugin_slug . '/' ) === 0 ) {
						// If the plugin is found and the update_referer function is callable, update the referer with the corresponding product slug.
						BSF_UTM_Analytics::update_referer( 'astra', $plugin_slug );
						return;
					}
				}
			}
		);

		if ( function_exists( 'wp_ajax_install_plugin' ) ) {
			// @psalm-suppress NoValue
			wp_ajax_install_plugin();
		} else {
			wp_send_json_error( array( 'message' => __( 'Plugin installation function not found.', 'astra' ) ) );
		}
	}

	/**
	 * Required Plugin Activate
	 *
	 * @since 1.2.4
	 */
	public function required_plugin_activate() {

		$response_data = array( 'message' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'invalid' ) );
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification.
		 */
		if ( ! check_ajax_referer( 'astra_plugin_manager_nonce', 'security', false ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! sanitize_text_field( wp_unslash( $_POST['init'] ) ) ) {
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			wp_send_json_error(
				array(
					'success' => false,
					'message' => esc_html__( 'No plugin specified', 'astra' ),
				)
			);
		}

		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$plugin_init = isset( $_POST['init'] ) ? sanitize_text_field( wp_unslash( $_POST['init'] ) ) : '';
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$activate = activate_plugin( $plugin_init );

		if ( is_wp_error( $activate ) ) {
			/** @psalm-suppress PossiblyNullReference */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			wp_send_json_error(
				array(
					'success' => false,
					'message' => $activate->get_error_message(),
				)
			);
			/** @psalm-suppress PossiblyNullReference */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		}

		/**
		 * Added this flag as tracker to track onboarding and funnel stats for SureCart owners.
		 *
		 * @since 4.7.0
		 */
		if ( 'surecart/surecart.php' === $plugin_init ) {
			update_option( 'surecart_source', 'astra', false );
		}

		wp_send_json_success(
			array(
				'success' => true,
				'message' => esc_html__( 'Plugin Successfully Activated', 'astra' ),
			)
		);
	}

	/**
	 * Required Plugin Activate
	 *
	 * @since 1.2.4
	 */
	public function required_plugin_deactivate() {

		$response_data = array( 'message' => $this->get_error_msg( 'permission' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		if ( empty( $_POST ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'invalid' ) );
			wp_send_json_error( $response_data );
		}

		/**
		 * Nonce verification.
		 */
		if ( ! check_ajax_referer( 'astra_plugin_manager_nonce', 'security', false ) ) {
			$response_data = array( 'message' => $this->get_error_msg( 'nonce' ) );
			wp_send_json_error( $response_data );
		}

		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! sanitize_text_field( wp_unslash( $_POST['init'] ) ) ) {
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			wp_send_json_error(
				array(
					'success' => false,
					'message' => esc_html__( 'No plugin specified', 'astra' ),
				)
			);
		}

		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$plugin_init = isset( $_POST['init'] ) ? sanitize_text_field( wp_unslash( $_POST['init'] ) ) : '';
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$deactivate = deactivate_plugins( $plugin_init );

		if ( is_wp_error( $deactivate ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => $deactivate->get_error_message(),
				)
			);
		}

		wp_send_json_success(
			array(
				'success' => true,
				'message' => esc_html__( 'Plugin Successfully Deactivated', 'astra' ),
			)
		);
	}
}

Astra_Admin_Ajax::get_instance();

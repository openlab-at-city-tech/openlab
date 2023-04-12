<?php

namespace WeBWorK;

/**
 * Plugin loader.
 *
 * @since 1.0.0
 */
class Loader {
	/**
	 * Integrations.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $integrations = array();

	/**
	 * Singleton bootstrap.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		static $instance;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Private constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$server_site_id = (int) apply_filters( 'webwork_server_site_id', get_current_blog_id() );
		if ( $server_site_id === get_current_blog_id() ) {
			$this->server = new Server();
		}

		$client_site_id = (int) apply_filters( 'webwork_client_site_id', get_current_blog_id() );
		if ( $client_site_id === get_current_blog_id() ) {
			$this->client = new Client();
		}

		$this->includes();
		$this->register_assets();

		if ( defined( 'WP_CLI' ) ) {
			$this->set_up_cli();
		}

		/**
		 * Fires when the WeBWorK plugin has been set up.
		 *
		 * @since 1.0.0
		 */
		do_action( 'webwork_init' );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 */
	protected function includes() {
		include WEBWORK_PLUGIN_DIR . '/includes/functions.php';
		include WEBWORK_PLUGIN_DIR . '/includes/template.php';
	}

	/**
	 * Register assets.
	 *
	 * @since 1.0.0
	 */
	protected function register_assets() {
		// Scripts.
		wp_register_script( 'webwork-form-js', WEBWORK_PLUGIN_URL . 'assets/js/webwork-form.js', array( 'jquery' ), WEBWORK_PLUGIN_VER, true );

		$webwork_form_js_strings = array(
			'hide_problem' => esc_html__( 'Hide problem', 'webworkqa' ),
			'show_problem' => esc_html__( 'Show problem', 'webworkqa' ),
			'hide_related' => esc_html__( 'Hide related questions', 'webworkqa' ),
			'show_related' => esc_html__( 'Show related questions', 'webworkqa' ),
		);
		wp_localize_script( 'webwork-form-js', 'WeBWorK', $webwork_form_js_strings );

		wp_register_script( 'webwork-mathjax-loader', WEBWORK_PLUGIN_URL . '/assets/js/webwork-mathjax-loader.js', [], WEBWORK_PLUGIN_VER, true );

		wp_register_script( 'webwork-redirector', WEBWORK_PLUGIN_URL . 'assets/js/webwork-redirector.js', [], WEBWORK_PLUGIN_VER, true );

		// Styles.
		wp_register_style( 'webwork-form-css', WEBWORK_PLUGIN_URL . 'assets/css/webwork-form.css', [], WEBWORK_PLUGIN_VER );
	}

	protected function set_up_cli() {
		\WP_CLI::add_command( 'webworkqa', __NAMESPACE__ . '\CLI\Command' );
	}
}

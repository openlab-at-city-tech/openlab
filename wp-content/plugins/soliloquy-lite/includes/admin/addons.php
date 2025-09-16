<?php
/**
 * Addons Class.
 *
 * @since 2.5.0
 * @package SoliloquyWP Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soliloquy Addons
 *
 * @since 2.5.0
 */
class Soliloquy_Addons {

	/**
	 * Hold Class Instance.
	 *
	 * @since 2.5.0
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * Holds Soliloquy_Lite
	 *
	 * @var Soliloquy_Lite
	 */
	public $base;

	/**
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	public $hook;

	/**
	 * Holds Soliloquy_Common_Admin_Lite O
	 *
	 * @var Soliloquy_Common_Admin_Lite
	 */
	public $common;

	/**
	 * Class Constructor
	 *
	 * @since 2.5.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base   = Soliloquy_Lite::get_instance();
		$this->common = Soliloquy_Common_Admin_Lite::get_instance();

		// Add custom settings submenu.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 12 );
	}

	/**
	 * Register our Admin Menu.
	 *
	 * @return void
	 */
	public function admin_menu() {

		// Register the submenu.
		$this->hook = add_submenu_page(
			'edit.php?post_type=soliloquy',
			__( 'Soliloquy Addons', 'soliloquy' ),
			__( 'Addons', 'soliloquy' ),
			apply_filters( 'soliloquy_menu_cap', 'manage_options' ),
			$this->base->plugin_slug . '-addons',
			[ $this, 'addons_page' ]
		);

		// If successful, load admin assets only on that page and check for addons refresh.
		if ( $this->hook ) {
			add_action( 'load-' . $this->hook, [ $this, 'addons_page_assets' ] );
		}
	}
	/**
	 * Loads assets for the settings page.
	 *
	 * @since 1.0.0
	 */
	public function addons_page_assets() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Helper Method to Enqueue Styles
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_admin_styles() {

		wp_register_style( $this->base->plugin_slug . '-addons-style', plugins_url( 'assets/css/addons.css', $this->base->file ), [], $this->base->version );
		wp_enqueue_style( $this->base->plugin_slug . '-addons-style' );

		// Run a hook to load in custom styles.
		do_action( 'soliloquy_addons_styles' );
	}

	/**
	 * Helper Method to Enqueue Scripts
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_admin_scripts() {

		wp_enqueue_script( 'jquery-ui-tabs' );

		wp_register_script( $this->base->plugin_slug . '-chosen', plugins_url( 'assets/js/min/chosen.jquery-min.js', $this->base->file ), [], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-chosen' );

		wp_register_script( $this->base->plugin_slug . '-addons-script', plugins_url( 'assets/js/addons.js', $this->base->file ), [ 'jquery', 'jquery-ui-tabs' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-addons-script' );
		wp_localize_script(
			$this->base->plugin_slug . '-addons-script',
			'soliloquy_addons',
			[
				'active'           => __( 'Active', 'soliloquy' ),
				'activate'         => __( 'Activate', 'soliloquy' ),
				'activate_nonce'   => wp_create_nonce( 'soliloquy-activate' ),
				'activating'       => __( 'Activating...', 'soliloquy' ),
				'ajax'             => admin_url( 'admin-ajax.php' ),
				'deactivate'       => __( 'Deactivate', 'soliloquy' ),
				'deactivate_nonce' => wp_create_nonce( 'soliloquy-deactivate' ),
				'deactivating'     => __( 'Deactivating...', 'soliloquy' ),
				'inactive'         => __( 'Inactive', 'soliloquy' ),
				'install'          => __( 'Install Addon', 'soliloquy' ),
				'install_nonce'    => wp_create_nonce( 'soliloquy-install' ),
				'installing'       => __( 'Installing...', 'soliloquy' ),
				'proceed'          => __( 'Proceed', 'soliloquy' ),
				'redirect'         => esc_url(
					add_query_arg(
						[
							'post_type'          => 'soliloquy',
							'soliloquy-upgraded' => true,
						],
						admin_url( 'edit.php' )
					)
				),
				'upgrade_nonce'    => wp_create_nonce( 'soliloquy-upgrade' ),
			]
		);

		// Run a hook to load in custom scripts.
		do_action( 'soliloquy_addons_scripts' );
	}

	/**
	 * Helper Method to output Addons Page.
	 *
	 * @return void
	 */
	public function addons_page() {

		?>

		<div id="soliloquy-heading">
			<h1><?php esc_html_e( 'Soliloquy Addons', 'soliloquy' ); ?></h1>
		</div>

		<div class="wrap">

			<h1 class="soliloquy-hideme"></h1>
			<div id="soliloquy-settings-addons">

		<?php $upgrade_addons = $this->get_all_addons(); ?>
			<div class="soliloquy-clearfix"></div>

			<div id="soliloquy-addons-upgrade-area">

				<h2 class="soliloquy-addons-upgrade"><?php esc_html_e( 'Unlock More Addons', 'soliloquy' ); ?></h2>

				<p class="soliloquy-unlock-text"><strong><?php esc_html_e( 'Want even more addons?', 'soliloquy' ); ?>&nbsp;</strong><a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank"><?php esc_html_e( 'Upgrade your Soliloquy account', 'soliloquy' ); ?></a><span>&nbsp;<?php esc_html_e( 'and unlock the following addons.', 'soliloquy' ); ?></span></p>

			<?php
			// Let's begin outputting the addons.
			if ( $upgrade_addons ) :
				$i = 0;

				foreach ( (array) $upgrade_addons as $i => $addon ) {
					// Attempt to get the plugin basename if it is installed or active.
					$plugin_basename   = $this->get_plugin_basename_from_slug( $addon->slug );
					$installed_plugins = get_plugins();
					$last              = ( 2 === $i % 3 ) ? 'last' : '';

					// If site is HTTPS, serve $addon->image as HTTPS too, this prevents warnings.
					if ( is_ssl() ) {
						$addon->image = str_replace( 'http://', 'https://', $addon->image );
					}
					?>

				<div class="soliloquy-addon <?php echo sanitize_html_class( $last ); ?>">

					<div class="soliloquy-addon-content">

						<h3 class="soliloquy-addon-title"><?php echo esc_html( $addon->title ); ?></h3>

						<img class="soliloquy-addon-thumb" src="<?php echo esc_url( $addon->image ); ?>" width="300px" height="250px" alt="<?php echo esc_attr( $addon->title ); ?>" />

							<p class="soliloquy-addon-excerpt"><?php echo esc_html( $addon->excerpt ); ?></p>

						</div>

						<div class="soliloquy-addon-footer">

							<div class="soliloquy-addon-unlock soliloquy-addon-message">

								<a  href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy soliloquy-addon-action-button soliloquy-unlock-addon" rel="<?php echo esc_attr( $addon->title ); ?>"><?php esc_html_e( 'Upgrade Now', 'soliloquy' ); ?></a>

							</div>
						</div>
					</div>
					<?php
					++$i;
				}
			endif
			?>


		</div>

		<?php
	}

	/**
	 * Helper Method to get all addons.
	 *
	 * @return array Addons Array.
	 */
	public function get_all_addons() {

		return $this->perform_remote_request( 'get-all-addons-data', [ 'tgm-updater-key' => '' ] );
	}

	/**
	 * Retrieve the plugin basename from the plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The plugin slug.
	 * @return string      The plugin basename if found, else the plugin slug.
	 */
	public function get_plugin_basename_from_slug( $slug ) {

		$keys = array_keys( get_plugins() );

		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug . '|', $key ) ) {
				return $key;
			}
		}

		return $slug;
	}
	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action        The name of the $_POST action var.
	 * @param array  $body           The content to retrieve from the remote URL.
	 * @param array  $headers        The headers to send to the remote URL.
	 * @param string $return_format The format for returning content from the remote URL.
	 * @return string|bool          Json decoded response on success, false on failure.
	 */
	public function perform_remote_request( $action, $body = [], $headers = [], $return_format = 'json' ) {

		// Build the body of the request.
		$body = wp_parse_args(
			$body,
			[
				'tgm-updater-action'     => $action,
				'tgm-updater-key'        => '',
				'tgm-updater-wp-version' => get_bloginfo( 'version' ),
				'tgm-updater-referer'    => site_url(),
			]
		);
		$body = http_build_query( $body, '', '&' );

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			[
				'Content-Type'   => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body ),
			]
		);

		// Setup variable for wp_remote_post.
		$post = [
			'headers' => $headers,
			'body'    => $body,
		];

		// Perform the query and retrieve the response.
		$response      = wp_remote_post( 'http://soliloquywp.com/', $post );
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( 200 !== $response_code || is_wp_error( $response_body ) ) {
			return false;
		}

		// Return the json decoded content.
		return json_decode( $response_body );
	}
	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Settings object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Addons ) ) {
			self::$instance = new Soliloquy_Addons();
		}

		return self::$instance;
	}
}

$soliloquy_addons = Soliloquy_Addons::get_instance();

<?php
/**
 * Plugin Name: Responsive WordPress Slider - Soliloquy Lite
 * Plugin URI:  https://soliloquywp.com
 * Description: Soliloquy is the best responsive WordPress slider plugin. This is the lite version.
 * Author:      Soliloquy Team
 * Author URI:  https://soliloquywp.com
 * Version:     2.8.0
 * Text Domain: soliloquy
 * Domain Path: languages
 *
 * @package SoliloquyWP Lite
 *
 * Soliloquy is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Soliloquy is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Soliloquy. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed

/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @package Soliloquy_Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */
class Soliloquy_Lite {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '2.8.0';

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'Soliloquy Lite';

	/**
	 * Unique plugin slug identifier.z
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_slug = 'soliloquy-lite';

	/**
	 * Plugin textdomain.
	 *
	 * @since 2.4.0.1
	 *
	 * @var string
	 */
	public $domain = 'soliloquy';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Notifications class
	 *
	 * @var object|null
	 */
	public $notifications = null;
	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->setup_constants();
		// Fire a hook before the class is setup.
		do_action( 'soliloquy_pre_init' );

		// Load the plugin textdomain.
		add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );

		// Load the plugin.
		add_action( 'init', [ $this, 'init' ], 0 );

		add_filter( 'admin_footer_text', [ $this, 'admin_footer' ], 1, 2 );
	}

	/**
	 * Helper method to set constants
	 *
	 * @return void
	 */
	public function setup_constants() {
		if ( ! defined( 'SOLILOQUY_VERSION' ) ) {

			define( 'SOLILOQUY_VERSION', $this->version );

		}

		if ( ! defined( 'SOLILOQUY_SLUG' ) ) {

			define( 'SOLILOQUY_SLUG', $this->plugin_slug );

		}

		if ( ! defined( 'SOLILOQUY_FILE' ) ) {

			define( 'SOLILOQUY_FILE', $this->file );

		}

		if ( ! defined( 'SOLILOQUY_DIR' ) ) {

			define( 'SOLILOQUY_DIR', plugin_dir_path( __FILE__ ) );

		}

		if ( ! defined( 'SOLILOQUY_URL' ) ) {

			define( 'SOLILOQUY_URL', plugin_dir_url( __FILE__ ) );

		}
	}
	/**
	 * Loads the plugin textdomain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Loads the plugin into WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// If the main Soliloquy plugin exists, do nothing.
		if ( class_exists( 'Soliloquy' ) || class_exists( 'Tgmsp' ) ) {
			return;
		}

		// Run hook once Soliloquy has been initialized.
		// This hook is deliberately different from the Pro version, to prevent the entire site breaking.
		// if a user activates Lite with Pro Addons.
		do_action( 'soliloquy_lite_init' );

		// Load admin only components.
		if ( is_admin() ) {
			$this->require_admin();
		}

		// Load global components.
		$this->require_global();
	}

	/**
	 * Loads all admin related files into scope.
	 *
	 * @since 1.0.0
	 */
	public function require_admin() {

		require plugin_dir_path( __FILE__ ) . 'includes/admin/ajax.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/common.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/editor.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/posttype.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/settings.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/addons.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/media-view.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/review.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/blocks.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/welcome.php';

		( new Soliloquy_Settings() )->hooks();
	}

	/**
	 * Loads all global files into scope.
	 *
	 * @since 1.0.0
	 */
	public function require_global() {

		require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/posttype.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/shortcode.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/notifications.php';

		$this->notifications = new Soliloquy_Notifications();
		$this->notifications->hooks();
	}

	/**
	 * Returns a slider based on ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id     The slider ID used to retrieve a slider.
	 * @return array|bool Array of slider data or false if none found.
	 */
	public function get_slider( $id ) {

		$slider = get_transient( '_sol_cache_' . $id );
		// Attempt to return the transient first, otherwise generate the new query to retrieve the data.
		if ( false === $slider ) {
			$slider = $this->_get_slider( $id );
			if ( $slider ) {
				set_transient( '_sol_cache_' . $id, $slider, DAY_IN_SECONDS );
			}
		}

		// Return the slider data.
		return $slider;
	}

	/**
	 * Internal method that returns a slider based on ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id     The slider ID used to retrieve a slider.
	 * @return array|bool Array of slider data or false if none found.
	 */
	public function _get_slider( $id ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		return get_post_meta( $id, '_sol_slider_data', true );
	}

	/**
	 * Returns a slider based on slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slider slug used to retrieve a slider.
	 * @return array|bool  Array of slider data or false if none found.
	 */
	public function get_slider_by_slug( $slug ) {

		$slider = get_transient( '_sol_cache_' . $slug );

		// Attempt to return the transient first, otherwise generate the new query to retrieve the data.
		if ( false === $slider ) {
			$slider = $this->_get_slider_by_slug( $slug );
			if ( $slider ) {
				set_transient( '_sol_cache_' . $slug, $slider, DAY_IN_SECONDS );
			}
		}

		// Return the slider data.
		return $slider;
	}

	/**
	 * Internal method that returns a slider based on slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slider slug used to retrieve a slider.
	 * @return array|bool  Array of slider data or false if none found.
	 */
	public function _get_slider_by_slug( $slug ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$sliders = $this->get_sliders();
		if ( ! $sliders ) {
			return false;
		}

		// Loop through the sliders to find a match by slug.
		$ret = false;
		foreach ( $sliders as $data ) {

			if ( empty( $data['config']['slug'] ) ) {
				continue;
			}

			if ( $data['config']['slug'] === $slug ) {

				$ret = $data;

				break;
			}
		}

		// Return the slider data.
		return $ret;
	}

	/**
	 * Returns all sliders created on the site.
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool Array of slider data or false if none found.
	 */
	public function get_sliders() {

		$sliders = get_transient( '_sol_cache_all' );
		// Attempt to return the transient first, otherwise generate the new query to retrieve the data.
		if ( false === $sliders ) {

			$sliders = $this->_get_sliders();
			if ( $sliders ) {
				set_transient( '_sol_cache_all', $sliders, DAY_IN_SECONDS );
			}
		}

		// Return the slider data.
		return $sliders;
	}

	/**
	 * Internal method that returns all sliders created on the site.
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool Array of slider data or false if none found.
	 */
	public function _get_sliders() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$sliders = new WP_Query(
			[
				'post_type'      => 'soliloquy',
				'post_status'    => 'publish',
				'posts_per_page' => apply_filters( 'soliloquy_get_limit', 99 ),
				'fields'         => 'ids',
				'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					[
						'key'     => '_sol_slider_data',
						'compare' => 'EXISTS',
					],
				],
			]
		);

		if ( ! isset( $sliders->posts ) || empty( $sliders->posts ) ) {
			return false;
		}

		// Now loop through all the sliders found and only use sliders that have images in them.
		$ret = [];
		foreach ( $sliders->posts as $id ) {

			$data = get_post_meta( $id, '_sol_slider_data', true );
			if ( ( empty( $data['slider'] ) && 'default' === Soliloquy_Shortcode_Lite::get_instance()->get_config( 'type', $data ) ) || 'dynamic' === Soliloquy_Shortcode_Lite::get_instance()->get_config( 'type', $data ) ) {
				continue;
			}

			$ret[] = $data;
		}

		// Return the slider data.
		return $ret;
	}

	/**
	 * Getter method for retrieving the main plugin filepath.
	 *
	 * @since 1.2.0
	 */
	public static function get_file() {

		return self::$file;
	}
	/**
	 * Loads Admin Partial
	 *
	 * @since 2.5.0
	 *
	 * @access public
	 * @param string $template Admin View to load.
	 * @param array  $data     (default: array()).
	 * @return bool
	 */
	public function load_admin_partial( $template, $data = [] ) {

		$dir = trailingslashit( plugin_dir_path( __FILE__ ) . 'includes/admin/partials' );

		if ( file_exists( $dir . $template ) ) {

			require_once $dir . $template;

			return true;
		}

		return false;
	}

	/**
	 * Helper flag method for any Soliloquy screen.
	 *
	 * @since 1.2.0
	 *
	 * @return bool True if on a Soliloquy screen, false if not.
	 */
	public static function is_soliloquy_screen() {

		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return false;
		}

		if ( 'soliloquy' === $current_screen->post_type ) {
			return true;
		}

		return false;
	}
	/**
	 * When user is on a Soliloquy related admin page, display footer text
	 * that graciously asks them to rate us.
	 *
	 * @since 2.5.0.5
	 * @param string $text Footer text to filter.
	 * @return string
	 */
	public function admin_footer( $text ) {
		global $current_screen;
		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'soliloquy' ) !== false ) {
			$url = 'https://wordpress.org/support/view/plugin-reviews/soliloquy-lite?filter=5';
			/* translators: %s: urls */
			$text = sprintf( __( 'Please rate <strong>SoliloquyWP</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%2$s" target="_blank">WordPress.org</a> to help us spread the word. Thank you from the Soliloquy team!', 'wpforms' ), $url, $url );
		}
		return $text;
	}
	/**
	 * Helper flag method for the Add/Edit Soliloquy screens.
	 *
	 * @since 1.2.0
	 *
	 * @return bool True if on a Soliloquy Add/Edit screen, false if not.
	 */
	public static function is_soliloquy_add_edit_screen() {

		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return false;
		}

		if ( 'soliloquy' === $current_screen->post_type && 'post' === $current_screen->base ) {
			return true;
		}

		return false;
	}


	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Lite ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

register_activation_hook( __FILE__, 'soliloquy_lite_activation_hook' );

/**
 * Fired when the plugin is activated.
 *
 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false otherwise.
 * @return void
 *
 * @since 1.0.0
 */
function soliloquy_lite_activation_hook( $network_wide ) {

	global $wp_version;

	// Deactivate fi Pro exists.
	if ( class_exists( 'Soliloquy' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	if ( version_compare( $wp_version, '4.4.0', '<' ) && ! defined( 'SOLILOQUY_FORCE_ACTIVATION' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		/* translators: %s: url*/
		wp_die( sprintf( esc_html__( 'Sorry, but your version of WordPress does not meet Soliloquy Lite\'s required version of <strong>4.0.0</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>.', 'soliloquy' ), esc_url( get_admin_url() ) ) );
	}

	if ( is_multisite() && $network_wide ) {
		global $wpdb;

		// LIMIT AND OFFSET THIS UNTIL 0 VALUES.
		$site_list = get_sites();
		foreach ( (array) $site_list as $site ) {
			switch_to_blog( $site->blog_id );

			// Set the upgraded licenses since this is an activation and no slider will have existed yet.
			update_option( 'soliloquy_upgrade', true );

			restore_current_blog();

		}
	} else {

		// Set the upgraded licenses since this is an activation and no slider will have existed yet.
		update_option( 'soliloquy_upgrade', true );

	}

	$over_time = get_option( 'soliloquy_over_time', [] );
	if ( empty( $over_time['installed_lite'] ) ) {
		$over_time['installed_lite'] = wp_date( 'U' );
		update_option( 'soliloquy_over_time', $over_time );
	}
}

// Load the main plugin class.
$soliloquy_lite = Soliloquy_Lite::get_instance();

// Conditionally load the template tag.
if ( ! function_exists( 'soliloquy' ) ) {
	/**
	 * Primary template tag for outputting Soliloquy sliders in templates.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $id     The ID of the slider to load.
	 * @param string $type   The type of field to query.
	 * @param array  $args   Associative array of args to be passed.
	 * @param bool   $html Flag to echo or return the slider HTML.
	 * @return string|void The slider HTML or void if print.
	 */
	function soliloquy( $id, $type = 'id', $args = [], $html = false ) {

		// If we have args, build them into a shortcode format.
		$args_string = '';
		if ( ! empty( $args ) ) {
			foreach ( (array) $args as $key => $value ) {
				$args_string .= ' ' . $key . '="' . $value . '"';
			}
		}

		// Build the shortcode.
		$shortcode = ! empty( $args_string ) ? '[soliloquy ' . $type . '="' . $id . '"' . $args_string . ']' : '[soliloquy ' . $type . '="' . $id . '"]';

		// Return or echo the shortcode output.
		if ( $html ) {
			return do_shortcode( $shortcode );
		} else {
			echo do_shortcode( $shortcode );
		}
	}
}

// For backwards compat, load the v1 template tag if it doesn't exist.
if ( ! function_exists( 'soliloquy_slider' ) ) {
	/**
	 * Primary template tag for outputting Soliloquy sliders in templates (v1).
	 *
	 * @since 1.0.0
	 *
	 * @param int  $id     The ID of the slider to load.
	 * @param bool $html Flag to echo or return the slider HTML.
	 */
	function soliloquy_slider( $id, $html = false ) {

		// First test to see if the slider can be found by ID. If so, run that.
		$by_id = Soliloquy_Lite::get_instance()->get_slider( $id );
		if ( $by_id ) {
			return soliloquy( $id, 'id', [], $html );
		}

		// If not by ID, it must be a slug, so return the slug.
		return soliloquy( $id, 'slug', [], $html );
	}
}

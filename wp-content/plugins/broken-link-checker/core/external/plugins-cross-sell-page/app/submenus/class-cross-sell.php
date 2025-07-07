<?php
/**
 * Admin page controller for free Plugins Cross Sell.
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\Modules\App\Submenus
 *
 * @copyright (c) 2025, Incsub (http://incsub.com)
 */

namespace WPMUDEV\Modules\Plugin_Cross_Sell\App\Submenus;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV\Modules\Plugin_Cross_Sell\Container;

/**
 *  Class CrossSell
 */
class CrossSell {
	/**
	 * The page title.
	 *
	 * @var string
	 */
	private $page_title;

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	private $page_slug = 'wpmudev_plugins_cross_sell';

	/**
	 * Submenu params.
	 *
	 * @var array
	 */
	private $submenu_params = array(
		'parent_slug' => '',
		'page_title'  => '',
		'menu_title'  => '',
		'capability'  => '',
		'menu_slug'   => '',
		'position'    => '',
	);

	/**
	 * Page Assets.
	 *
	 * @var array
	 */
	private $page_scripts = array();

	/**
	 * Assets version.
	 *
	 * @var string
	 */
	private $assets_version = '';

	/**
	 * A unique string id to be used in markup and jsx.
	 *
	 * @var string
	 */
	private $unique_id = '';

	/**
	 * Utilities object.
	 *
	 * @since 1.0.0
	 *
	 * @var Utilities
	 */
	protected $utilities = null;

	/**
	 * The translation directory of the plugin.
	 * 
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $translation_dir = '';

	/**
	 * Initializes the page.
	 *
	 * @param Container $container The dependency container.
	 * @return void
	 * @since 1.0.0
	 */
	public function init( Container $container ): void {
		$this->submenu_params  = $container->get( 'submenu_data' );
		$this->assets_version  = ! empty( $this->script_data( 'version' ) ) ? $this->script_data( 'version' ) : WPMUDEV_MODULE_PLUGIN_CROSS_SELL_VERSION;
		$this->unique_id       = "wpmudev-cross-sell-container-{$this->assets_version}";
		$this->page_slug       = $this->submenu_params['menu_slug'];
		$this->translation_dir = $this->submenu_params['translation_dir'] ?? '';
		$this->utilities       = $container->get( 'utilities' );
		$menu_hook_priority    = ! empty( $this->submenu_params['menu_hook_priority'] ) ? intval( $this->submenu_params['menu_hook_priority'] ) : 10;

		if ( ! $this->utilities instanceof \WPMUDEV\Modules\Plugin_Cross_Sell\Utilities ) {
			$this->utilities = new \WPMUDEV\Modules\Plugin_Cross_Sell\Utilities;
		}
		
		// Enqueue assets.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		// Add body class to admin pages.
		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ) );

		
		// Register the submenu.
		// On multisites pluins can be installed only from network admin so it shouldn't be shown on subsites.
		if ( ! is_multisite() ) {
			add_action( 'admin_menu', array( $this, 'register_submenu' ), $menu_hook_priority );
		} else {
			if ( is_network_admin() ) {
				add_action( 'network_admin_menu', array( $this, 'register_submenu' ), $menu_hook_priority );
			}
		}
	}

	/**
	 * Actions (not necessary hooks) that should be executed on specific admin menu's page load.
	 * 
	 * @param string $text The text to be displayed in the footer.
	 * 
	 * @return void
	 */
	public function internal_admin_actions( $text = '' ): void {
		// Prepare assets used for specific admin menu page.
		$this->prepare_assets();

		// Suppress admin notices.
		$this->suppress_admin_notices();

		// Remove the footer text.
		add_filter( 'admin_footer_text', array( $this, 'rm_footer_text' ) );

		// Remove the footer version.
		add_filter( 'update_footer', array( $this, 'rm_footer_verion' ) );

		// Hide the WP footer.
		add_filter( 'admin_footer_text', array( $this, 'hide_wp_footer' ) );
	}


	/**
	 * Prepares assets.
	 *
	 * @return void
	 */
	public function prepare_assets(): void {
		if ( ! is_array( $this->page_scripts ) ) {
			$this->page_scripts = array();
		}

		$current_plugin_slug = $this->submenu_params['slug'];
		$free_plugins        = $this->utilities->get_free_plugins();
		$pro_plugins         = $this->utilities->get_pro_plugins();
		$handle              = 'wpmudev_plugin_cross_sell';
		$script_suffix       = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$src                 = WPMUDEV_MODULE_PLUGIN_CROSS_SELL_ASSETS_URL . '/js/crosssellpage' . $script_suffix . '.js';
		$style_src           = WPMUDEV_MODULE_PLUGIN_CROSS_SELL_ASSETS_URL . '/css/crosssellpage' . $script_suffix . '.css';
		$dependencies        = ! empty( $this->script_data( 'dependencies' ) )
			? $this->script_data( 'dependencies' )
			: array(
				'react',
				'wp-element',
				'wp-i18n',
				'wp-is-shallow-equal',
				'wp-polyfill',
			);

		$this->page_scripts[ $handle ] = array(
			'src'       => $src,
			'style_src' => $style_src,
			'deps'      => $dependencies,
			'ver'       => $this->assets_version,
			'strategy'  => true,
			'localize'  => array(
				'nonce'                  => wp_create_nonce( 'wpmudev_plugin_cross_sell_nonce' ),
				'dom_element_id'         => $this->unique_id,
				'current_slug'           => $current_plugin_slug, // The slug of the current plugin. Can be used in rest requests to fetch filtered plugin list in case it is needed.
				'restEndpointGetPlugins' => 'wpmudev_pcs/v1/plugincrosssell/get_plugins', // Gets the list of plugins. The param need include the current plugin slug and type (free|pro).
				'restEndpointInstall'    => 'wpmudev_pcs/v1/plugincrosssell/install_plugin', // The endpoint to install a plugin.
				'restEndpointActivate'   => 'wpmudev_pcs/v1/plugincrosssell/activate_plugin', // The endpoint to activate a plugin.
				'free_plugins'           => ! empty( $free_plugins ) ? $this->filter_plugins_list( $free_plugins ) : array(),
				'pro_plugins'            => ! empty( $pro_plugins ) ? $this->filter_plugins_list( $pro_plugins ) : array(),
				'utmSource'              => $this->get_utm_source(),
			),
		);
	}

	/**
	 * Removes the footer text.
	 * 
	 * @param string $text The text to be displayed in the footer.
	 *
	 * @return string
	 */
	public function rm_footer_text( $text = '' ) {
		return '';
	}

	/**
	 * Removes the footer version.
	 * 
	 * @param string $text The text/version to be displayed in the footer.
	 *
	 * @return string
	 */
	public function rm_footer_verion( $text = '' ) {
		return '';
	}

	/**
	 * Hides the WP footer.
	 * 
	 * @param string $text The text to be displayed in the footer.
	 *
	 * @return string
	 */
	public function hide_wp_footer( $text = '' ) {
		return '';
	}

	/**
	 * Suppresses admin notices.
	 *
	 * @return void
	 */
	public function suppress_admin_notices() {
		// Save the core update notices before removing all notices.
		//$core_update_notice      = has_action( 'admin_notices', 'update_nag' );
		//$core_maintenance_notice = has_action( 'admin_notices', 'maintenance_nag' );

		// Remove all admin notices.
		remove_all_actions( 'admin_notices' );

		// Re-add critical WordPress core notices
		//if ( $core_update_notice ) {
		//	add_action( 'admin_notices', 'update_nag' );
		//}

		//if ( $core_maintenance_notice ) {
		//	add_action( 'admin_notices', 'maintenance_nag' );
		//}
	}

	/**
	 * Registers the submenu.
	 *
	 * @return string|bool The resulting page’s hook_suffix, or false if the user does not have the capability required.
	 * @since 1.0.0
	 */
	public function register_submenu() {
		$default_params = array(
			'parent_slug' => '',
			'page_title'  => __( 'More free Plugins?', 'plugin-cross-sell-textdomain' ),
			'menu_title'  => __( 'More free Plugins?', 'plugin-cross-sell-textdomain' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'plugins_cross_sell',
			'position'    => 10,
		);

		$submenu_params = $this->utilities->validate_schema( $this->submenu_params, $this->get_submenu_schema() ) ? wp_parse_args( $this->submenu_params, $default_params ) : $default_params;

		if ( empty( $submenu_params['parent_slug'] ) ) {
			return false;
		}

		if ( is_network_admin() ) {
			$submenu_params['capability'] = 'manage_network_options';
		}

		// The position param was added in WP version 5.3. Mentioned in docs : https://developer.wordpress.org/reference/functions/add_submenu_page/.
		if ( version_compare( get_bloginfo( 'version' ), '5.3', '>=' ) ) {
			$page = add_submenu_page(
				esc_html( $submenu_params['parent_slug'] ),
				esc_html( $submenu_params['page_title'] ),
				esc_html( $submenu_params['menu_title'] ),
				esc_html( $submenu_params['capability'] ),
				esc_html( $submenu_params['menu_slug'] ),
				array( $this, 'callback' ),
				intval( $submenu_params['position'] )
			);
		} else {
			$page = add_submenu_page(
				esc_html( $submenu_params['parent_slug'] ),
				esc_html( $submenu_params['page_title'] ),
				esc_html( $submenu_params['menu_title'] ),
				esc_html( $submenu_params['capability'] ),
				esc_html( $submenu_params['menu_slug'] ),
				array( $this, 'callback' )
			);
		}

		add_action( 'load-' . $page, array( $this, 'internal_admin_actions' ) );

		return $page;
	}

	/**
	 * Retrieves the submenu schema, which includes all parameters for adding a submenu.
	 *
	 * @return array An associative array representing the submenu schema.
	 * @since 1.0.0
	 */
	private function get_submenu_schema(): array {
		return array(
			'slug'        => 'string',
			'parent_slug' => 'string',
			'page_title'  => 'string',
			'menu_title'  => 'string',
			'capability'  => 'string',
			'menu_slug'   => 'string',
			'position'    => 'int',
		);
	}

	/**
	 * The admin page callback method.
	 *
	 * @return void
	 */
	public function callback(): void {
		$this->view();
	}

	protected function get_utm_source(): string {
		static $utm_source = null;

		if ( empty( $utm_source ) ) {
			// Only the Free plugins will show the cross sell page. 
			// As we're interested in the current plugin's utm_source we'll be checking free plugins list.
			$plugins    = $this->utilities->get_free_plugins();
			$utm_source = $this->submenu_params['utm_source'] ?? '';

			if ( empty( $utm_source ) ) {
				$current_plugin_slug = $this->submenu_params['slug'];
				$current_plugin      = isset( $plugins[ $current_plugin_slug ] ) ? $plugins[ $current_plugin_slug ] : array();
				$utm_source          = ! empty( $current_plugin['utm_source'] ) ? $current_plugin['utm_source'] : '';

				$matched_plugins = array_filter( $plugins, function ($plugin) use ($current_plugin_slug) {
					return strpos( $plugin['slug'], $current_plugin_slug ) !== false;
				} );

				$current_plugin = reset( $matched_plugins );
				$utm_source     = ! empty( $current_plugin['utm_source'] ) ? $current_plugin['utm_source'] : '';
			}
		}

		return $utm_source;
	}

	/**
	 * Filters the plugins list.
	 *
	 * @param array $plugins Plugins list.
	 * @return array
	 */
	private function filter_plugins_list( array $plugins = array() ): array {
		$current_plugin_slug = $this->submenu_params['slug'];
		$utm_source          = $this->get_utm_source();

		if ( empty( $plugins ) || empty( $current_plugin_slug ) ) {
			return $plugins;
		}

		foreach ( $plugins as $key => $plugin ) {
			// Remove the current plugin from the list and if the slug is empty.
			if ( empty( $plugin['slug'] ) || $plugin['slug'] === $current_plugin_slug ) {
				unset( $plugins[ $key ] );
				continue;
			}

			// We need to check if the plugin is installed and active.
			if ( ! empty( $plugin['path'] ) ) {
				$plugins[ $key ]['installed'] = $this->utilities->is_plugin_installed( $plugin['path'] );
				$plugins[ $key ]['active']    = is_plugin_active( $plugin['path'] );
			}

			if ( ! empty( $plugin['admin_url_page'] ) ) {
				$plugins[ $key ]['admin_url'] = admin_url( 'admin.php?page=' . $plugin['admin_url_page'] );
			} else {
				$plugins[ $key ]['admin_url'] = '';
			}

			/* phpcs:disable Universal.WhiteSpace.DisallowInlineTabs.NonIndentTabsUsed, Squiz.PHP.CommentedOutCode.Found, Squiz.Commenting.InlineComment.NoSpaceBefore, Squiz.Commenting.InlineComment.NoSpaceBefore, Squiz.Commenting.InlineComment.NoSpaceBefore, Squiz.Commenting.InlineComment.InvalidEndChar, Squiz.Commenting.InlineComment.SpacingBefore */
			//Get the plugin stats from remote api to make sure we are up to date.
			//if ( ! empty( $plugin['rating'] ) || ! empty( $plugin['active_installs'] ) ) {
			// We will replace this with front end api calls per plugin.
			//$plugin_stats = Utilities::get_plugin_stats( $plugin['slug'] );

			//if ( ! empty( $plugin_stats ) ) {
			//	$plugins[ $key ]['rating']          = ! empty( $plugin_stats['rating'] ) ? $plugin_stats['rating'] : ( ! empty( $plugin['rating'] ) ? $plugin['rating'] : 0 );
			//	$plugins[ $key ]['active_installs'] = ! empty( $plugin_stats['active_installs'] ) ? $plugin_stats['active_installs'] : ( ! empty( $plugin['active_installs'] ) ? $plugin['active_installs'] : 0 );
			//}
			//}
			/* phpcs:enable Universal.WhiteSpace.DisallowInlineTabs.NonIndentTabsUsed, Squiz.PHP.CommentedOutCode.Found, Squiz.Commenting.InlineComment.NoSpaceBefore, Squiz.Commenting.InlineComment.NoSpaceBefore, Squiz.Commenting.InlineComment.NoSpaceBefore, Squiz.Commenting.InlineComment.InvalidEndChar, Squiz.Commenting.InlineComment.SpacingBefore */

			// Set the plugin's full url for logo.
			$plugins[ $key ]['logo'] = ! empty( $plugin['logo'] ) ? WPMUDEV_MODULE_PLUGIN_CROSS_SELL_URL . 'assets/images/' . $plugin['logo'] : '';

			// Add UTM to plugin's url.
			if ( ! empty( $plugin['url'] ) && ! empty( $utm_source ) ) {
				$plugin_utm_campaign = ! empty( $plugin['utm_campaign'] ) ? $plugin['utm_campaign'] : '';

				$plugins[ $key ]['url'] = add_query_arg( array(
					'utm_source'   => $utm_source,
					'utm_medium'   => 'plugin',
					'utm_campaign' => $plugin_utm_campaign,
					'utm_content'  => 'plugins-cross-sell'
				), $plugin['url'] );
			}
		}

		return apply_filters( 'wpmudev_cross_sell_plugins_list', $plugins );
	}

	/**
	 * Gets assets data for given key.
	 *
	 * @param string $key The requested portion of data, an array key, usually version or dependencies.
	 *
	 * @return string|array
	 */
	protected function script_data( string $key = '' ) {
		$raw_script_data = $this->raw_script_data();

		return ! empty( $key ) && ! empty( $raw_script_data[ $key ] ) ? $raw_script_data[ $key ] : $raw_script_data;
	}

	/**
	 * Gets the script data from assets php file.
	 *
	 * @return array
	 */
	protected function raw_script_data(): array {
		static $script_data = null;

		if ( is_null( $script_data ) && file_exists( WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR . 'assets/js/crosssellpage.asset.php' ) ) {
			$script_data = include WPMUDEV_MODULE_PLUGIN_CROSS_SELL_DIR . 'assets/js/crosssellpage.asset.php';
		}

		return (array) $script_data;
	}

	/**
	 * Prepares assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! empty( $this->page_scripts ) ) {
			foreach ( $this->page_scripts as $handle => $page_script ) {
				wp_register_script(
					$handle,
					$page_script['src'],
					$page_script['deps'],
					$page_script['ver'],
					$page_script['strategy']
				);

				if ( ! empty( $page_script['localize'] ) ) {
					wp_localize_script( $handle, 'wpmudev_cross_sell_data', $page_script['localize'] );
				}

				wp_enqueue_script( $handle );
				// Set script translation.
				// We need to set the script translation on admin_enqueue_scripts action to make sure the script is registered.
				add_action( 'admin_enqueue_scripts', function () use ($handle) {
					$this->set_script_translation( $handle );
				}, 999 );

				// Enqueue styles.
				if ( ! empty( $page_script['style_src'] ) ) {
					wp_enqueue_style( $handle, $page_script['style_src'], array(), $this->assets_version );
				}
			}
		}
	}

	/**
	 * Sets the script translation.
	 *
	 * @param string $handle The handle of the script.
	 *
	 * @return void
	 */
	public function set_script_translation( string $handle = '' ): void {
		if ( ! empty( $this->translation_dir ) && is_string( $this->translation_dir ) ) {
			wp_set_script_translations( $handle, 'plugin-cross-sell-textdomain', $this->translation_dir );
		} else {
			wp_set_script_translations( $handle, 'plugin-cross-sell-textdomain' );
		}
	}

	/**
	 * Prints the wrapper element which React will use as root.
	 *
	 * @return void
	 */
	protected function view() {
		echo '<div id="' . esc_attr( $this->unique_id ) . '" class="sui-wrap"></div>';
	}

	/**
	 * Adds the SUI class on markup body.
	 *
	 * @param string $classes The current body classes.
	 *
	 * @return string
	 */
	public function admin_body_classes( $classes = '' ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $classes;
		}

		if ( ! $this->is_current_page() ) {
			return $classes;
		}

		$classes .= ' sui-' . str_replace( '.', '-', WPMUDEV_MODULE_PLUGIN_CROSS_SELL_SUI_VERSION ) . ' ';

		return $classes;
	}

	protected function is_current_page(): bool {
		$current_screen = get_current_screen();
		return ! empty( $current_screen->id )
			&& ! empty( $this->page_slug )
			&& (
				str_ends_with( $current_screen->id, $this->page_slug )
				|| ( is_network_admin() && str_ends_with( $current_screen->id, $this->page_slug . '-network' ) )
			);
	}
}

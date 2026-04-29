<?php
/**
 * App class.
 *
 * @package Imagely\NGG\Admin
 */

// phpcs:disable Squiz.Commenting

namespace Imagely\NGG\Admin;

use Imagely\NGG\Admin\AMNotifications as Notifications;
use Imagely\NGG\DisplayType\LegacyTemplateLocator;

class App {
	private $hook_suffixes = [];

	public function __construct() {
		// Constructor should be empty since we register hooks in hooks()
	}

	public function hooks() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_menu', [ $this, 'add_upgrade_menu_item' ], 1000 );
		add_action( 'admin_menu', [ $this, 'add_cdn_menu_item' ], 11 );
		add_action( 'admin_head', [ $this, 'admin_inline_styles' ] );
		add_action( 'admin_footer', [ $this, 'admin_sidebar_target' ] );
	}

	public function admin_menu() {
		$notifications = new Notifications();

		// Notification count HTML to append to the menu.
		$nav_append_count = '';
		if ( absint( $notifications->get_count() ) > 0 ) {
			$nav_append_count = "<span class='ngg-menu-notification-indicator update-plugins'>" . absint( $notifications->get_count() ) . '</span>';
		}

		$menu = 'imagely';

		// Use NextGEN Gallery overview capability for main menu, matching legacy admin behavior
		$this->hook_suffixes[] = add_menu_page(
			__( 'Imagely', 'nggallery' ),
			__( 'Imagely', 'nggallery' ) . $nav_append_count,
			'NextGEN Gallery overview', // phpcs:ignore WordPress.WP.Capabilities.Unknown
			$menu,
			[ $this, 'render_settings_page' ],
			plugins_url( 'assets/images/logo-icon.png', NGG_PLUGIN_FILE ),
			10
		);

		// Use proper NextGEN capabilities for each menu item, matching legacy admin behavior
		$sub_menus = [
			[
				'name'       => __( 'Galleries', 'nggallery' ),
				'capability' => 'NextGEN Manage gallery',
				'menu_slug'  => $menu,
			],
			[
				'name'       => __( 'Add New Gallery', 'nggallery' ),
				'capability' => 'NextGEN Upload images',
				'menu_slug'  => "$menu&create_gallery=1",
			],
			[
				'name'       => __( 'Edit Gallery', 'nggallery' ),
				'capability' => 'NextGEN Manage gallery',
				'menu_slug'  => "$menu-add-new",
			],
			[
				'name'       => __( 'Albums', 'nggallery' ),
				'capability' => 'NextGEN Edit album',
				'menu_slug'  => "$menu-albums",
			],
			[
				'name'       => __( 'Tags', 'nggallery' ),
				'capability' => 'NextGEN Manage tags',
				'menu_slug'  => "$menu-tags",
			],
			[
				'name'       => __( 'Features', 'nggallery' ),
				'capability' => 'manage_options',
				'menu_slug'  => "$menu-addons",
			],
		];

		// Only add eCommerce menu item if Pro level is installed
		$pro_type = $this->get_pro_type_installed();
		if ( 'pro' === $pro_type ) {
			$sub_menus[] = [
				'name'       => __( 'eCommerce', 'nggallery' ),
				'capability' => 'NextGEN Change options',
				'menu_slug'  => "$menu-ecommerce",
			];
		}

		$sub_menus[] = [
			'name'       => __( 'Settings', 'nggallery' ),
			'capability' => 'NextGEN Change options',
			'menu_slug'  => "$menu-settings",
		];
		$sub_menus[] = [
			'name'       => __( 'Layout Settings', 'nggallery' ),
			'capability' => 'NextGEN Change style',
			'menu_slug'  => "$menu-layout-settings",
		];
		$sub_menus[] = [
			'name'       => __( 'About Us', 'nggallery' ),
			'capability' => 'NextGEN Gallery overview',
			'menu_slug'  => "$menu-about-us",
		];

		foreach ( $sub_menus as $sub_menu ) {
			$this->hook_suffixes[] = add_submenu_page(
				$menu,
				$sub_menu['name'],
				$sub_menu['name'],
				$sub_menu['capability'],
				$sub_menu['menu_slug'],
				[ $this, 'render_settings_page' ]
			);
		}

		// Hide the "Edit Gallery" menu item from the sidebar (but keep it registered)
		add_action( 'admin_head', [ $this, 'hide_edit_gallery_menu' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	/**
	 * Hide the "Edit Gallery" submenu item from the sidebar.
	 * We still register it so the page exists (for direct URL access with ID),
	 * but we don't want it to appear in the menu.
	 */
	public function hide_edit_gallery_menu() {
		global $submenu;

		if ( ! isset( $submenu['imagely'] ) ) {
			return;
		}

		foreach ( $submenu['imagely'] as $key => $item ) {
			// Remove the "imagely-add-new" submenu item
			if ( isset( $item[2] ) && 'imagely-add-new' === $item[2] ) {
				unset( $submenu['imagely'][ $key ] );
			}
			// Remove the "imagely-layout-settings" submenu item (accessible via Settings tab)
			if ( isset( $item[2] ) && 'imagely-layout-settings' === $item[2] ) {
				unset( $submenu['imagely'][ $key ] );
			}
		}
	}

	public static function is_debug() {
		// Check custom debug mode setting first
		$settings   = get_option( 'ngg_options', [] );
		$debug_mode = isset( $settings['ngg_debug_mode'] ) && $settings['ngg_debug_mode'] ? filter_var( $settings['ngg_debug_mode'], FILTER_VALIDATE_BOOLEAN ) : false;
		return $debug_mode;
	}

	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, $this->hook_suffixes, true ) ) {
			return;
		}

		// Suppress WP admin notices on Imagely App pages
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'user_admin_notices' );

		$script = require NGG_PLUGIN_DIR . 'adminApp/build/dependencies.php';

		$script['version'] = self::is_debug() ? time() : $script['version'];

		// Enqueue WordPress media scripts
		wp_enqueue_media();

		wp_enqueue_script(
			'imagely-settings-app',
			plugins_url( 'adminApp/build/index.min.js', NGG_PLUGIN_FILE ),
			$script['dependencies'],
			$script['version'],
			true
		);

		// Load translations for the adminApp script
		// WordPress will automatically load the JSON file based on the script path
		wp_set_script_translations(
			'imagely-settings-app',
			'nggallery',
			NGG_PLUGIN_DIR . 'static/I18N'
		);

		wp_localize_script(
			'imagely-settings-app',
			'imagelyApp',
			self::get_imagely_app_data()
		);

		wp_enqueue_style(
			'imagely-settings-app-styles',
			plugins_url( 'adminApp/build/style.min.css', NGG_PLUGIN_FILE ),
			[],
			$script['version']
		);

		// Unregister wp forms.css
		wp_deregister_style( 'forms-css' );
	}

	public function render_settings_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for display mode
		$embed     = isset( $_GET['embed'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['embed'] ) );
		$embed_css = $embed ? '<style>
		#adminmenumain, #wpadminbar, #screen-meta, #wpfooter, #wpbody-content > .notice, #wpbody-content > .updated, #wpbody-content > .error { display:none !important; }
		html.wp-toolbar #wpcontent { margin: 0 !important; padding:0 !important; overflow: hidden !important; }
		html.wp-toolbar { margin: 0 !important; padding:0 !important; overflow: hidden !important; }
		#wpbody-content { padding-bottom: 0 !important; }
		#wpbody, #wpbody-content { overflow: hidden !important; }
		body { overflow: hidden; background: #fff; }
		/* Ensure app fills iframe viewport */
		html, body, #wpcontent, #wpbody, #wpbody-content, .imagely-wrap, #imagely-admin-app { height: 100vh !important; }
		.imagely-wrap { margin: 0 !important; padding: 0 !important; overflow: hidden !important; }
		#imagely-admin-app { overflow-y: auto !important; -webkit-overflow-scrolling: touch; }
		</style>' : '';
		$page      = <<<HTML
{$embed_css}
<div class="imagely-wrap">
	<div id="imagely-admin-app"></div>
</div>
HTML;

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intentionally outputting HTML.
		echo $page;
	}

	/**
	 * Get imagelyApp data for wp_localize_script.
	 * Centralized method to ensure consistency across admin pages and blocks.
	 *
	 * @return array ImagelyApp data array (base fields, may be filtered/extended via 'ngg_imagely_app_data')
	 */
	public static function get_imagely_app_data() {
		$data = [
			'nonce'                    => wp_create_nonce( 'imagely-admin' ),
			'nonce_preview'            => wp_create_nonce( 'ngg_preview_shortcode' ),
			'restURL'                  => esc_url_raw( rest_url() ),
			'assetsURL'                => plugins_url( 'assets', NGG_PLUGIN_FILE ),
			'home_url'                 => esc_url_raw( get_home_url() ),
			'adminUrl'                 => esc_url_raw( admin_url() ),
			'pluginPath'               => NGG_PLUGIN_DIR,
			'plugin_url'               => esc_url_raw( trailingslashit( plugins_url( '', NGG_PLUGIN_FILE ) ) ),
			'debug'                    => self::is_debug(),
			'version'                  => class_exists( '\Imagely\NGGPro\Bootloader' ) && ! empty( \Imagely\NGGPro\Bootloader::$plugin_version )
				? \Imagely\NGGPro\Bootloader::$plugin_version
				: ( defined( 'NGG_PLUGIN_VERSION' ) ? NGG_PLUGIN_VERSION : '' ),
			'utmVersion'               => self::get_utm_version(),
			'proTypeInstalled'         => self::get_pro_type_installed(),
			'licenseData'              => self::get_license_data(),
			'enviraCdnConfig'          => self::get_cdn_config(),
			'canAccessRolesSettings'   => self::can_access_roles_settings(),
			'canAccessLicenseSettings' => self::can_access_license_settings(),
			'legacyTemplates'          => self::get_legacy_templates(),
		];

		return apply_filters( 'ngg_imagely_app_data', $data );
	}

	/**
	 * Get available legacy templates organized by display type prefix.
	 *
	 * @return array Legacy templates grouped by prefix (gallery, imagebrowser, album, singlepic)
	 */
	public static function get_legacy_templates() {
		$locator  = LegacyTemplateLocator::get_instance();
		$prefixes = [ 'gallery', 'imagebrowser', 'album', 'singlepic' ];

		$templates = [];

		foreach ( $prefixes as $prefix ) {
			$templates[ $prefix ] = [];

			// Add default option first.
			$templates[ $prefix ]['default'] = __( 'Default', 'nggallery' );

			// Get templates for this prefix.
			$found = $locator->find_all( $prefix );

			foreach ( $found as $label => $files ) {
				foreach ( $files as $file ) {
					$filename                      = basename( $file );
					$templates[ $prefix ][ $file ] = "{$label}: {$filename}";
				}
			}
		}

		return $templates;
	}

	/**
	 * Returns the installed NextGen Pro type (pro, plus, starter, or lite), not the license status.
	 */
	public static function get_pro_type_installed() {
		if ( class_exists( '\Imagely\NGGPro\Bootloader' ) ) {
			return \Imagely\NGGPro\Bootloader::$plugin_id;
		}
		return 'lite';
	}

	/**
	 * Get current license data to expose to JavaScript.
	 * This mirrors the data structure from the license-actions/current endpoint.
	 *
	 * @return array License data array
	 */
	public static function get_license_data() {
		$license_data = [
			'license_key'  => '',
			'status'       => 'free',
			'level'        => 'free',
			'last_check'   => 0,
			'plugin_level' => 'free',
			'is_valid'     => false,
			'expiration'   => null,
			'expires_soon' => false,
		];

		// Only get license data if we're not on the free version
		$pro_type = self::get_pro_type_installed();
		if ( 'lite' === $pro_type ) {
			return $license_data;
		}

		// Get license using the same method as the REST endpoint
		if ( class_exists( '\Imagely\NGGPro\License\Manager' ) ) {
			$licensing       = new \Imagely\NGGPro\License\Manager();
			$current_product = $licensing::get_current_product();
			$license_key     = $licensing->get_license( $current_product );

			if ( ! empty( $license_key ) ) {
				$license_data['license_key'] = $license_key;

				// Get plugin level
				$license_data['plugin_level'] = $pro_type;

				// Get license status
				$status = get_option( 'ngg_license_status_' . $pro_type, '' );
				if ( ! empty( $status ) ) {
					$license_data['status']   = $status;
					$license_data['is_valid'] = in_array( $status, [ 'active', 'valid' ], true );
				}

				// Get license level
				$level = get_option( 'ngg_license_level_' . $pro_type, '' );
				if ( ! empty( $level ) ) {
					$license_data['level'] = $level;
				}

				// Get last check time
				$last_check                 = get_option( 'ngg_last_license_check', 0 );
				$license_data['last_check'] = (int) $last_check;

				// Get license expiration
				$expiration = get_option( 'ngg_license_expiration_' . $pro_type, '' );
				if ( ! empty( $expiration ) ) {
					if ( 'lifetime' === $expiration ) {
						$license_data['expiration']   = 'lifetime';
						$license_data['expires_soon'] = false;
					} elseif ( is_numeric( $expiration ) ) {
						$license_data['expiration'] = (int) $expiration;
						// Check if license expires within 30 days
						$thirty_days_from_now         = time() + ( 30 * DAY_IN_SECONDS );
						$license_data['expires_soon'] = ( $expiration < $thirty_days_from_now && $expiration > time() );
					}
				}
			}
		}

		return $license_data;
	}

	/**
	 * Get system information for the admin app.
	 *
	 * @return array System information array
	 */
	public static function get_system_info() {
		global $wp_version, $wpdb;

		$settings = \Imagely\NGG\Settings\Settings::get_instance();

		// Get database info
		$db_server_info = method_exists( $wpdb, 'db_server_info' ) ? $wpdb->db_server_info() : '';
		$mysql_type     = stripos( $db_server_info, 'mariadb' ) !== false ? 'MariaDB' : 'MySQL';
		$mysql_version  = method_exists( $wpdb, 'db_version' ) ? $wpdb->db_version() : 'Unknown';

		// Get upload directory info
		$upload_dir = wp_upload_dir();

		$system_info = [
			// Existing fields
			'php_version'              => phpversion(),
			'wordpress_version'        => $wp_version,
			'memory_limit'             => ini_get( 'memory_limit' ),
			'max_upload_size'          => size_format( wp_max_upload_size() ),
			'server_software'          => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : 'Unknown',
			'plugin_version'           => defined( 'NGG_PLUGIN_VERSION' ) ? NGG_PLUGIN_VERSION : 'Unknown',
			'gd_version'               => self::get_gd_version(),
			'imagick_version'          => self::get_imagick_version(),

			// Server Environment
			'php_os'                   => PHP_OS,
			'php_sapi'                 => php_sapi_name(),
			'mysql_version'            => $mysql_version,
			'mysql_type'               => $mysql_type,
			'curl_version'             => function_exists( 'curl_version' ) ? curl_version()['version'] : 'Not available',
			'openssl_version'          => defined( 'OPENSSL_VERSION_TEXT' ) ? OPENSSL_VERSION_TEXT : 'Not available',

			// PHP Extensions
			'exif_enabled'             => function_exists( 'exif_read_data' ) ? 'Yes' : 'No',
			'iptc_enabled'             => function_exists( 'iptcparse' ) ? 'Yes' : 'No',
			'mbstring_enabled'         => extension_loaded( 'mbstring' ) ? 'Yes' : 'No',
			'zip_enabled'              => extension_loaded( 'zip' ) ? 'Yes' : 'No',
			'fileinfo_enabled'         => extension_loaded( 'fileinfo' ) ? 'Yes' : 'No',

			// PHP Configuration
			'post_max_size'            => ini_get( 'post_max_size' ),
			'max_execution_time'       => ini_get( 'max_execution_time' ),
			'max_input_vars'           => ini_get( 'max_input_vars' ),
			'upload_max_filesize'      => ini_get( 'upload_max_filesize' ),
			'allow_url_fopen'          => ini_get( 'allow_url_fopen' ) ? 'Yes' : 'No',

			// WordPress Configuration
			'wp_debug'                 => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Yes' : 'No',
			'wp_debug_log'             => defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? 'Yes' : 'No',
			'wp_memory_limit'          => defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : 'Not set',
			'site_url'                 => site_url(),
			'home_url'                 => home_url(),
			'is_multisite'             => is_multisite() ? 'Yes' : 'No',
			'active_theme'             => wp_get_theme()->get( 'Name' ) . ' ' . wp_get_theme()->get( 'Version' ),

			// NextGen Specific Settings
			'ngg_options_version'      => get_option( 'ngg_options_version', 'Not set' ),
			'image_library_preference' => $settings->get( 'imgLibrary', 'gd' ),
			'thumbnail_quality'        => $settings->get( 'thumbquality', '100' ),
			'backup_images'            => $settings->get( 'imgBackup', false ) ? 'Yes' : 'No',
			'galleries_count'          => wp_count_posts( 'ngg_gallery' )->publish ?? 0,
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			'images_count'             => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ngg_pictures" ) ?? 0,

			// Server Paths
			'wp_content_dir'           => WP_CONTENT_DIR,
			'upload_dir'               => $upload_dir['basedir'],
			'ngg_gallery_path'         => $settings->get( 'gallerypath', 'Not set' ),

			// Additional Settings
			'timezone'                 => wp_timezone_string(),
			'locale'                   => get_locale(),
			'permalink_structure'      => get_option( 'permalink_structure', 'Default' ),

			// NextGen Legacy Settings
			'show_legacy_admin_pages'  => $settings->get( 'ngg_show_old_settings', false ) ? 'Yes' : 'No',
			'activate_legacy_block'    => $settings->get( 'ngg_installation_type', 'fresh' ) === 'existing' ? 'Yes' : 'No',
		];

		return $system_info;
	}

	/**
	 * Get GD version information.
	 *
	 * @return string GD version or 'Not available'
	 */
	private static function get_gd_version() {
		if ( ! function_exists( 'gd_info' ) ) {
			return 'Not available';
		}

		$gd_info = gd_info();
		return isset( $gd_info['GD Version'] ) ? $gd_info['GD Version'] : 'Not available';
	}

	/**
	 * Get ImageMagick version information.
	 *
	 * @return string ImageMagick version or 'Not available'
	 */
	private static function get_imagick_version() {
		if ( ! class_exists( 'Imagick' ) ) {
			return 'Not available';
		}

		try {
			// phpcs:ignore PHPCompatibility.Classes.NewClasses.imagickFound
			$imagick = new \Imagick();
			$version = $imagick->getVersion();
			return isset( $version['versionString'] ) ? $version['versionString'] : 'Not available';
		} catch ( \Exception $e ) {
			return 'Not available';
		}
	}

	/**
	 * Get CDN plugin configuration if active.
	 *
	 * @return array|null CDN configuration or null if not active
	 */
	private static function get_cdn_config() {
		// Check if Envira CDN plugin is active.
		if ( ! class_exists( 'Envira_CDN' ) ) {
			return null;
		}

		// Check if CDN is enabled globally.
		$cdn_config = get_option( 'envira_cdn_config', [] )['envira_cdn_config'] ?? [];

		$enabled = isset( $cdn_config['enable'] ) ? $cdn_config['enable'] : false;

		// Check if license is valid.
		$license_valid = false;
		if ( class_exists( 'Envira\CDN\Utils\Common' ) ) {
			$license_key   = \Envira\CDN\Utils\Common::get_license_key();
			$license_valid = ! empty( $license_key );
		}

		return [
			'enabled'       => $enabled,
			'license_valid' => $license_valid,
		];
	}

	/**
	 * Check if current user can access roles and capabilities settings.
	 *
	 * Single site: same as before — allow when user is super admin (effectively administrator).
	 * Multisite: super admin always; otherwise site admins only when network has
	 * "Enable roles/capabilities" (wpmuRoles) and user has manage_options.
	 *
	 * @return bool
	 */
	public static function can_access_roles_settings() {
		if ( ! is_multisite() ) {
			return is_super_admin();
		}

		if ( is_super_admin() ) {
			return true;
		}

		$wpmu_roles = (bool) \Imagely\NGG\Settings\GlobalSettings::get_instance()->get( 'wpmuRoles' );
		return $wpmu_roles && current_user_can( 'manage_options' );
	}

	/**
	 * Check if current user can access license settings.
	 *
	 * @return bool
	 */
	public static function can_access_license_settings() {
		if ( ! is_multisite() ) {
			return current_user_can( 'manage_options' );
		}

		return is_super_admin();
	}

	/**
	 * Add lite-specific upgrade to pro menu item.
	 *
	 * @since 3.6.0
	 * @return void
	 */
	public function add_upgrade_menu_item() {
		global $submenu;

		// Only add upgrade menu item for lite version (hide for pro, plus, and starter)
		$pro_type = $this->get_pro_type_installed();
		if ( 'lite' !== $pro_type ) {
			return;
		}

		// Use same capability as main menu
		add_submenu_page(
			'imagely',
			esc_html__( 'Upgrade to Pro', 'nggallery' ),
			esc_html__( 'Upgrade to Pro', 'nggallery' ),
			'NextGEN Gallery overview', // phpcs:ignore WordPress.WP.Capabilities.Unknown
			esc_url( $this->get_utm_link( 'https://www.imagely.com/lite/', 'adminsidebar', 'unlockprosidebar' ) )
		);

		if ( ! current_user_can( 'NextGEN Gallery overview' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
			return;
		}

		// Find the upgrade menu item and add CSS class for styling
		if ( ! isset( $submenu['imagely'] ) ) {
			return;
		}

		$upgrade_link_position = key(
			array_filter(
				$submenu['imagely'],
				static function ( $item ) {
					return isset( $item[2] ) && str_contains( (string) $item[2], 'https://www.imagely.com/lite/' );
				}
			)
		);

		if ( null === $upgrade_link_position ) {
			return;
		}

		// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( isset( $submenu['imagely'][ $upgrade_link_position ][4] ) ) {
			$submenu['imagely'][ $upgrade_link_position ][4] .= ' imagely-sidebar-upgrade-pro';
		} else {
			$submenu['imagely'][ $upgrade_link_position ][] = 'imagely-sidebar-upgrade-pro';
		}
		// phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	/**
	 * Add CDN menu item.
	 *
	 * @return void
	 */
	public function add_cdn_menu_item() {
		global $submenu;
		$utm = '?utm_source=imagely&utm_medium=admin-menu&utm_campaign=imagely-cdn&utm_content=' . self::get_utm_version();
		// Only add if the submenu exists.
		if ( isset( $submenu['imagely'] ) ) {
			$submenu['imagely'][] = [
				// Use HTML in the menu title, but be aware some WP versions may not render it.
				'Imagely CDN <span class="imagely_cdn_new_badge">NEW</span>',
				'manage_options',
				'https://www.imagely.com/cdn/' . $utm,
			];
		}
	}

	/**
	 * Add inline styles for upgrade menu item.
	 *
	 * @since 3.6.0
	 * @return void
	 */
	public function admin_inline_styles() {
		$pro_type = $this->get_pro_type_installed();
		if ( 'lite' !== $pro_type ) {
			return;
		}

		echo '<style>
			.imagely-sidebar-upgrade-pro {
				background-color: #37993B;
			}
			.imagely-sidebar-upgrade-pro a {
				color: #fff !important;
			}
		</style>';
	}

	/**
	 * Make upgrade menu link open in new tab.
	 *
	 * @since 3.6.0
	 * @return void
	 */
	public function admin_sidebar_target() {
		$pro_type = $this->get_pro_type_installed();
		if ( 'lite' !== $pro_type ) {
			return;
		}

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('li.imagely-sidebar-upgrade-pro a').attr('target','_blank');
		});
		</script>
		<?php
	}

	/**
	 * Returns the utm_content version string formatted as {plan}_{version}.
	 * Uses the installed Pro/Plus/Starter version when available, otherwise Lite.
	 *
	 * Examples: lite_4.0.6-0, plus_2.0.4-0, pro_3.0.0, starter_1.0.0
	 *
	 * @return string
	 */
	public static function get_utm_version(): string {
		$pro_type = self::get_pro_type_installed();
		if ( 'lite' !== $pro_type && class_exists( '\Imagely\NGGPro\Bootloader' ) && ! empty( \Imagely\NGGPro\Bootloader::$plugin_version ) ) {
			return $pro_type . '_' . \Imagely\NGGPro\Bootloader::$plugin_version;
		}
		return 'lite_' . ( defined( 'NGG_PLUGIN_VERSION' ) ? NGG_PLUGIN_VERSION : '' );
	}

	/**
	 * Get UTM link for marketing URLs.
	 *
	 * @since 3.6.0
	 * @param string $url Base URL.
	 * @param string $medium UTM medium parameter.
	 * @param string $campaign UTM campaign parameter.
	 * @param string $source UTM source parameter.
	 * @param string $content UTM content parameter. Defaults to get_utm_version().
	 * @return string URL with UTM parameters.
	 */
	private function get_utm_link( $url, $medium = 'default', $campaign = 'default', $source = 'ngg', $content = '' ) {
		$params = apply_filters(
			'ngg_marketing_parameters',
			[
				'url'      => $url,
				'medium'   => $medium,
				'campaign' => $campaign,
				'source'   => $source,
				'content'  => ! empty( $content ) ? $content : self::get_utm_version(),
			]
		);

		$url .= '?utm_source=' . $params['source'];
		$url .= '&utm_medium=' . $params['medium'];
		$url .= '&utm_campaign=' . $params['campaign'];
		if ( ! empty( $params['content'] ?? '' ) ) {
			$url .= '&utm_content=' . $params['content'];
		}

		return $url;
	}
}

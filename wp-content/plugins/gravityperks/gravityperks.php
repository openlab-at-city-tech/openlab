<?php
/**
 * Plugin Name: Gravity Perks
 * Plugin URI: https://gravitywiz.com/
 * Description: Effortlessly install and manage small functionality enhancements (aka "perks") for Gravity Forms.
 * Version: 2.2.9
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Text Domain: gravityperks
 * Domain Path: /languages
 * Update URI: https://gravitywiz.com/updates/gravityperks
 */

define( 'GRAVITY_PERKS_VERSION', '2.2.9' );

/**
 * Include the perk model as early as possible to when Perk plugins are loaded, they can safely extend
 * the GWPerk class.
 */
require_once( plugin_dir_path( __FILE__ ) . 'model/perk.php' );

add_action( 'init', array( 'GravityPerks', 'init' ) );
add_action( 'gform_loaded', array( 'GravityPerks', 'init_perk_as_plugin_functionality' ) );

class GravityPerks {

	public static $version          = GRAVITY_PERKS_VERSION;
	public static $tooltip_template = '<h6>%s</h6> %s';

	private static $basename;
	private static $slug                      = 'gravityperks';
	private static $min_gravity_forms_version = '2.2';
	private static $min_wp_version            = '4.8';
	/**
	* @var GWAPI
	*/
	private static $api;

	/**
	* TODO: review...
	*
	* Need to store a modified version of the form object based the on the gform_admin_pre_render hook for use
	* in perk hooks.
	*
	* Example usage: GWPreventSubmit::add_form_setting()
	*
	* @var array
	*/
	public static $form;

	/**
	* Set to true by the GWPerk class when any perk enqueues a form setting via the
	* GWPerk::enqueue_form_setting() function
	*
	* @var bool
	*/
	public static $has_form_settings;

	/**
	* Set to true by the GWPerk class when any perk enqueues a field setting via the
	* GWPerk::enqueue_field_setting() function.
	*
	* @var bool
	*/
	private static $has_field_settings;

	/**
	* When displaying a plugin row message, the first message display will also output a small style to fix the bottom
	* border styling issue which WP handles for plugins with updates, but not with notices.
	*
	* @see self::display_plugin_row_message()
	*
	* @var mixed
	*
	*/
	private static $plugin_row_styled;

	// CACHE VARIABLES //

	private static $installed_perks;



	// INITIALIZE //

	public static function init() {

		self::define_constants();
		self::$basename = plugin_basename( __FILE__ );

		require_once( self::get_base_path() . '/includes/functions.php' );
		require_once( self::get_base_path() . '/includes/deprecated.php' );

		load_plugin_textdomain( 'gravityperks', false, basename( dirname( __file__ ) ) . '/languages/' );

		if ( ! self::is_gravity_forms_supported() ) {
			self::handle_error( 'gravity_forms_required' );
		} elseif ( ! self::is_wp_supported() ) {
			self::handle_error( 'wp_required' );
		}

		self::maybe_setup();
		self::load_api();

		self::register_scripts();

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			global $pagenow;

			self::include_admin_files();

			// enqueue welcome pointer script
			add_action( 'admin_enqueue_scripts', array( 'GWPerks', 'welcome_pointer' ) );

			// show Perk item in GF menu
			add_filter( 'gform_addon_navigation', array( 'GWPerks', 'add_menu_item' ) );

			// show various plugin messages after the plugin row
			add_action( 'after_plugin_row_' . self::$basename, array( 'GWPerks', 'after_plugin_row' ), 10, 2 );
			add_action( 'after_plugin_row', array( 'GWPerks', 'after_perk_plugin_row' ), 10, 2 );

			if ( self::is_gravity_perks_page() ) {

				if ( rgget( 'view' ) ) {
					require_once( self::get_base_path() . '/admin/manage_perks.php' );
					GWPerksPage::load_perk_settings();
				}

				add_thickbox();

			}

			if ( self::is_gf_version_lte( '2.5-beta-1' ) && self::is_gravity_page() ) {

				add_filter( 'gform_admin_pre_render', array( 'GWPerks', 'store_modified_form' ), 11 );
				add_action( 'gform_editor_js', array( 'GWPerks', 'add_form_editor_tabs' ), 1 );

			}
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			add_action( 'wp_ajax_gwp_manage_perk', array( 'GWPerks', 'manage_perk' ) );
			add_action( 'wp_ajax_gwp_dismiss_pointers', array( __class__, 'dismiss_pointers' ) );
			add_action( 'wp_ajax_gwp_dismiss_announcement', array( 'GWPerks', 'dismiss_announcement' ) );

		}

		add_filter( 'admin_body_class', array( __class__, 'add_helper_body_classes' ) );

		add_action( 'gform_logging_supported', array( __class__, 'enable_logging_support' ) );

		// Add Perks tab to form editor.
		add_action( 'gform_field_settings_tabs', array( __class__, 'add_perks_tab' ) );
		add_action( 'gform_field_settings_tab_content_gravity-perks', array( __class__, 'add_perks_tab_settings' ) );

		add_action( 'gform_field_standard_settings', array( __class__, 'dynamic_setting_actions' ), 10, 2 );
		add_action( 'gform_field_appearance_settings', array( __class__, 'dynamic_setting_actions' ), 10, 2 );
		add_action( 'gform_field_advanced_settings', array( __class__, 'dynamic_setting_actions' ), 10, 2 );

		add_action( 'activate_plugin', array( __class__, 'register_perk_activation_hooks' ) );

		add_filter( 'plugin_action_links', array( __class__, 'plugin_manage_perks_link' ), 10, 2 );

		add_action( 'admin_notices', array( __class__, 'get_dashboard_announcements' ) );

		add_filter( 'plugin_auto_update_setting_html', array( __CLASS__, 'disable_auto_updater' ), 10, 3 );

		// load and init all active perks
		self::initialize_perks();

	}

	public static function add_helper_body_classes( $body_class ) {
		if ( is_callable( array( 'GFForms', 'get_page' ) ) && GFForms::get_page() && ! self::is_gf_version_gte( '2.5-beta-1' ) ) {
			$body_class .= ' gf-legacy-ui';
		}
		return $body_class;
	}

	public static function register_perk_activation_hooks( $plugin ) {

		if ( ! GP_Perk::is_perk( $plugin ) ) {
			return;
		}

		$perk = GWPerk::get_perk( $plugin );
		if ( is_wp_error( $perk ) || ! $perk->is_supported() ) {
			return;
		}

		$perk->activate();

	}

	public static function plugin_manage_perks_link( $links, $file ) {
		if ( $file != plugin_basename( __FILE__ ) ) {
			return $links;
		}

		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php' ) ) . '?page=gwp_perks">' . esc_html__( 'Manage Perks', 'gravityperks' ) . '</a>' );

		return $links;
	}

	public static function define_constants() {

		if ( ! defined( 'GW_DOMAIN' ) ) {
			define( 'GW_DOMAIN', 'gravitywiz.com' );
		}

		if ( ! defined( 'GW_PROTOCOL' ) ) {
			define( 'GW_PROTOCOL', 'https' );
		}

		define( 'GW_URL', GW_PROTOCOL . '://' . GW_DOMAIN );

		if ( ! defined( 'GWAPI_URL' ) ) {
			define( 'GWAPI_URL', GW_URL . '/gwapi/v2/' ); // @used storefront_api.php
		}

		define( 'GW_UPGRADE_URL', GW_URL . '/upgrade/' );
		define( 'GW_ACCOUNT_URL', GW_URL . '/account/' );
		define( 'GW_SUPPORT_URL', GW_URL . '/support/' );
		define( 'GW_BUY_URL', GW_URL );

		define( 'GW_GFORM_AFFILIATE_URL', 'http://gwiz.io/gravityforms' );
		define( 'GW_MANAGE_PERKS_URL', admin_url( 'admin.php?page=gwp_perks' ) );

		define( 'GW_PRICE_ID_LEGACY_SINGLE', 0 );
		define( 'GW_PRICE_ID_LEGACY_UNLIMITED', 1 );
		define( 'GW_PRICE_ID_BASIC', 2 );
		define( 'GW_PRICE_ID_ADVANCED', 3 );
		define( 'GW_PRICE_ID_PRO', 4 );

		/**
		* @deprecated 2.0
		* @remove 2.1
		*/
		define( 'GW_SETTINGS_URL', admin_url( 'admin.php?page=gf_settings&addon=Perks&subview=Perks' ) );

		// WP Engine throws an error if we set this directly.
		$register_license_url = esc_url_raw( add_query_arg( array( 'register' => 1 ), GW_SETTINGS_URL ) );
		/**
		* @deprecated 2.0
		* @remove 2.1
		*/
		define( 'GW_REGISTER_LICENSE_URL', $register_license_url );

		/**
		* @deprecated 2.0
		* @remove 2.1
		*/
		define( 'GW_BUY_GPERKS_URL', GW_URL );

	}

	public static function activation() {
		self::init_perk_as_plugin_functionality();
	}

	public static function disable_auto_updater( $html, $plugin_file, $plugin_data ) {
		if ( self::has_valid_license() ) {
			return $html;
		}

		if ( in_array( $plugin_file, array_keys( GWPerks::get_installed_perks() ) ) ) {
			$html = '<em>' . __( 'Register Gravity Perks to enable auto-updates.', 'gravityperks' ) . '</em>';
		}

		return $html;
	}

	/**
	* Get all active perks, load Perk objects, and initialize.
	*
	* By default, perks are only initialized by Gravity Perks. Since they are plugins they have the option to
	* initialize themselves; however, they will need to use a different init function name than "init" as this
	* will always be loaded by default.
	*
	* IF IS NETWORK ADMIN
	*     - only init network-activated perks
	*     - only handle errors for network-activated perks
	* IF IS SINGLE ADMIN
	*     - init network-activated perks and single-activated perks
	*     - only handles errors for
	*
	*/
	private static function initialize_perks() {

		$network_perks = get_site_option( 'gwp_active_network_perks' );

		// if on the network admin, only handle network-activated perks
		$perks = is_network_admin() ? array() : get_option( 'gwp_active_perks' );

		if ( ! $network_perks ) {
			$network_perks = array();
		}

		if ( ! $perks ) {
			$perks = array();
		}

		$perks = array_merge( $network_perks, $perks );

		foreach ( $perks as $perk_file => $perk_data ) {

			$perk = GWPerk::get_perk( $perk_file );

			// New perks (which have a 'parent' property) will be initialized by Gravity Forms via the Add-on Framework.
			if ( is_wp_error( $perk ) || ! $perk->is_old_school() ) {
				continue;
			}

			if ( $perk->is_supported() ) {

				$perk->init();

			} else {

				foreach ( $perk->get_failed_requirements() as $requirement ) {
					self::handle_error( gwar( $requirement, 'code' ), $perk_file, gwar( $requirement, 'message' ) );
				}
			}
		}

	}

	/**
	* Include admin files required on all pages
	*
	*/
	private static function include_admin_files() {
		require_once( self::get_base_path() . '/model/notice.php' );
	}

	private static function maybe_setup() {

		// maybe set up Gravity Perks; only on admin requests for single site installs and always for multisite
		$is_non_ajax_admin = is_admin() && ( defined( 'DOING_AJAX' ) && DOING_AJAX === true ) === false;
		if ( ! $is_non_ajax_admin && ! is_multisite() ) {
			return;
		}

		$has_version_changed = get_option( 'gperks_version' ) != self::$version;

		// making sure version has really changed; gets around aggressive caching issue on some sites that cause setup to run multiple times
		if ( $has_version_changed && is_callable( array( 'GFForms', 'get_wp_option' ) ) ) {
			$has_version_changed = GFForms::get_wp_option( 'gperks_version' ) != self::$version;
		}

		if ( ! $has_version_changed ) {
			return;
		}

		self::setup();

	}

	private static function setup() {

		// force license to be revalidated
		self::flush_license( true );

		update_option( 'gperks_version', self::$version );

	}





	// CLASS INTERFACE //

	/**
	* Called by perks when the "Perks" field settings tab is required.
	*/
	public static function enqueue_field_settings() {
		self::$has_field_settings = true;
	}

	public static function add_perks_tab( $tabs ) {
		$tabs[] = array(
			'id'             => 'gravity-perks',
			'title'          => __( 'Perks', 'gravity-perks' ),
			'toggle_classes' => array(),
			'body_classes'   => array( 'panel-block-tabs__body--settings' ),
		);
		return $tabs;
	}

	public static function add_perks_tab_settings() {
		do_action( 'gws_field_settings' );
		do_action( 'gperk_field_settings' );
	}



	// ERRORS AND NOTICES //

	public static function handle_error( $error_slug, $plugin_file = false, $message = '' ) {
		global $pagenow;

		$plugin_file = $plugin_file ? $plugin_file : self::$basename;
		$is_perk     = $plugin_file != self::$basename;
		$action      = $is_perk ? array( 'GWPerks', 'after_perk_plugin_row' ) : array( 'GWPerks', 'after_plugin_row' );

		$is_plugins_page = self::is_plugins_page();

		switch ( $error_slug ) {

			case 'gravity_forms_required':
				$message          = self::get_message( $error_slug, $plugin_file );
				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_maybe_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

				break;

			case 'wp_required':
				$message          = self::get_message( $error_slug, $plugin_file );
				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_maybe_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

				break;

			case 'gravity_perks_required':
				$message          = self::get_message( $error_slug, $plugin_file );
				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_maybe_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

				break;

			default:
				if ( ! $message || ! $is_plugins_page ) {
					return;
				}

				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

		}

		if ( isset( $message_function ) ) {
			wp_enqueue_style( 'gwp-plugins', self::get_base_url() . '/styles/plugins.css' );
		}

		return;
	}

	public static function get_message( $message_slug, $plugin_file = false ) {

		$min_gravity_forms_version = self::$min_gravity_forms_version;
		$min_wp_version            = self::$min_wp_version;

		// if a $plugin_file is provided AND it is not the same as the base plugin, let's assume it is a perk
		$is_perk = $plugin_file && $plugin_file != self::$basename;

		if ( $is_perk ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$perk      = GWPerk::get_perk( $plugin_file );
			$perk_data = GWPerk::get_perk_data( $plugin_file );

			if ( $perk->is_old_school() ) {
				$min_gravity_forms_version = $perk->get_property( 'min_gravity_forms_version' );
				$min_wp_version            = $perk->get_property( 'min_wp_version' );
			} else {
				$requirements = $perk->parent->minimum_requirements();

				$min_gravity_forms_version = rgars( $requirements, 'gravityforms/version' );
				$min_wp_version            = rgars( $requirements, 'wordpress/version' );
			}
		}

		switch ( $message_slug ) {

			case 'gravity_forms_required':
				if ( class_exists( 'GFForms' ) ) {
					return sprintf(__( 'Current Gravity Forms version (%1$s) does not meet minimum Gravity Forms version requirement (%2$s).', 'gravityperks' ),
					GFForms::$version, $min_gravity_forms_version);
				} else {
					return sprintf(__( 'Gravity Forms %1$s or greater is required. Activate it now or %2$spurchase it today!%3$s', 'gravityperks' ),
					$min_gravity_forms_version, '<a href="' . GW_GFORM_AFFILIATE_URL . '">', '</a>');
				}

			case 'wp_required':
				if ( isset( $perk ) ) {
					return sprintf(__( '%1$s requires WordPress %2$s or greater. You must upgrade WordPress in order to use this perk.', 'gravityperks' ),
					$perk_data['Name'], $min_wp_version);
				} else {
					return sprintf(__( 'Gravity Perks requires WordPress %1$s or greater. You must upgrade WordPress in order to use Gravity Perks.', 'gravityperks' ),
					$min_wp_version);
				}

			case 'gravity_perks_required':
				return sprintf(__( '%1$s requires Gravity Perks %2$s or greater. Activate it now or %3$spurchase it today!%4$s', 'gravityperks' ),
				$perk_data['Name'], $perk->get_property( 'min_gravity_perks_version' ), '<a href="' . GW_BUY_URL . '" target="_blank">', '</a>');

			case 'register_gravity_perks':
				if ( isset( $perk ) ) {
					return sprintf(__( '%1$sRegister%2$s your copy of Gravity Perks to receive access to automatic upgrades and support for this perk. Need a license key? %3$sPurchase one now.%2$s', 'gravityperks' ),
					'<a href="' . GW_MANAGE_PERKS_URL . '">', '</a>', '<a href="' . GW_BUY_URL . '" target="_blank">');
				} else {
					return sprintf(__( '%1$sRegister%2$s your copy of Gravity Perks to receive access to automatic upgrades and support. Need a license key? %3$sPurchase one now.%2$s', 'gravityperks' ),
					'<a href="' . GW_MANAGE_PERKS_URL . '">', '</a>', '<a href="' . GW_BUY_URL . '" target="_blank">');
				}
		}

		return '';
	}


	public static function after_plugin_row( $plugin_file, $plugin_data ) {

		$template = '<p>%s</p>';

		if ( ! self::has_valid_license() ) {

			$message = self::get_message( 'register_gravity_perks' );
			self::display_plugin_row_message( sprintf( $template, $message ), $plugin_data, true, $plugin_file );

		} elseif ( ! self::is_gravity_forms_supported() ) {

			$message = self::get_message( 'gravity_forms_required' );
			self::display_plugin_row_message( sprintf( $template, $message ), $plugin_data, true, $plugin_file );

		} elseif ( ! self::is_wp_supported() ) {

			$message = self::get_message( 'wp_required' );
			self::display_plugin_row_message( sprintf( $template, $message ), $plugin_data, true, $plugin_file );

		}

	}

	public static function after_perk_plugin_row( $plugin_file, $plugin_data ) {

		if ( empty( $plugin_data['Perk'] ) ) {
			return;
		}

		if ( ! self::has_valid_license() ) {
			$message = self::get_message( 'register_gravity_perks', $plugin_file );
			self::display_plugin_row_message( "<p>{$message}</p>", $plugin_data, true, $plugin_file );
		}

		$perk = GWPerk::get_perk( $plugin_file );

		if ( is_wp_error( $perk ) ) {
			return;
		}

		if ( ! $perk->is_supported() && $perk->is_old_school() ) {

			$messages = $perk->get_requirement_messages( $perk->get_failed_requirements() );
			$message  = count( $messages ) > 1 ? '<ul><li>' . implode( '</li><li>', $messages ) . '</li></ul>' : "<p>{$messages[0]}</p>";
			self::display_plugin_row_message( $message, $plugin_data, true, $plugin_file );

		}

	}

	public static function display_admin_message( $message, $class ) {
		?>

		<div id="message" class="<?php echo $class; ?> gwp-message"><p><?php echo $message; ?></p></div>

		<?php
	}

	public static function display_plugin_row_message( $message, $plugin_data, $is_error = false, $plugin_file = false ) {

		$id        = sanitize_title( $plugin_data['Name'] );
		$is_active = false;

		if ( $plugin_file ) {
			$is_active = is_network_admin() ? is_plugin_active_for_network( $plugin_file ) : is_plugin_active( $plugin_file );
		}

		$active = $is_active ? 'active' : 'inactive';

		?>

		<style type="text/css" scoped>
			<?php printf( '#%1$s td, #%1$s th', $id ); ?>,
			<?php printf( 'tr[data-slug="%1$s"] td, tr[data-slug="%1$s"] th', $id ); ?>
			{
				border-bottom: 0;
				box-shadow: none !important;
				-webkit-box-shadow: none !important;
			}

			.gwp-plugin-notice td {
				box-shadow: none !important;
				padding: 0 !important;
			}

			.gwp-plugin-notice + tr[data-slug]:not(.plugin-update-tr) {
				box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
			}

			/*.gwp-plugin-notice + tr.active[data-slug]:not(.plugin-update-tr) > * {*/
			/*    box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1) !important;*/
			/*}*/
			tr.plugin-update-tr.active.gwp-plugin-notice > * {
				box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.1) !important;
			}

			.plugin-update-tr[data-slug^="gp-"] + tr[data-slug]:not(.plugin-update-tr) {
				box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
			}
		</style>

		<tr class="plugin-update-tr <?php echo $active; ?> gwp-plugin-notice">
			<td colspan="4" class="colspanchange">
				<div class="update-message notice inline notice-error notice-alt"><?php echo $message; ?></div>
			</td>
		</tr>

		<?php
	}



	// IS SUPPORTED //

	public static function is_gravity_forms_supported( $min_version = false ) {
		$min_version = $min_version ? $min_version : self::$min_gravity_forms_version;
		return class_exists( 'GFCommon' ) && version_compare( GFCommon::$version, $min_version, '>=' );
	}

	public static function is_wp_supported( $min_version = false ) {
		$min_version = $min_version ? $min_version : self::$min_wp_version;
		return version_compare( get_bloginfo( 'version' ), $min_version, '>=' );
	}

	public static function get_version() {
		return self::$version;
	}



	// PERKS AS PLUGINS //

	/**
	* Initalize all functionality that enables Perks to be plugins but also managed as perks.
	*
	*/
	public static function init_perk_as_plugin_functionality() {

		require_once( 'model/class-gp-plugin.php' );
		require_once( 'model/class-gp-feed-plugin.php' );

		add_filter( 'extra_plugin_headers', array( __class__, 'extra_perk_headers' ) );

		// any time a plugin is activated/deactivated, refresh the list of active perks
		add_action( 'update_site_option_active_sitewide_plugins', array( __class__, 'refresh_active_sitewide_perks' ) );
		add_action( 'update_option_active_plugins', array( __class__, 'refresh_active_perks' ) );

		// any time a plugin is activated/deactivated, reorder plugin loading to load Gravity Perks first
		add_filter( 'pre_update_site_option_active_sitewide_plugins', array( __class__, 'reorder_plugins' ) );
		add_filter( 'pre_update_option_active_plugins', array( __class__, 'reorder_plugins' ) );

		// add "manage perks" link after perk install/update
		add_filter( 'install_plugin_complete_actions', array( __class__, 'add_manage_perks_action' ), 10, 3 );
		add_filter( 'update_plugin_complete_actions', array( __class__, 'add_manage_perks_action' ), 10, 2 );

		// add "upgrade license" button after perk install/update as necessary
		add_filter( 'install_plugin_complete_actions', array( __class__, 'add_upgrade_license_action' ), 10, 3 );
		add_filter( 'update_plugin_complete_actions', array( __class__, 'add_upgrade_license_action' ), 10, 2 );

		// add "buy license" button and text after perk install/update as necessary
		add_filter( 'install_plugin_complete_actions', array( __class__, 'add_invalid_license_action' ), 10, 3 );
		add_filter( 'update_plugin_complete_actions', array( __class__, 'add_invalid_license_action' ), 10, 2 );

		// display "back to perks" link on plugins page
		add_action( 'pre_current_active_plugins', array( __CLASS__, 'display_perks_status_message' ) );

		// when deleting plugins, output a script to update the "No, Return me to the plugin list" button verbiage
		add_action( 'admin_action_delete-selected', array( __class__, 'maybe_add_perk_delete_confirmation' ) );

		if ( is_multisite() ) {

			// prevent perks from being network activated if Gravity Perks is not network activated, priority 11 so it fires after 'save_last_modified_plugin'
			add_action( 'admin_action_activate', array( __class__, 'require_gravity_perks_network_activation' ), 11 );

		}

		// initiate a fix for Windows servers where the GP package file name is too long and prevents installs/updates from processing
		add_filter( 'upgrader_pre_download', array( __class__, 'maybe_shorten_edd_filename' ), 10, 4 );

		do_action( 'gperks_loaded' );

	}

	/**
	* Check if the URL that is about to be downloaded is an EDD package URL. If so, hook our function to shorten
	* the filename.
	*
	* @param mixed  $return
	* @param string $package The URL of the file to be downloaded.
	*
	* @return mixed
	*/
	public static function maybe_shorten_edd_filename( $return, $package ) {
		if ( strpos( $package, '/edd-sl/package_download/' ) !== false ) {
			add_filter( 'wp_unique_filename', array( __class__, 'shorten_edd_filename' ), 10, 2 );
		}
		return $return;
	}

	/**
	* Truncate the temporary filename to 50 characters. This resolves issues with some Windows servers which
	* can not handle very long filenames.
	*
	* @param string $filename
	* @param string $ext
	*
	* @return string
	*/
	public static function shorten_edd_filename( $filename, $ext ) {
		$filename = substr( $filename, 0, 50 ) . $ext;
		remove_filter( 'wp_unique_filename', array( __class__, 'shorten_edd_filename' ), 10 );
		return $filename;
	}

	public static function add_manage_perks_action( $actions, $api, $plugin_file = false ) {

		if ( ( ! $plugin_file || ! GWPerk::is_perk( $plugin_file, true ) ) && ! self::is_request_from_gravity_perks() ) {
			return $actions;
		}

		// if we're coming from Manage Perk's page...
		if ( self::is_request_from_gravity_perks() ) {
			$actions['manage_perks'] = '<a href="' . GW_MANAGE_PERKS_URL . '">' . __( 'Back to Manage Perks', 'gravityperks' ) . '</a>';
			unset( $actions['plugins_page'] );
		} else {
			$actions['manage_perks'] = ' | <a href="' . GW_MANAGE_PERKS_URL . '">' . __( 'Manage Perks', 'gravityperks' ) . '</a>';
		}

		if ( isset( $actions['activate_plugin'] ) ) {
			$actions['activate_plugin'] = str_replace( __( 'Activate Plugin' ), __( 'Activate Perk', 'gravityperks' ), $actions['activate_plugin'] );
		}

		return $actions;
	}

	public static function add_upgrade_license_action( $actions, $api, $plugin_file = false ) {

		if ( ( ! $plugin_file || ! GWPerk::is_perk( $plugin_file, true ) ) && ! self::is_request_from_gravity_perks() ) {
			return $actions;
		}

		if ( empty( $api->download_link ) && self::has_valid_license() && ! self::has_available_perks() ) {
			$actions['upgrade_license'] = '<div class="notice notice-info gp-plugin-action-perk-limit">
				<p>
					You&lsquo;ve reached your perk registration limit. Upgrade your license to install more perks.
				</p>
				<p>
					<a class="button-primary" href="' . self::get_license_upgrade_url() . '">Upgrade License</a>
				</p>
			</div>';
		}

		return $actions;

	}

	public static function add_invalid_license_action( $actions, $api, $plugin_file = false ) {

		if ( ( ! $plugin_file || ! GWPerk::is_perk( $plugin_file, true ) ) && ! self::is_request_from_gravity_perks() ) {
			return $actions;
		}

		if ( ! self::has_valid_license() && empty( $api->download_link ) ) {
			$actions['invalid_license'] = '<div class="notice notice-error gp-plugin-action-invalid-license">
				<p>
					<strong>Uh-oh!</strong> It looks like this site doesn&lsquo;t have a valid Gravity Perks license.
					Please add your license under Manage Perks if you already have a license.
				</p>

				<p>
					Otherwise, you may purchase a license by clicking &ldquo;Buy License&rdquo; below.
				</p>

				<p>
					<a class="button-secondary" href="' . GW_MANAGE_PERKS_URL . '">Manage Perks</a>
					&nbsp;
					<a class="button-secondary" href="' . GW_BUY_URL . '">Buy License</a>
				</p>
			</div>';
		}

		return $actions;

	}

	/**
	* Pull the "Perk" header out of the plugin header data. Used to determine if the plugin is intended to be
	* run by Gravity Perks.
	*
	*/
	public static function extra_perk_headers( $headers ) {
		array_push( $headers, 'Perk' );
		return $headers;
	}

	/**
	* Refresh the list of active perks. Triggered anytime the "active_plugins" or "active_sitewide_plugins" option is updated.
	* This option is updated anytime a plugin is activated or deactivated.
	*
	*/
	public static function refresh_active_perks( $old_value ) {

		$plugins       = self::get_plugins();
		$perks         = array();
		$network_perks = array();

		foreach ( $plugins as $plugin_file => $plugin ) {

			// skip all non-perk plugins
			if ( rgar( $plugin, 'Perk' ) != 'True' ) {
				continue;
			}

			if ( is_multisite() && is_plugin_active_for_network( $plugin_file ) ) {
				$network_perks[ $plugin_file ] = $plugin;
			} elseif ( is_plugin_active( $plugin_file ) ) {
				$perks[ $plugin_file ] = $plugin;
			}
		}

		// if multsite, update network perks
		if ( is_multisite() ) {
			update_site_option( 'gwp_active_network_perks', $network_perks );
		}

		// update active perks every time
		update_option( 'gwp_active_perks', $perks );

	}

	public static function refresh_active_sitewide_perks( $old_value ) {
		self::refresh_active_perks( $old_value );
	}

	/**
	* Update plugin loading order. Anytime the "active_plugins" option is updated, this function reorders the plugins, placing
	* Gravity Perks as the first plugin to load to ensure that it is loaded before any individual Perk plugin.
	*
	*/
	public static function reorder_plugins( $plugins ) {

		$perks_file = plugin_basename( __FILE__ );

		$index = array_search( $perks_file, $plugins );
		if ( $index === false ) {
			$index = array_key_exists( $perks_file, $plugins ) ? $perks_file : false;
		}

		if ( $index === false ) {
			return $plugins;
		}

		$perks_item = array( $index => $plugins[ $index ] );
		unset( $plugins[ $index ] );

		if ( is_numeric( $index ) ) {
			array_unshift( $plugins, $perks_file );
		} else {
			$plugins = array_merge( $perks_item, $plugins );
		}

		return $plugins;
	}

	public static function save_requesting_blog_id( $value ) {
		$blog_id = isset( $_REQUEST['blog_id'] ) ? intval( $_REQUEST['blog_id'] ) : false;
		update_option( 'gperk_requestee_blog_id', $blog_id );
	}

	public static function get_requesting_blog_id() {
		return get_option( 'gperk_requestee_blog_id' );
	}

	public static function get_plugin_actions() {
		return array( 'install-plugin', 'delete-selected', 'deactivate', 'activate' );
	}

	public static function display_perks_status_message() {

		// @todo Revisit this function; see pre 1.2.21 version for original version. Has been stripped down for now.

		$error = isset( $_GET['gwp_error'] ) ? $_GET['gwp_error'] : false;
		if ( ! $error ) {
			return;
		}

		$is_error = true;
		$message  = '';

		switch ( $error ) {
			case 'networkperks':
				$message = __( '<strong>Gravity Perks</strong> must be network activated before a <strong>perk</strong> can be network activated.', 'gravityperks' );
				break;
		}

		?>

		<div class="<?php echo $is_error ? 'error' : 'updated'; ?> gwp-message">
			<p><?php echo $message; ?></p>
		</div>

		<style type="text/css">
			#message + div.gwp-message { margin-top:-17px; border-top-style: dotted;
				border-top-right-radius: 0; border-top-left-radius: 0; }
		</style>

		<?php

	}

	public static function maybe_add_perk_delete_confirmation() {
		// only show 'Return to Manage Perks Page' button if request came from GPerks
		if ( self::is_request_from_gravity_perks() ) {
			add_action( 'in_admin_footer', array( __class__, 'add_perk_delete_confirmation_script' ) );
		}
	}

	/**
	* Add 'Return to Manage Perks Page' button on plugin delete confirmation screen.
	*
	* There is no way to filter the default buttons on this screen, so let's add our own button and hide
	* the existing button via JS.
	*
	*/
	public static function add_perk_delete_confirmation_script() {
		?>

		<script type="text/javascript">
			(function($){

				var cancelButton = $('input[value="<?php _e( 'No, Return me to the plugin list' ); ?>"]');
				var perksButton = $('<input value="<?php _e( 'No, Return to Manage Perks page', 'gravityperks' ); ?>" type="submit" class="button" />');
				perksButton.insertAfter(cancelButton);
				cancelButton.hide();

			})(jQuery);
		</script>

		<?php
	}

	public static function get_manage_perks_site_select() {
		$blogs       = get_blogs_of_user( get_current_user_id() );
		$site_select = '
			<span id="manage-perks-site-select" style="display:none;">
				<select onchange="if(this.value != \'\') { window.location.href = this.value };">
					<option value="">Select Site</option>';

		foreach ( $blogs as $blog ) {
			$site_select .= '<option value="' . get_admin_url( $blog->userblog_id, 'admin.php?page=gwp_perks' ) . '">' . $blog->blogname . '</option>';
		}

		$site_select .= '
				</select>
			</span>';

		return $site_select;
	}

	public static function require_gravity_perks_network_activation() {

		if ( ! is_network_admin() || self::is_gravity_perks_network_activated() ) {
			return;
		}

		$plugin = gwar( $_REQUEST, 'plugin' );
		if ( ! GWPerk::is_perk( $plugin ) ) {
			return;
		}

		$redirect = self_admin_url( 'plugins.php?gwp_error=networkperks&plugin=' . $plugin );
		wp_redirect( esc_url_raw( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $plugin ), $redirect ) ) );
		exit;

	}

	public static function is_gravity_perks_network_activated() {

		if ( ! is_multisite() ) {
			return false;
		}

		foreach ( wp_get_active_network_plugins() as $plugin ) {
			$plugin_file = plugin_basename( $plugin );
			if ( plugin_basename( __file__ ) == $plugin_file && is_plugin_active_for_network( $plugin_file ) ) {
				return true;
			}
		}

		return false;
	}



	// API & LICENSING //
	public static function can_manage_license() {
		return current_user_can( 'install_plugins' );
	}

	public static function load_api() {
		require_once( dirname( __FILE__ ) . '/includes/class-gwapi.php' );
		self::$api = new GWAPI( array(
			'plugin_file' => plugin_basename( __FILE__ ),
			'version'     => GWPerks::get_version(),
			'license'     => GWPerks::get_license_key(),
			'item_name'   => 'Gravity Perks',
			'author'      => 'David Smith',
		) );
	}

	public static function get_api() {
		return self::$api;
	}

	public static function has_valid_license( $flush = false ) {
		return self::$api->has_valid_license( $flush );
	}

	public static function get_license_data( $flush = false ) {
		return self::$api->get_license_data( $flush );
	}

	public static function get_api_status() {
		return self::$api->get_api_status();
	}

	public static function get_api_error_message() {

		$message  = __( 'Oops! Your site is having some trouble communicating with the our API.', 'gravityperks' );
		$message .= sprintf( '&nbsp;<a href="%s" target="_blank">%s</a>', 'https://' . GW_DOMAIN . '/documentation/troubleshooting-licensing-api/', __( 'Let\'s get this fixed.', 'gravityperks' ) );

		return $message;
	}

	public static function register_perk( $perk_id ) {
		if ( ! self::can_manage_license() ) {
			return false;
		}

		return self::$api->register_perk( $perk_id );
	}

	public static function deregister_perk( $perk_id ) {
		if ( ! self::can_manage_license() ) {
			return false;
		}

		return self::$api->deregister_perk( $perk_id );
	}

	public static function get_license_upgrade_url() {
		$license_data = self::get_license_data();

		if ( empty( $license_data['ID'] ) || empty( $license_data['checksum'] ) ) {
			return GW_ACCOUNT_URL;
		}

		return add_query_arg( array(
			'license_id'   => $license_data['ID'],
			'license_hash' => md5( GWPerks::get_license_key() ),
			'utm_source'   => 'plugin-upgrade',
		), GW_UPGRADE_URL );
	}

	public static function has_available_perks( $flush = false ) {
		$license_data = self::get_license_data( $flush );

		if ( rgar( $license_data, 'valid' ) === false ) {
			return false;
		}

		$register_perks = rgar( $license_data, 'registered_perks', array() );

		$registered_perk_count = count( $register_perks );
		$perk_limit            = $license_data['perk_limit'];

		if ( $perk_limit === 0 || $registered_perk_count < $perk_limit ) {
			return true;
		}

		return false;
	}

	public static function is_unlimited( $flush = false ) {
		$license_data = self::get_license_data( $flush );

		if ( ! $license_data || ! isset( $license_data['perk_limit'] ) ) {
			return false;
		}

		return $license_data['perk_limit'] === 0;
	}

	public static function is_perk_registered( $perk_id, $flush = false ) {
		$license_data = self::get_license_data( $flush );

		if ( empty( $license_data['registered_perks'] ) ) {
			return false;
		}

		$registered_perks = $license_data['registered_perks'];

		if ( array_search( $perk_id, $registered_perks ) !== false ) {
			return true;
		}

		return false;
	}

	public static function flush_license( $hard = false ) {
		delete_site_transient( 'gwp_license_data' );

		if ( ! $hard ) {
			return;
		}

		delete_site_transient( 'gwapi_get_dashboard_announcements' );
		delete_site_transient( 'gwapi_get_products' );
	}

	public static function get_license_key() {

		$settings = get_site_option( 'gwp_settings' );

		if ( defined( 'GPERKS_LICENSE_KEY' ) && GPERKS_LICENSE_KEY ) {
			return trim( GPERKS_LICENSE_KEY );
		}

		if ( isset( $settings['license_key'] ) ) {
			return trim( $settings['license_key'] );
		}

		return false;
	}

	/**
	* Returns a complete list of available perks from API.
	*/
	public static function get_available_perks() {

		$perks = self::$api->get_products();

		if ( ! $perks || ! is_array( $perks ) ) {
			return array();
		}

		$perks = array_filter( $perks, array( __CLASS__, 'filter_perk_from_product' ) );

		uasort( $perks, array( __class__, 'sort_available_perks_alphabetically' ) );

		return $perks;

	}

	public static function filter_perk_from_product( $product ) {

		if ( empty( $product->categories ) || array_search( 'perk', $product->categories ) === false ) {
			return false;
		}

		return true;

	}

	/**
	* Sorts available perks alphabetically with their name property
	**/
	public static function sort_available_perks_alphabetically( $a, $b ) {
		return strcasecmp( $a->name, $b->name );
	}

	/**
	* Retrieve all installed perks.
	*
	*/
	public static function get_installed_perks() {

		if ( ! empty( self::$installed_perks ) ) {
			return self::$installed_perks;
		}

		$plugins = self::get_plugins();
		$perks   = array();

		foreach ( $plugins as $plugin_file => $plugin_data ) {
			if ( ( isset( $plugin_data['Perk'] ) && $plugin_data['Perk'] ) ) {
				$perks[ $plugin_file ] = $plugin_data;
			}
		}

		return $perks;

	}



	// WP ADMIN INTEGRATION //

	public static function load_page() {
		require_once( self::get_base_path() . '/admin/manage_perks.php' );
		GWPerksPage::load_page();
	}

	/**
	* Hook into Gravity Forms menu and add "Perks" as a submenu item.
	*
	* @param mixed $addon_menus
	*/
	public static function add_menu_item( $addon_menus ) {

		$menu = array(
			'label'      => __( 'Perks', 'gravityperks' ),
			'permission' => 'update_plugins',
			'name'       => 'gwp_perks',
			'callback'   => array( __CLASS__, 'load_page' ),
		);

		$addon_menus[] = $menu;

		add_filter( 'parent_file', array( __CLASS__, 'add_updates_badge' ) );

		return $addon_menus;
	}

	public static function add_updates_badge( $return ) {
		global $submenu;

		if ( ! self::has_undismissed_announcment() || ! current_user_can( 'manage_options' ) ) {
			return $return;
		}

		if ( isset( $submenu['gf_edit_forms'] ) && is_array( $submenu['gf_edit_forms'] ) ) {
			foreach ( $submenu['gf_edit_forms'] as &$item ) {
				if ( $item[0] == __( 'Perks', 'gravityperks' ) ) {
					$item[0] .= ' <span class="update-plugins count-1"><span class="plugin-count">1</span></span>';
				}
			}
		}

		return $return;
	}

	/**
	* @TODO: might not be used...
	*
	* Hook into WP and modify the actions available after installing a perk plugin.
	*
	*/
	public static function install_plugin_complete_actions( $install_actions, $api, $plugin_file ) {

		if ( ! isset( $api->is_perk ) || ! $api->is_perk ) {
			return $install_actions;
		}

		unset( $install_actions['plugins_page'] );

		$perks_page_url                = gwget( 'blog_id' ) ? get_admin_url( gwget( 'blog_id' ), 'admin.php?page=gwp_perks' ) : GW_MANAGE_PERKS_URL;
		$install_actions['perks_page'] = '<a href="' . $perks_page_url . '" title="' . __( 'Return to Perks Page', 'gravityperks' ) . '" target="_parent">' . __( 'Return to Perks Page', 'gravityperks' ) . '</a>';

		return $install_actions;
	}

	/**
	* Register scripts and init the gperk object
	*
	*/
	public static function register_scripts() {

		// @todo Should we make Gravity Perks load from gform_loaded so we can safely assume GF has been loaded?
		if ( ! class_exists( 'GFCommon' ) ) {
			return;
		}

		wp_register_style( 'gwp-admin', self::get_base_url() . '/styles/admin.css' );
		wp_register_style( 'gwp-asmselect', self::get_base_url() . '/styles/jquery.asmselect.css' );

		wp_register_script( 'gwp-common', self::get_base_url() . '/scripts/common.js', array( 'jquery' ), GravityPerks::$version, false );
		wp_register_script( 'gwp-admin', self::get_base_url() . '/scripts/admin.js', array( 'jquery', 'gwp-common' ), GravityPerks::$version, true );
		wp_register_script( 'gwp-frontend', self::get_base_url() . '/scripts/frontend.js', array( 'jquery', 'gwp-common' ), GravityPerks::$version, true );
		wp_register_script( 'gwp-repeater', self::get_base_url() . '/scripts/repeater.js', array( 'jquery' ), GravityPerks::$version, true );
		wp_register_script( 'gwp-asmselect', self::get_base_url() . '/scripts/jquery.asmselect.js', array( 'jquery' ), GravityPerks::$version, true );

		// register our scripts with Gravity Forms so they are not blocked when noconflict mode is enabled
		add_filter( 'gform_noconflict_scripts', array( __CLASS__, 'register_noconflict_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( __CLASS__, 'register_noconflict_styles' ) );

		require_once( GFCommon::get_base_path() . '/currency.php' );

		wp_localize_script('gwp-common', 'gperk', array(
			'baseUrl'      => self::get_base_url(),
			'gformBaseUrl' => GFCommon::get_base_url(),
			'currency'     => RGCurrency::get_currency( GFCommon::get_currency() ),
		) );

		add_action( 'admin_enqueue_scripts', array( 'GWPerks', 'enqueue_scripts' ) );

	}

	/**
	* Enqueue Javascript
	*
	* In the admin, include admin.js (and common.js by dependency) on all Gravity Form and Gravity Perk pages.
	* On the front-end, common.js and frontend.js are included when enqueued by a perk.
	*
	*/
	public static function enqueue_scripts() {

		GWPerks::enqueue_styles();

		if ( self::is_gravity_perks_page() || self::is_gravity_page() ) {
			wp_enqueue_script( 'gwp-admin' );
		}

	}

	public static function enqueue_styles() {

		if ( self::is_gravity_perks_page() || self::is_gravity_page() ) {
			wp_enqueue_style( 'gwp-admin' );
		}

	}

	public static function register_noconflict_scripts( $scripts ) {
		return array_merge( $scripts, array( 'gwp-admin', 'gwp-frontend', 'gwp-common', 'gwp-asmselect' ) );
	}

	public static function register_noconflict_styles( $styles ) {
		return array_merge( $styles, array( 'gwp-admin', 'gwp-asmselect' ) );
	}


	// AJAX //

	public static function manage_perk() {
		require_once( GWPerks::get_base_path() . '/admin/manage_perks.php' );
		GWPerksPage::ajax_manage_perk();
	}

	public static function json_and_die( $data ) {
		echo json_encode( $data );
		die();
	}



	// HELPERS //

	public static function get_base_url() {
		$folder = basename( dirname( __FILE__ ) );
		return plugins_url( $folder );
	}

	public static function get_base_path() {
		return dirname( __FILE__ );
	}

	public static function is_gravity_page() {
		return class_exists( 'RGForms' ) ? RGForms::is_gravity_page() : false;
	}

	public static function is_plugins_page() {
		global $pagenow;

		$query_action = isset( $_GET['action'] ) ? $_GET['action'] : false;

		return $pagenow == 'plugins.php' && ! $query_action;
	}

	public static function is_gravity_perks_page( $page = false ) {

		$current_page = self::get_current_page();
		$gp_pages     = array( 'gwp_perks', 'gwp_settings' );

		if ( $page ) {
			return $current_page == $page;
		}

		return in_array( $current_page, $gp_pages );
	}

	public static function is_plugin_file_perk( $plugin ) {

		$plugins = is_array( $plugin ) ? $plugin : array( $plugin );

		foreach ( $plugins as $plugin ) {
			if ( GWPerk::is_perk( $plugin ) ) {
				return true;
			}
		}

		return false;
	}

	public static function is_request_from_gravity_perks() {
		return isset( $_GET['gwp'] ) || ( isset( $_GET['from'] ) && $_GET['from'] == 'gwp' );
	}

	public static function is_local() {
		return $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || in_array( substr( strrchr( $_SERVER['SERVER_NAME'], '.' ), 1 ), array( 'local', 'dev' ) );
	}

	private static function get_current_page() {
		$current_page = trim( strtolower( rgget( 'page' ) ) );

		return $current_page;
	}

	/**
	* @return array An array of installed plugins.
	*/
	public static function get_plugins( $clear_cache = false ) {
		// Ensure that WordPress plugin functions are loaded before calling them as get_plugins() can be called at various times.
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugins = get_plugins();

		if ( $clear_cache || ! self::plugins_have_perk_plugin_header( $plugins ) ) {
			wp_cache_delete( 'plugins', 'plugins' );
			$plugins = get_plugins();
		}

		return $plugins;
	}

	/**
	* Confirm whether our custom plugin header 'Perk' is available.
	*
	* When activating Gravity Perks, the plugin cache has already been created without the custom 'Perk' header.
	*
	*/
	public static function plugins_have_perk_plugin_header( $plugins ) {
		foreach ( $plugins as $plugin ) {
			if ( rgar( $plugin, 'Perk' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	* Handle showing welcome pointer.
	*
	*/
	public static function welcome_pointer() {

		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		if ( in_array( 'gwp_welcome', $dismissed ) || self::is_gravity_perks_page() ) {
			return;
		}

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		add_action( 'admin_print_footer_scripts', array( 'GWPerks', 'welcome_pointer_script' ) );

	}

	public static function welcome_pointer_script() {

		$pointer_content  = '<h3>' . __( 'Welcome to Gravity Perks', 'gravityperks' ) . '</h3>';
		$pointer_content .= '<p>' . __( 'Ready to get started? Click the <strong>Perks</strong> link (to the left) to take a quick tour.', 'gravityperks' ) . '</p>';
		?>

		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.wp-submenu a[href="admin.php?page=gwp_perks"]').pointer({
				content: '<?php echo $pointer_content; ?>',
				position: {
					edge: 'left',
					align: 'center'
				},
				open: function(event, elements) {
					elements.element.css('backgroundColor', 'rgba( 255, 255, 255, 0.15' );
				},
				close: function(event, elements) {
					$.post( ajaxurl, {
						pointer: 'gwp_welcome',
						action: 'dismiss-wp-pointer'
					});
					elements.element.css('backgroundColor', 'transparent');
					$('a[href="#manage"]').pointer('open');
				}
			}).pointer('open');
		});
		//]]>
		</script>

		<?php
	}

	public static function is_pointer_dismissed( $pointer_name ) {
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		return in_array( $pointer_name, $dismissed );
	}

	public static function dismiss_pointer( $pointer ) {

		if ( is_array( $pointer ) ) {
			foreach ( $pointer as $pntr ) {
				self::dismiss_pointer( $pntr );
			}
		} else {

			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );
			if ( in_array( $pointer, $dismissed ) ) {
				return;
			}

			$dismissed[] = $pointer;
			$dismissed   = implode( ',', $dismissed );

			update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed );

		}

	}

	public static function dismiss_pointers() {
		require_once( self::get_base_path() . '/admin/manage_perks.php' );

		check_ajax_referer( 'gwp-dismiss-pointers', 'security' );

		foreach ( GWPerksPage::get_perk_pointers() as $perk_pointer ) {
			self::dismiss_pointer( $perk_pointer['name'] );
		}
	}

	public static function format_changelog( $string ) {
		$string = wp_strip_all_tags( $string );
		$string = stripslashes( $string );
		$string = implode( "\n", array_map( 'trim', explode( "\n", $string ) ) );
		$string = str_replace( '## ', '#### ', $string );
		return self::markdown( $string );
	}

	/**
	* There are on-going issues with this Markdown library and versions of PHP 7.1+. Usage is currently limited to
	* formatting the changelog of Gravity Perks.
	*
	* @param $string
	*
	* @return mixed
	*/
	public static function markdown( $string ) {
		if ( ! version_compare( phpversion(), '5.3', '>=' ) ) {
			return sprintf( '<div class="error"><p>%s</p></div>%s', __( 'Does this page look strange? Your PHP version is out-of-date. <strong>Please upgrade.</strong>', 'gravityperks' ), $string );
		}
		return include 'includes/_markdown.php';
	}

	public static function apply_filters( $filter_base, $modifiers, $value ) {

		if ( ! is_array( $modifiers ) ) {
			$modifiers = array( $modifiers );
		}

		array_unshift( $modifiers, '' );

		$args = array_slice( func_get_args(), 3 );

		// apply default filter first
		$value = self::call_apply_filters( $filter_base, $value, $args );

		// apply modified versions of filter
		foreach ( $modifiers as $modifier ) {
			$value = self::call_apply_filters( "{$filter_base}_{$modifier}", $value, $args );
		}

		return $value;
	}

	private static function call_apply_filters( $filter_name, $value, $args ) {
		return apply_filters( $filter_name, $value,
			isset( $args[0] ) ? $args[0] : null,
			isset( $args[1] ) ? $args[1] : null,
			isset( $args[2] ) ? $args[2] : null,
			isset( $args[3] ) ? $args[3] : null,
			isset( $args[4] ) ? $args[4] : null,
			isset( $args[5] ) ? $args[5] : null,
			isset( $args[6] ) ? $args[6] : null,
			isset( $args[7] ) ? $args[7] : null,
			isset( $args[8] ) ? $args[8] : null,
			isset( $args[9] ) ? $args[9] : null,
			isset( $args[10] ) ? $args[10] : null
		);
	}

	public static function dynamic_setting_actions( $position, $form_id ) {

		$action = current_filter() . '_' . $position;

		if ( did_action( $action ) < 1 ) {
			do_action( $action, $form_id );
			//echo $action . '<br />';
		}
	}

	public static function drop_tables( $tables ) {
		global $wpdb;

		$tables = is_array( $tables ) ? $tables : array( $tables );

		foreach ( $tables as $table ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}

	}



	// LOGGING //

	public static function enable_logging_support( $plugins ) {
		$plugins['gravityperks'] = __( 'Gravity Perks', 'gravityperks' );
		return $plugins;
	}

	public static function log_error( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'gravityperks', $message, KLogger::ERROR );
		}
	}

	public static function log_debug( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'gravityperks', $message, KLogger::DEBUG );
		}
	}

	public static function log( $message ) {
		$backtrace = debug_backtrace();
		$caller    = $backtrace[1];
		$method    = '';
		if ( isset( $caller['class'] ) && $caller['class'] ) {
			$method .= $caller['class'] . '::';
		}
		$method .= $caller['function'];
		self::log_debug( sprintf( '%s: %s', $method, $message ) );
	}










	// REEVALUATE ALL CODE BELOW THIS LINE //

	/**
	* Adds a new "Perks" tab to the form and/or field settings where perk objects can
	* will load their form settings
	*
	* @param array $form: GF Form object
	*/
	public static function add_form_editor_tabs() {

		// Editor has changed in Gravity Forms 2.5.
		if ( self::is_gf_version_gte( '2.5-beta-1' ) ) {
			return;
		}

		if ( ! self::$has_form_settings && ! self::$has_field_settings ) {
			return;
		}

		?>

		<style type="text/css">
			.gws-child-setting { display:none; padding: 10px 0 10px 15px; margin: 6px 0 0 6px; border-left: 2px solid #eee; }
		</style>

		<script type="text/javascript">

		jQuery(document).ready(function($){

			<?php if ( self::$has_form_settings ) : ?>
				gperk.addTab( $('#form_settings'), '#gws_form_tab', '<?php _e( 'Perks', 'gravityperks' ); ?>');
			<?php endif; ?>

			<?php if ( self::$has_field_settings ) : ?>
				gperk.addTab( $('#field_settings'), '#gws_field_tab', '<?php _e( 'Perks', 'gravityperks' ); ?>');
			<?php endif; ?>

		});

		</script>

		<?php if ( self::$has_form_settings ) : ?>
		<div id="gws_form_tab">
			<ul class="gforms_form_settings">
				<?php do_action( 'gws_form_settings' ); ?>
			</ul>
		</div>
		<?php endif; ?>

		<?php if ( self::$has_field_settings ) : ?>
		<div id="gws_field_tab">
			<ul class="gforms_field_settings">
				<?php do_action( 'gws_field_settings' ); ?>
				<?php do_action( 'gperk_field_settings' ); ?>
			</ul>
		</div>
		<?php endif; ?>

		<?php
	}

	public static function store_modified_form( $form ) {
		self::$form = $form;
		return $form;
	}


	/**
	* Dashboard News
	*/
	public static function get_dismissed_announcements() {
		$dismissed_announcements = get_user_meta( get_current_user_id(), '_gwp_dismissed_announcements', true );

		if ( ! is_array( $dismissed_announcements ) ) {
			$dismissed_announcements = array();
		}

		return $dismissed_announcements;
	}

	public static function has_undismissed_announcment( $announcements = array() ) {

		$announcements = ! empty( $announcements ) ? $announcements : self::$api->get_dashboard_announcements();
		if ( empty( $announcements ) ) {
			return false;
		}

		$dismissed_announcements = self::get_dismissed_announcements();
		if ( array_search( $announcements[0]->ID, $dismissed_announcements ) !== false ) {
			return false;
		}

		return true;
	}

	public static function get_dashboard_announcements() {

		if ( ! current_user_can( 'manage_options' ) || ! class_exists( 'GFForms' ) ) {
			return;
		}

		$announcements = self::$api->get_dashboard_announcements();
		if ( ! self::has_undismissed_announcment( $announcements ) ) {
			return;
		}

		if ( $announcements[0]->context === 'manage_perks' && rgget( 'page' ) != 'gwp_perks' ) {
			return;
		}
		?>
		<div class="notice notice-info gwiz-announcement is-dismissible" style="border-left-color: #6e4c88">
			<style>
			.gwiz-announcement a {
				color: #6e4c88;
			}
			</style>

			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$('.wrap').on('click', '.gwiz-announcement .notice-dismiss', function () {
						$.post(ajaxurl, {
							action: 'gwp_dismiss_announcement',
							announcementId: '<?php echo $announcements[0]->ID; ?>'
						});
						// Remove badge from Perks menu item.
						$( '.wp-submenu' ).find( 'a[href="admin.php?page=gwp_perks"]' ).find( 'span.update-plugins' ).remove();
					});
				});
			</script>

			<p><?php echo $announcements[0]->body; ?></p>
		</div>
		<?php

	}

	public static function dismiss_announcement() {

		$dismissed_announcements   = self::get_dismissed_announcements();
		$dismissed_announcements[] = rgpost( 'announcementId' );

		update_user_meta( get_current_user_id(), '_gwp_dismissed_announcements', $dismissed_announcements );

	}


	/**
	* Get all perk options or optionally specify a slug to get a specific perk's options.
	* If slug provided and no options found, return default perk options.
	*
	* @param mixed $slug Perk slug
	* @return array $options array or array of of perk options arrays
	*/
	public static function get_perk_options( $slug = false ) {

		$all_perk_options = get_option( 'gwp_perk_options' );

		if ( ! $all_perk_options ) {
			$all_perk_options = array();
		}

		if ( $slug ) {
			foreach ( $all_perk_options as $perk_options ) {
				if ( $perk_options['slug'] == $slug ) {
					return $perk_options;
				}
			}
			require_once( self::get_base_path() . '/model/perk.php' );
			return GWPerk::get_default_perk_options( $slug );
		}

		return $all_perk_options;
	}

	public static function get_options_from_installed_perks() {

		$perks            = GWPerks::get_installed_perks();
		$all_perk_options = array();

		foreach ( $perks as $perk ) {
			$all_perk_options[] = $perk->get_save_options();
		}

		return $all_perk_options;
	}

	public static function update_perk_option( $updated_options ) {

		$all_perk_options = self::get_perk_options();
		$is_new           = true;

		foreach ( $all_perk_options as &$perk_options ) {

			if ( $perk_options['slug'] == $updated_options['slug'] ) {
				$is_new       = false;
				$perk_options = $updated_options;
			}
		}

		if ( $is_new ) {
			$all_perk_options[ $updated_options['slug'] ] = $updated_options;
		}

		return update_option( 'gwp_perk_options', $all_perk_options );
	}

	public static function is_debug() {

		$enabled_via_constant = defined( 'GP_DEBUG' ) && GP_DEBUG;
		$enabled_via_query    = isset( $_GET['gp_debug'] ) && current_user_can( 'update_core' );

		return $enabled_via_constant || $enabled_via_query;
	}

	public static function get_gravityforms_db_version() {

		if ( method_exists( 'GFFormsModel', 'get_database_version' ) ) {
			$db_version = GFFormsModel::get_database_version();
		} else {
			$db_version = GFForms::$version;
		}

		return $db_version;
	}

	/**
	* Check if installed version of Gravity Forms is less than or equal to the specified version.
	*
	* @param string $version Version to compare with Gravity Forms' version.
	*
	* @return bool
	*/
	public static function is_gf_version_lte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( GFForms::$version, $version, '<=' );
	}

	/**
	* Check if installed version of Gravity Forms is greater than or equal to the specified version.
	*
	* @param string $version Version to compare with Gravity Forms' version.
	*
	* @return bool
	*/
	public static function is_gf_version_gte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( GFForms::$version, $version, '>=' );
	}

}

class GWPerks extends GravityPerks { }

/**
 * Late static binding for dynamic function calls.
 *
 * Provides compatibility with PHP 7.2 (create_function deprecated) and 5.2.
 * So whenever the need for `create_function` arises, use this instead.
 */
class GP_Late_Static_Binding {

	private $args = array();

	public function __construct( $args = array() ) {
		$this->args = wp_parse_args( $args, array(
			'form_id' => 0,
			'message' => '',
			'class'   => '',
			'value'   => '',
		) );
	}

	public function GravityPerks_display_admin_message() {
		GravityPerks::display_admin_message( $this->args['message'], $this->args['class'] );
	}

	public function GravityPerks_maybe_display_admin_message() {

		$screen = get_current_screen();
		if ( $screen->id === 'dashboard' || GravityPerks::is_gravity_page() || GravityPerks::is_gravity_perks_page() || GravityPerks::is_plugins_page() ) {
			GravityPerks::display_admin_message( $this->args['message'], $this->args['class'] );
		}

	}

	public function GWAPI_dummy_func( $return ) {
		return $return;
	}

	public function Perk_array_push( $array ) {
		$array[] = $this->args['value'];
		return $array;
	}

	public function Perk_value_pass_through( $return ) {
		return $this->args['value'];
	}
}

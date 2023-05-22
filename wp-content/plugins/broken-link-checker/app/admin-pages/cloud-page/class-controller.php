<?php
/**
 * BLC Dashboard admin page
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Dashboard
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Pages\Cloud_Page;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Admin_Page;
use WPMUDEV_BLC\Core\Traits\Escape;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\App\Admin_Pages\Cloud_Page\Model as Dash_Model;

use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Page
 */
class Controller extends Admin_Page {
	/**
	 * Use the Escape and Dashboard_API Traits.
	 *
	 * @since 2.0.0
	 */
	use Escape, Dashboard_API;

	/**
	 * States if the site is connected to Hub.
	 *
	 * @since 2.0.0
	 * @var bool $site_connected True if site is connected, else false.
	 *
	 */
	private $site_connected = false;

	/**
	 * States if using legacy. True if using false legacy false if not.
	 *
	 * @since 2.0.0
	 * @var bool $site_connected True if using false legacy false if not.
	 *
	 */
	private $use_legacy = false;

	/**
	 * Override Admin Page Init
	 *
	 * @since 2.0.0
	 *
	 * @return void Initialize the Admin_Page.
	 */
	public function init() {
		add_action(
			'rest_api_init',
			function () {
				$this->prepare_props();
				$this->actions();
			}
		);

		parent::init();
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function prepare_props() {
		$this->is_submenu     = false;
		$this->unique_id      = Utilities::get_unique_id();
		$this->page_title     = __( 'Broken Link Checker', 'broken-link-checker' );
		$this->menu_title     = __( 'Link Checker', 'broken-link-checker' );
		$this->capability     = 'manage_options';
		$this->menu_slug      = 'blc_dash';
		$this->site_connected = (bool) self::site_connected();
		$this->use_legacy     = (bool) Dash_Model::use_legacy();

		// phpcs:ignore
		$this->icon_url = 'data:image/svg+xml;base64,' . base64_encode( '
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clip-path="url(#clip0_1245_1418)">
					<path d="M14.0179 20.0001C13.5043 20.0001 12.9908 19.9302 12.4773 19.7906L11.5196 19.5252C10.9922 19.3855 10.673 18.8408 10.8118 18.2962C10.9506 17.7654 11.4919 17.4442 12.0331 17.5839L12.9908 17.8492C15.1005 18.4219 17.2934 17.1509 17.8624 15.0001C18.14 13.9665 18.0012 12.8772 17.4738 11.9554C16.9464 11.0336 16.0859 10.3632 15.0588 10.0839L12.6993 9.49727C12.1719 9.35761 11.8388 8.82688 11.9776 8.28219C12.1164 7.75146 12.6438 7.41627 13.1851 7.55593L15.5585 8.15649C17.1129 8.57548 18.4037 9.5671 19.1948 10.9638C19.986 12.3604 20.1941 13.9805 19.7916 15.5308C19.0699 18.2263 16.6549 20.0001 14.0179 20.0001Z" fill="white"/>
					<path d="M5.98202 20.0001C3.34496 20.0001 0.929973 18.2264 0.208252 15.5308C-0.208126 13.9806 6.31477e-05 12.3605 0.80506 10.9638C1.59618 9.56716 2.88695 8.57554 4.42754 8.15655L6.78702 7.45822C7.31443 7.30459 7.8696 7.59789 8.02227 8.12861C8.17494 8.65934 7.88348 9.218 7.35607 9.37163L4.96884 10.0839C3.91401 10.3632 3.06738 11.0336 2.52609 11.9554C1.99868 12.8772 1.85988 13.9666 2.13747 15.0001C2.70652 17.137 4.89944 18.4219 7.00909 17.8493L7.96675 17.5839C8.49417 17.4443 9.03546 17.7515 9.18813 18.2962C9.32692 18.8269 9.02158 19.3716 8.48029 19.5253L7.52262 19.7906C6.99521 19.9303 6.48167 20.0001 5.98202 20.0001Z" fill="white"/>
					<path d="M4.06657 14.4972C3.92778 13.9665 4.23313 13.4218 4.77442 13.2681L7.43923 12.5419C7.96664 12.4022 8.50793 12.7095 8.66061 13.2542C8.7994 13.7849 8.49405 14.3296 7.95276 14.4832L5.28795 15.2095C4.74666 15.3492 4.20537 15.0279 4.06657 14.4972Z" fill="white"/>
					<path d="M14.7118 15.1956L12.047 14.4694C11.5195 14.3297 11.2003 13.785 11.3391 13.2403C11.4779 12.7096 12.0192 12.3884 12.5605 12.528L15.2253 13.2543C15.7527 13.394 16.0719 13.9387 15.9331 14.4833C15.7944 15.028 15.2392 15.3493 14.7118 15.1956Z" fill="white"/>
					<path d="M9.99295 4.77654C9.43779 4.77654 8.99365 4.32961 8.99365 3.77095V1.00559C8.99365 0.446927 9.43779 0 9.99295 0C10.5481 0 10.9923 0.446927 10.9923 1.00559V3.78492C10.9923 4.32961 10.5481 4.77654 9.99295 4.77654Z" fill="white"/>
					<path d="M13.6435 5.81015C13.1716 5.53082 13.0051 4.9163 13.2826 4.44144L14.6706 2.0392C14.9482 1.56434 15.5588 1.39674 16.0307 1.67607C16.5026 1.9554 16.6692 2.56993 16.3916 3.04479L14.9898 5.44702C14.7261 5.92188 14.1154 6.08948 13.6435 5.81015Z" fill="white"/>
					<path d="M5.05192 5.44702L3.664 3.04479C3.38641 2.56993 3.55296 1.9554 4.02486 1.67607C4.49675 1.39674 5.10744 1.56434 5.38502 2.0392L6.77295 4.44144C7.05054 4.9163 6.88398 5.53082 6.41209 5.81015C5.94019 6.08948 5.32951 5.92188 5.05192 5.44702Z" fill="white"/>
				</g>
				<defs>
					<clipPath id="clip0_1245_1418">
						<rect width="20" height="20" fill="white"/>
					</clipPath>
				</defs>
			</svg>'
			);

	}

	/**
	 * Add Actions
	 *
	 * @since 2.0.0
	 * @return void Add the Actions.
	 */
	public function actions() {
		parent::actions();
		add_action( 'rest_api_init', array( $this, 'pass_user_roles_in_api' ), 10 );
		add_filter( 'rest_prepare_user', array( $this, 'prepare_rest_user_fields' ), 10, 3 );
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 1.0.0
	 * @return void The callback function of the Admin Menu Page.
	 */
	public function output() {
		View::instance()->render(
			array(
				'slug'           => $this->menu_slug,
				'unique_id'      => $this->unique_id,
				'site_connected' => $this->site_connected,
			)
		);

	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 1.0.0
	 * @return array Register scripts for the admin page.
	 */
	public function set_admin_scripts() {
		$script_data  = include WPMUDEV_BLC_DIR . 'assets/js/dashboard/main.asset.php';
		$dependencies = $script_data['dependencies'] ?? array(
			'react',
			'wp-element',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-polyfill',
		);
		$version      = $this->scripts_version();

		// In case Clipboard is required in order to import '@wpmudev/shared-ui' scripts.
		// $dependencies[] = 'clipboard';.

		return array(
			'blc_dashboard' => array(
				'src'       => $this->scripts_dir . 'dashboard/main.js',
				'deps'      => $dependencies,
				'ver'       => $version,
				'in_footer' => true,
				'localize'  => array(
					'blc_dashboard' => $this->localized_values(),
				),
				'translate' => true,
			),
			// END OF blc_dashboard.
		);
	}

	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/js/dashboard/main.asset.php';
			$scripts_version = $script_data['version'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}

	/**
	 * Returns all required localized values to be used with enqueued js.
	 *
	 * @return array
	 */
	protected function localized_values() {
		$user_roles = Dash_Model::list_user_roles();
		$signup_url = add_query_arg(
			array(
				'utm_source'   => 'blc',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'blc_plugin_onboarding',
			),
			Utilities::signup_url()
		);

		return array(
			'data'   => array(
				'rest_url'               => esc_url_raw( rest_url() ),
				'rest_url_full'          => esc_url_raw( rest_url() . 'wp/v2/' ),
				'settings_endpoint'      => '/wpmudev_blc/v1/settings', // app/rest-endpoints/settings.
				'avatar_endpoint'        => '/wpmudev_blc/v1/avatars',
				'scan_endpoint'          => '/wpmudev_blc/v1/scan',
				'sync_scan_endpoint'     => '/wpmudev_blc/v1/sync_scan',
				'unique_id'              => $this->unique_id,
				'nonce'                  => wp_create_nonce( 'wp_rest' ),
				'dash_installed'         => boolval( Utilities::dash_plugin_installed() ),
				'dash_active'            => boolval( Utilities::dash_plugin_active() ),
				'site_connected'         => boolval( $this->site_connected ),
				'expired_membership'     => boolval( Utilities::membership_expired() ),
				'use_legacy'             => esc_html( $this->use_legacy ),
				//'hub_url'            => esc_url( Utilities::hub_connect_url() ),
				'hub_home_url'           => esc_url( Utilities::hub_home_url() ),
				'hub_scan_url'           => esc_url( Utilities::hub_scan_url() ),
				'hub_signup_url'         => $signup_url,
				'hub_account_url'        => esc_url( Utilities::hub_account_url() ),
				'scan_results'           => $this->escape_array_fixed( Dash_Model::get_scan_results() ),
				'scan_in_progress'       => Dash_Model::scan_in_progress(),
				'site_url'               => esc_url_raw( get_site_url() ),
				'trigger_schedule_modal' => $this->trigger_schedule_modal(),
				'schedule'               => $this->escape_array_fixed( Dash_Model::get_schedule() ),
				'timezone'               => esc_html( Utilities::get_timezone_string( false ) ),
				'local_time'             => current_time( get_option( 'time_format' ) ),
				'hour_list'              => wp_json_encode( Dash_Model::get_hours_list() ),
				'week_days'              => wp_json_encode( Utilities::get_week_days() ),
				'current_admin'          => wp_json_encode( array( $this->escape_array_fixed( Dash_Model::get_current_user_data_formated() ) ) ),
				'searchLettersCount'     => 3,
				'usersPerPage'           => 10,
				'dummyAvatar'            => esc_url_raw( WPMUDEV_BLC_URL . '/assets/images/dummy-avatar.png' ),
				'allowedRoles'           => $user_roles,
				'allowedRolesSlugs'      => wp_list_pluck( $user_roles, 'role_slug' ),
				'cooldownData'           => $this->escape_array_fixed( Dash_Model::get_cooldown_data() ),
			), // End blc_dashboard/data.
			'labels' => array(
				'page_title'     => esc_html( $this->page_title ),
				'error_messages' => array(
					'general' => esc_html__( 'Something went wrong here.', 'broken-link-checker' ),
				),
			),
		);
	}

	/**
	 * Checks if schedule modal should pop up.
	 *
	 * @return bool
	 */
	public function trigger_schedule_modal() {
		return filter_input( INPUT_GET, 'set_schedule', FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Register css files for admin page.
	 *
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_sui'       => array(
				'src' => $this->styles_dir . 'shared-ui-' . BLC_SHARED_UI_VERSION_NUMBER . '.min.css',
				'ver' => $this->scripts_version(),
			),
			'blc_dashboard' => array(
				'src' => $this->styles_dir . 'dashboard.min.css',
				'ver' => $this->scripts_version(),
			),
		);
	}

	/**
	 * Adds Page specific hooks. Extends $this->actions.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function page_hooks() {
		add_filter( 'admin_body_class', array( $this, 'admin_dash_page_classes' ), 999 );
	}

	/**
	 * Returns the body css classes to bve added in admin page.
	 *
	 * @param array $classes The body classes.
	 */
	public function admin_dash_page_classes( $classes ) {
		if ( $this->can_boot() ) {
			return ( $this->site_connected && ! $this->use_legacy ) ? "{$classes} blc-dashboard" : "{$classes} blc-fullpage";
		}

		return $classes;
	}

	/**
	 * Pushes user roles in Rest response.
	 */
	public function pass_user_roles_in_api() {
		register_rest_field(
			'user',
			'roles',
			array(
				'get_callback'    => function ( $object, $field_name, $request ) {
					$referer = $request->get_header( 'referer' );

					if ( ! $referer || Utilities::get_query_var( $referer, 'page' ) !== $this->menu_slug ) {
						return '';
					}

					// Return up to the first 3 roles.
					return implode( ', ', array_slice( Utilities::user_role_names( $object['id'] ), 0, 3 ) );
				},
				'update_callback' => null,
				'schema'          => array(
					'type' => 'array',
				),
			)
		);
	}

	/**
	 * Remove not necessary user fields from Rest response nad include roles.
	 *
	 * @param object $data .
	 * @param object $user .
	 * @param object $request .
	 *
	 * @return mixed
	 */
	public function prepare_rest_user_fields( $data, $user, $request ) {
		$referer = $request->get_header( 'referer' );

		if ( ! $referer || Utilities::get_query_var( $referer, 'page' ) !== $this->menu_slug ) {
			return $data;
		}

		// In front end we are looking for `avatar` index.
		if ( isset( $data->data['avatar_urls'] ) && is_array( $data->data['avatar_urls'] ) ) {
			if ( isset( $data->data['avatar_urls'][30] ) ) {
				$data->data['avatar'] = $data->data['avatar_urls'][30];
			} else {
				$data->data['avatar'] = array_values( $data->data['avatar_urls'] )[0];
			}
		}

		unset( $data->data['avatar_urls'] );
		unset( $data->data['url'] );
		unset( $data->data['description'] );
		unset( $data->data['link'] );
		unset( $data->data['slug'] );
		unset( $data->data['meta'] );
		unset( $data->data['_links'] );

		$data->data['roles'] = implode( ', ', Utilities::user_role_names( $user->ID ) );

		return $data;
	}

}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST layer for the React Settings page. Doesn't redefine any field —
 * reads the exact same `DLM_Admin_Settings::get_settings()` tree the old
 * PHP-rendered page uses (populated by lite + every dlm-pro extension via
 * the `dlm_settings` filter), and adds `locked`/`badge`/`reason` metadata
 * per section/field based on the current plan + Extensions on/off state.
 */
class DLM_Settings_Rest {

	private static $instance;

	/**
	 * section_key => prefixed extension slug, for sections entirely owned
	 * by one extension (every field in the section is gated the same way).
	 */
	const SECTION_OWNERS = array(
		'email_lock'           => 'dlm-email-lock',
		'ninja_forms'          => 'dlm-ninja-forms',
		'gravity_forms'        => 'dlm-gravity-forms',
		'contact_form_7'       => 'dlm-cf7-lock',
		'wpforms'              => 'dlm-wpforms-lock',
		'password_lock'        => 'dlm-password-lock',
		'mailchimp_connection' => 'dlm-mailchimp-lock',
		'expiring_links'       => 'dlm-expiring-links',
		'captcha'              => 'dlm-captcha',
		'turnstile'            => 'dlm-captcha',
		'global'               => 'dlm-captcha',
		'google_drive'         => 'dlm-google-drive',
		'amazon_s3'            => 'dlm-amazon-s3',
		'email_notification'   => 'dlm-email-notification',
		'base_settings'        => 'dlm-page-addon',
		'table_settings'       => 'dlm-page-addon',
		'grid_settings'        => 'dlm-page-addon',
		'minimal_settings'     => 'dlm-page-addon',
	);

	/**
	 * Individual field `name` => prefixed extension slug, for fields a pro
	 * extension adds into an otherwise lite-owned section (only those
	 * specific fields are locked, the rest of the section stays open).
	 */
	const FIELD_OWNERS = array(
		'dlm_aam_shortcode_hide_no_access' => 'dlm-advanced-access-manager',
		'dlm_aam_pa_hide_no_access'        => 'dlm-advanced-access-manager',
		'dlm_remove_download_logs_after'   => 'dlm-enhanced-metrics',
		'dlm_dp_downloading_page'          => 'dlm-downloading-page',
		'dlm_pa_search_results_page'       => 'dlm-page-addon',
		'dlm_pa_persist_content'           => 'dlm-page-addon',
	);

	/**
	 * slug => plan tier it first appears in, for the upgrade badge label
	 * only (the actual lock decision comes from is_upgradable_addon()).
	 */
	const SLUG_TIER = array(
		'dlm-enhanced-metrics'         => 'basic',
		'dlm-captcha'                  => 'basic',
		'dlm-expiring-links'           => 'basic',
		'dlm-email-notification'       => 'popular',
		'dlm-csv-importer'             => 'popular',
		'dlm-csv-exporter'             => 'popular',
		'dlm-page-addon'               => 'popular',
		'dlm-downloading-page'         => 'popular',
		'dlm-amazon-s3'                => 'popular',
		'dlm-google-drive'             => 'popular',
		'dlm-advanced-access-manager'  => 'complete',
		'dlm-ninja-forms'              => 'complete',
		'dlm-gravity-forms'            => 'complete',
		'dlm-mailchimp-lock'           => 'complete',
		'dlm-email-lock'               => 'complete',
		'dlm-wpforms-lock'             => 'complete',
		'dlm-cf7-lock'                 => 'complete',
		'dlm-password-lock'            => 'complete',
	);

	/**
	 * Field types that never get saved (display-only), same list the
	 * classic renderer already excludes from register_setting().
	 */
	const DISPLAY_ONLY_TYPES = array( 'action_button', 'callback', 'title', 'desc', 'gateway_overview', 'htaccess_status', 'blacklist_status', 'drive_auth_button', 'console_uri_field', 'download_paths_table', 'api_keys_table', 'templates_table', 'page_addon_settings', 'mailchimp_connection_app', 'expiring_links_tokens_app' );

	public static function get_instance() {
		if ( ! isset( self::$instance ) || ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			'download-monitor/v1',
			'/settings-tabs',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_tabs' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/settings-pages',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_pages' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/settings-gateways',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_gateways' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/api-keys',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_api_keys' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_api_key' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/api-keys/(?P<id>\d+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_api_key' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/users-search',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'search_users' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/download-paths',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_download_paths' ),
					'permission_callback' => array( $this, 'download_paths_permissions_check' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'save_download_path' ),
					'permission_callback' => array( $this, 'download_paths_permissions_check' ),
				),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/download-paths/(?P<id>\d+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_download_path' ),
				'permission_callback' => array( $this, 'download_paths_permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/theme-templates',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_theme_templates' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/regenerate-htaccess',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'regenerate_htaccess' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		register_rest_route(
			'download-monitor/v1',
			'/regenerate-robots',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'regenerate_robots' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function regenerate_htaccess() {
		$upload_dir      = wp_upload_dir();
		$server_software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		$is_iis          = false !== stristr( $server_software, 'Microsoft-IIS' );
		$protection_file = $is_iis ? 'web.config' : '.htaccess';
		$protection_path = $upload_dir['basedir'] . '/dlm_uploads/' . $protection_file;
		$index_path      = $upload_dir['basedir'] . '/dlm_uploads/index.html';

		if ( file_exists( $protection_path ) ) {
			unlink( $protection_path );
		}

		if ( file_exists( $index_path ) ) {
			unlink( $index_path );
		}

		$this->create_upload_protection_files( $upload_dir, $is_iis );

		$success = file_exists( $protection_path ) && file_exists( $index_path );

		return new \WP_REST_Response( array( 'success' => $success ), $success ? 200 : 500 );
	}

	private function create_upload_protection_files( $upload_dir, $is_iis ) {
		if ( $is_iis ) {
			$webconfig_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
				'<configuration>' . "\n" .
				'    <system.web>' . "\n" .
				'        <authorization>' . "\n" .
				'            <deny users="*" />' . "\n" .
				'        </authorization>' . "\n" .
				'    </system.web>' . "\n" .
				'    <system.webServer>' . "\n" .
				'        <security>' . "\n" .
				'            <requestFiltering>' . "\n" .
				'                <denyUrlSequences>' . "\n" .
				'                    <add sequence="dlm_uploads" />' . "\n" .
				'                </denyUrlSequences>' . "\n" .
				'            </requestFiltering>' . "\n" .
				'        </security>' . "\n" .
				'    </system.webServer>' . "\n" .
				'</configuration>';

			$files = array(
				array(
					'base'    => $upload_dir['basedir'] . '/dlm_uploads',
					'file'    => 'web.config',
					'content' => $webconfig_content,
				),
				array(
					'base'    => $upload_dir['basedir'] . '/dlm_uploads',
					'file'    => 'index.html',
					'content' => '',
				),
			);
		} else {
			$htaccess_content = '# Apache 2.4 and up
	<IfModule mod_authz_core.c>
	Require all denied
	</IfModule>

	# Apache 2.3 and down
	<IfModule !mod_authz_core.c>
	Order Allow,Deny
	Deny from all
	</IfModule>';

			$files = array(
				array(
					'base'    => $upload_dir['basedir'] . '/dlm_uploads',
					'file'    => '.htaccess',
					'content' => $htaccess_content,
				),
				array(
					'base'    => $upload_dir['basedir'] . '/dlm_uploads',
					'file'    => 'index.html',
					'content' => '',
				),
			);
		}

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' );

				if ( $file_handle ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function regenerate_robots() {
		delete_transient( 'dlm_robots_txt' );

		$robots_file = sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) . '/robots.txt';
		$success     = false;

		if ( ! file_exists( $robots_file ) ) {
			$txt         = 'User-agent: *' . "\n" . 'Disallow: /dlm_uploads/';
			$dlm_robots  = fopen( $robots_file, 'w' );
			fwrite( $dlm_robots, $txt );
			fclose( $dlm_robots );
			$success = true;
		} else {
			$content = file_get_contents( $robots_file );

			if ( ! stristr( $content, 'dlm_uploads' ) ) {
				$txt        = 'User-agent: *' . "\n" . 'Disallow: /dlm_uploads/' . "\n\n" . $content;
				$dlm_robots = fopen( $robots_file, 'w' );
				fwrite( $dlm_robots, $txt );
				fclose( $dlm_robots );
				$success = true;
			}
		}

		return new \WP_REST_Response( array( 'success' => $success ), 200 );
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_theme_templates() {
		$status = \DLM_Plugin_Status::get_instance();
		$info   = $status->get_theme_info();

		$overrides = array();

		foreach ( (array) $info['overrides'] as $override ) {
			$core_version  = ! empty( $override['core_version'] ) ? $override['core_version'] : '';
			$theme_version = ! empty( $override['version'] ) ? $override['version'] : '';

			// $override['file'] is relative to WP_CONTENT_DIR . '/themes/' and starts
			// with the slug of whichever theme actually holds the override (the child
			// theme's stylesheet dir, or the parent template dir if not overridden
			// there) — derive the theme-editor.php params from that, don't assume the
			// parent/template slug, otherwise child-theme overrides 404 with "Sorry,
			// that file cannot be edited."
			$slash_pos     = strpos( $override['file'], '/' );
			$theme_slug    = false !== $slash_pos ? substr( $override['file'], 0, $slash_pos ) : $info['template'];
			$relative_file = false !== $slash_pos ? substr( $override['file'], $slash_pos + 1 ) : $override['file'];

			$overrides[] = array(
				'file'         => $override['file'],
				'version'      => $theme_version,
				'core_version' => $core_version,
				'needs_update' => $theme_version && $core_version && version_compare( $theme_version, $core_version, '<' ),
				'edit_url'     => admin_url(
					'theme-editor.php?' . http_build_query(
						array(
							'file'  => $relative_file,
							'theme' => $theme_slug,
						)
					)
				),
			);
		}

		return new \WP_REST_Response( array( 'overrides' => $overrides ), 200 );
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_api_keys() {
		global $wpdb;

		$rows   = $wpdb->get_results( "SELECT * FROM {$wpdb->dlm_api_keys} ORDER BY create_date DESC" );
		$result = array();

		foreach ( $rows as $row ) {
			$key  = new \DLM_API_Key( $row );
			$user = $key->get_user();

			$result[] = array(
				'id'          => $key->get_id(),
				'user_id'     => $key->get_user_id(),
				'username'    => $user ? $user->user_login : '',
				'user_email'  => $user ? $user->user_email : '',
				'public_key'  => $key->get_public_key(),
				'secret_key'  => $key->get_secret_key(),
				'token'       => $key->get_token(),
				'create_date' => $key->get_creation_date(),
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function create_api_key( \WP_REST_Request $request ) {
		$user_id = absint( $request->get_param( 'user_id' ) );

		if ( ! $user_id || ! get_userdata( $user_id ) ) {
			return new \WP_REST_Response( array( 'message' => 'invalid_user' ), 400 );
		}

		$created = \DLM_Key_Generation::get_instance()->generate_api_key( $user_id, true );

		if ( ! $created ) {
			return new \WP_REST_Response( array( 'message' => 'generation_failed' ), 400 );
		}

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function delete_api_key( \WP_REST_Request $request ) {
		$key = new \DLM_API_Key();

		if ( $key->get_key_by_id( absint( $request->get_param( 'id' ) ) ) ) {
			\DLM_Key_Generation::get_instance()->revoke_api_key( $key->get_user_id() );
		}

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function search_users( \WP_REST_Request $request ) {
		$term = sanitize_text_field( (string) $request->get_param( 'q' ) );

		$query = new \WP_User_Query(
			array(
				'search'         => '*' . $term . '*',
				'search_columns' => array( 'user_login', 'user_nicename', 'user_email', 'display_name' ),
				'number'         => 20,
			)
		);

		$result = array();

		foreach ( $query->get_results() as $user ) {
			$result[] = array(
				'value' => $user->ID,
				'label' => $user->display_name . ' (' . $user->user_email . ')',
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_download_paths() {
		$paths = \DLM_Downloads_Path_Helper::get_all_paths();

		return new \WP_REST_Response( array_values( (array) $paths ), 200 );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function save_download_path( \WP_REST_Request $request ) {
		$id       = absint( $request->get_param( 'id' ) );
		$path_val = trailingslashit( sanitize_text_field( (string) $request->get_param( 'path_val' ) ) );
		$enabled  = (bool) $request->get_param( 'enabled' );

		$paths = array_values( (array) \DLM_Downloads_Path_Helper::get_all_paths() );

		foreach ( $paths as $existing ) {
			if ( absint( $existing['id'] ) !== $id && trailingslashit( $existing['path_val'] ) === $path_val ) {
				return new \WP_REST_Response( array( 'message' => __( 'Path already exists.', 'download-monitor' ) ), 400 );
			}
		}

		if ( $id ) {
			foreach ( $paths as $key => $existing ) {
				if ( absint( $existing['id'] ) === $id ) {
					$paths[ $key ]['path_val'] = $path_val;
					$paths[ $key ]['enabled']  = $enabled;

					\DLM_Downloads_Path_Helper::save_paths( $paths );

					return new \WP_REST_Response( array( 'success' => true ), 200 );
				}
			}
		}

		$next_id = 1;

		foreach ( $paths as $existing ) {
			$next_id = max( $next_id, absint( $existing['id'] ) + 1 );
		}

		$paths[] = array(
			'id'       => $next_id,
			'path_val' => $path_val,
			'enabled'  => $enabled,
		);

		\DLM_Downloads_Path_Helper::save_paths( $paths );

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function delete_download_path( \WP_REST_Request $request ) {
		$id    = absint( $request->get_param( 'id' ) );
		$paths = array_values(
			array_filter(
				(array) \DLM_Downloads_Path_Helper::get_all_paths(),
				function ( $path ) use ( $id ) {
					return absint( $path['id'] ) !== $id;
				}
			)
		);

		\DLM_Downloads_Path_Helper::save_paths( $paths );

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_gateways() {
		$result = array();

		foreach ( $this->get_all_gateways() as $gateway ) {
			$result[] = array(
				'id'      => $gateway->get_id(),
				'title'   => $gateway->get_title(),
				'enabled' => $gateway->is_enabled(),
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * @return \WPChill\DownloadMonitor\Shop\Checkout\PaymentGateway\PaymentGateway[]
	 */
	private function get_all_gateways() {
		if ( ! class_exists( 'WPChill\DownloadMonitor\Shop\Services\Services' ) ) {
			return array();
		}

		return WPChill\DownloadMonitor\Shop\Services\Services::get()->service( 'payment_gateway' )->get_all_gateways();
	}

	/**
	 * @return string[]
	 */
	private function gateway_option_names() {
		$names = array();

		foreach ( $this->get_all_gateways() as $gateway ) {
			$names[] = 'dlm_gateway_' . $gateway->get_id() . '_enabled';
		}

		return $names;
	}

	/**
	 * Every current `lazy_select` field is a WP Page picker — one shared
	 * endpoint instead of re-registering a `dlm_settings_lazy_select_*`
	 * filter callback per field.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_pages() {
		$pages  = get_pages();
		$result = array();

		foreach ( $pages as $page ) {
			$result[] = array(
				'value' => $page->ID,
				'label' => $page->post_title,
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	public function permissions_check() {
		return current_user_can( 'manage_options' );
	}

	public function download_paths_permissions_check() {
		return DLM_Downloads_Path::get_instance()->check_access();
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_tabs() {
		$settings = ( new DLM_Admin_Settings() )->get_settings();
		$tabs     = array();

		foreach ( $settings as $tab_key => $tab ) {
			$sections = array();

			foreach ( $tab['sections'] as $section_key => $section ) {
				if ( empty( $section['fields'] ) ) {
					continue;
				}

				$owner                                          = isset( self::SECTION_OWNERS[ $section_key ] ) ? self::SECTION_OWNERS[ $section_key ] : null;
				list( $locked, $badge, $reason, $extension_name ) = $this->gate_for_slug( $owner );

				$sections[] = array(
					'slug'          => $section_key,
					'label'         => isset( $section['title'] ) ? $section['title'] : $section_key,
					'locked'        => $locked,
					'badge'         => $badge,
					'reason'        => $reason,
					'extensionName' => $extension_name,
					'fields'        => $this->prepare_fields( $section['fields'] ),
				);
			}

			if ( empty( $sections ) ) {
				continue;
			}

			$tabs[] = array(
				'slug'     => $tab_key,
				'label'    => isset( $tab['title'] ) ? $tab['title'] : $tab_key,
				'sections' => $sections,
			);
		}

		return new \WP_REST_Response( $tabs, 200 );
	}

	private function prepare_fields( $fields ) {
		$prepared = array();

		foreach ( $fields as $field ) {
			if ( 'group' === $field['type'] ) {
				$field['options'] = $this->prepare_fields( isset( $field['options'] ) ? $field['options'] : array() );
				$prepared[]       = $field;

				continue;
			}

			if ( 'callback' === $field['type'] && ! empty( $field['callback'] ) && is_callable( $field['callback'] ) ) {
				ob_start();
				call_user_func( $field['callback'] );
				$field['html'] = ob_get_clean();
				unset( $field['callback'] ); // not JSON-serializable
			}

			if ( 'drive_auth_button' === $field['type'] ) {
				$drive_token         = get_option( 'dlm_access_token', false );
				$field['connected']  = false !== $drive_token && ! empty( $drive_token['refresh_token'] );
				$base                = admin_url( 'edit.php?post_type=dlm_download&page=download-monitor-settings&tab=external_hosting' );
				$field['grant_url']  = esc_url_raw( add_query_arg( array(
					'action'   => 'oauth_grant',
					'_wpnonce' => wp_create_nonce( 'oauth_grant' ),
				), $base ) );
				$field['revoke_url'] = esc_url_raw( add_query_arg( array(
					'action'   => 'oauth_revoke',
					'_wpnonce' => wp_create_nonce( 'oauth_revoke' ),
				), $base ) );
			}

			if ( 'blacklist_status' === $field['type'] ) {
				$field = array_merge( $field, $this->blacklist_status() );
			}

			if ( ! empty( $field['name'] ) ) {
				$owner = isset( self::FIELD_OWNERS[ $field['name'] ] ) ? self::FIELD_OWNERS[ $field['name'] ] : null;

				if ( $owner ) {
					list( $locked, $badge, $reason, $extension_name ) = $this->gate_for_slug( $owner );

					$field['locked']        = $locked;
					$field['badge']         = $badge;
					$field['reason']        = $reason;
					$field['extensionName'] = $extension_name;
				}

				// console_uri_field carries its own precomputed 'value' (a URL
				// built from the site's own URL, not something we store).
				$field['default'] = 'console_uri_field' === $field['type']
					? ( isset( $field['value'] ) ? $field['value'] : '' )
					: get_option( $field['name'], isset( $field['std'] ) ? $field['std'] : '' );
			}

			$prepared[] = $field;
		}

		return $prepared;
	}

	/**
	 * Mirrors DLM_Pro\Blacklist\Field::render() as data instead of HTML —
	 * that class only knows how to `echo`, this REST layer needs JSON.
	 *
	 * @return array
	 */
	private function blacklist_status() {
		$timestamp   = (int) get_option( 'dlm_pro_blacklist_last_updated', 0 );
		$date_format = get_option( 'date_format', 'Y-m-d' ) . ' ' . get_option( 'time_format', 'H:i' );

		if ( $timestamp > 0 ) {
			return array(
				'updated'      => true,
				'status_label' => sprintf(
					/* translators: %s: date and time of last update */
					__( 'Agents list last updated on: %s', 'download-monitor' ),
					date_i18n( $date_format, $timestamp )
				),
			);
		}

		$next       = class_exists( 'DLM_Pro\Blacklist\Blacklist' ) ? wp_next_scheduled( DLM_Pro\Blacklist\Blacklist::CRON_HOOK ) : false;
		$next_label = $next ? date_i18n( $date_format, $next ) : __( 'unknown', 'download-monitor' );

		return array(
			'updated'      => false,
			'status_label' => sprintf(
				/* translators: %s: date and time of next scheduled sync */
				__( 'Agents list not yet synced. Next sync scheduled for: %s', 'download-monitor' ),
				$next_label
			),
		);
	}

	/**
	 * @param string|null $slug Prefixed extension slug, or null for lite/Shop.
	 *
	 * @return array{0: bool, 1: string|null, 2: string|null, 3: string|null} [locked, badge, reason, extension_name]
	 */
	private function gate_for_slug( $slug ) {
		if ( ! $slug ) {
			return array( false, null, null, null );
		}

		$provider = class_exists( 'DLM_Pro\Extensions\Extensions' )
			? DLM_Pro\Extensions\Extensions::get_instance()
			: DLM_Extensions_Base::get_instance();

		$name = isset( $provider->extensions[ $slug ]['name'] ) ? $provider->extensions[ $slug ]['name'] : $this->slug_to_name( $slug );

		if ( $provider->is_upgradable_addon( $slug ) ) {
			$badge = isset( self::SLUG_TIER[ $slug ] ) ? self::SLUG_TIER[ $slug ] : 'basic';

			return array( true, $badge, 'plan', $name );
		}

		if ( method_exists( $provider, 'extension_enabled' ) && ! $provider->extension_enabled( $slug ) ) {
			return array( true, null, 'disabled', $name );
		}

		return array( false, null, null, $name );
	}

	/**
	 * Fallback label when the extensions catalog hasn't been populated yet.
	 *
	 * @param string $slug Prefixed extension slug, e.g. 'dlm-captcha'.
	 *
	 * @return string
	 */
	private function slug_to_name( $slug ) {
		return ucwords( str_replace( array( 'dlm-', '-' ), array( '', ' ' ), $slug ) );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function update_settings( \WP_REST_Request $request ) {
		$submitted = $request->get_json_params();

		if ( ! is_array( $submitted ) ) {
			return new \WP_REST_Response( array( 'message' => 'invalid_payload' ), 400 );
		}

		$known = $this->flatten_known_fields( ( new DLM_Admin_Settings() )->get_settings() );

		foreach ( $submitted as $name => $value ) {
			if ( ! isset( $known[ $name ] ) ) {
				continue;
			}

			update_option( $name, $this->sanitize_value( $known[ $name ]['type'], $value, $known[ $name ] ) );
		}

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * @return array<string, array> field name => field definition, skipping display-only types.
	 */
	private function flatten_known_fields( $settings ) {
		$known = array();

		foreach ( $settings as $tab ) {
			foreach ( $tab['sections'] as $section ) {
				if ( empty( $section['fields'] ) ) {
					continue;
				}

				foreach ( $section['fields'] as $field ) {
					if ( 'group' === $field['type'] ) {
						foreach ( (array) ( $field['options'] ?? array() ) as $group_field ) {
							$this->add_known_field( $known, $group_field );
						}

						continue;
					}

					if ( 'gateway_overview' === $field['type'] ) {
						foreach ( $this->gateway_option_names() as $name ) {
							$known[ $name ] = array( 'type' => 'checkbox' );
						}

						continue;
					}

					$this->add_known_field( $known, $field );
				}
			}
		}

		return $known;
	}

	private function add_known_field( &$known, $field ) {
		if ( empty( $field['name'] ) || in_array( $field['type'], self::DISPLAY_ONLY_TYPES, true ) ) {
			return;
		}

		$known[ $field['name'] ] = $field;
	}

	private function sanitize_value( $type, $value, $field ) {
		switch ( $type ) {
			case 'checkbox':
				return $value ? '1' : '0';

			case 'select':
			case 'radio':
			case 'enhanced_radio':
				$options = isset( $field['options'] ) ? array_keys( $field['options'] ) : array();

				return ( empty( $options ) || in_array( $value, $options, true ) ) ? sanitize_text_field( $value ) : ( isset( $field['std'] ) ? $field['std'] : '' );

			case 'textarea':
			case 'editor':
			case 'el_code_editor':
				return wp_kses_post( $value );

			default:
				return sanitize_text_field( $value );
		}
	}
}

<?php

// uncomment this line for testing
// set_site_transient( 'update_plugins', null );

/* To test locally, change line 454 of
 * wp-admin/includes/file.php
 * from wp_safe_remote_get to wp_remote_get
 * (within function download_url)
 */

/**
 * Allows plugins to use their own update API.
 *
 * @author Pippin Williamson and Dan Lester
 * @version 13
 */
class EDD_SL_Plugin_Updater13 {
	private $api_url  = '';
	private $api_data = array();
	private $name     = '';
	private $slug     = '';

	private $license_status_optname = '';
	private $license_settings_url = '';

	private $license_warning_delay = 3600;

	private $display_warnings = true;
	private $cache_key = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string $_api_url The URL pointing to the custom API endpoint.
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array $_api_data Optional data to send with API calls.
	 * @return void
	 */
	function __construct( $_api_url, $_plugin_file, $_api_data = null,
		$license_status_optname = null, $license_settings_url = null, $display_warnings=true ) {
		$this->api_url  = trailingslashit( $_api_url );
		$this->api_data = $_api_data;
		$this->name     = plugin_basename( $_plugin_file );
		$this->slug     = basename( $_plugin_file, '.php');
		$this->version  = $_api_data['version'];
		$this->beta     = $_api_data['beta'];
		$this->display_warnings = $display_warnings;

		$this->cache_key = md5( serialize( $this->slug . $this->api_data['license'] ) );

		if (is_null($license_status_optname)) {
			$license_status_optname = 'eddsl_'.$this->slug;
		}
		$this->license_status_optname = $license_status_optname;

		if (!is_null($license_settings_url)) {
			$this->license_settings_url = $license_settings_url;
		}
	}

	/**
	 * Set up Wordpress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function setup_hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );

		// add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );

		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
	}

	public function admin_init() {
		if (!current_user_can( is_multisite() ? 'manage_network_options' : 'manage_options' )) {
			return;
		}

		// Setup hooks to display message if license is not valid
		$license_status = $this->get_license_status_option();
		if (isset($license_status['status']) && isset($license_status['result_cleared'])
		    && !$license_status['result_cleared']) {

			// Do they want it cleared?
			// $nothanks_url = add_query_arg( $this->license_status_optname.'_eddsl_action', 'no_thanks' );
			$queryname = $this->license_status_optname.'_eddsl_action';
			if (isset($_REQUEST[$queryname]) && $_REQUEST[$queryname]=='no_thanks') {
				$license_status = $this->get_license_status_option();
				$license_status['result_cleared'] = true;
				update_site_option($this->license_status_optname, $license_status);
				$this->license_status_option = $license_status;
			}
			elseif ($this->display_warnings) {
				if (is_multisite()) {
					add_action('network_admin_notices', Array($this, 'eddsl_license_notice'));
				}
				add_action('admin_notices', Array($this, 'eddsl_license_notice'));
			}
		}
	}

	protected $version_info = null;
	protected $auto_license_checked = false;

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update api just when Wordpress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native Wordpress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array $_transient_data Update array build by Wordpress.
	 * @return array Modified update array with custom plugin data.
	 */
	function pre_set_site_transient_update_plugins_filter( $_transient_data ) {

		global $pagenow;

		if( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		/* if( 'plugins.php' == $pagenow && is_multisite() ) {
			return $_transient_data;
		} */

		if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $this->name ] ) ) {

			if (is_null($this->version_info)) {
				$this->version_info = $this->api_request( 'get_version', array( 'slug' => $this->slug ) );
			}

			if ( false !== $this->version_info && is_object( $this->version_info ) && isset( $this->version_info->new_version ) ) {

				if( version_compare( $this->version, $this->version_info->new_version, '<' ) ) {
					$this->version_info->plugin = $this->name;
					$_transient_data->response[ $this->name ] = $this->version_info;

				}

				$_transient_data->last_checked = time();
				$_transient_data->checked[ $this->name ] = $this->version;

			}

		}

		// Do actual license check now
		if (!$this->auto_license_checked) {

			$api_check = $this->api_request( 'check_license', array( 'slug' => $this->slug ) );

			// Returns only valid or invalid, but also contains expires and renewal_link

			// Record latest status from license server
			$license_status = $this->update_license_status_option($api_check);
			$this->auto_license_checked = true;

			// If we got invalid, then trying activating now
			if (isset($license_status['status']) && ($license_status['status'] == 'invalid' || $license_status['status'] == 'site_inactive')) {
				$license_status = $this->edd_license_activate(); // calls update_license_status_option
			}
		}

		return $_transient_data;
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string  $file
	 * @param array   $plugin
	 */
	public function show_update_notification( $file, $plugin ) {

		if( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );

		$update_cache = get_site_transient( 'update_plugins' );

		if ( ! is_object( $update_cache ) || empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$cache_key    = md5( 'edd_plugin_' .sanitize_key( $this->name ) . '_version_info' . ($this->api_data['beta'] ? '-beta' :'-reg') );
			$version_info = $this->get_cached_version_info( $cache_key );

			if( false === $version_info ) {

				$version_info = $this->api_request( 'get_version', array( 'slug' => $this->slug ) ); // 'plugin_latest_version'

				$this->set_version_info_cache( $version_info, $cache_key );
			}


			if( ! is_object( $version_info ) ) {
				return;
			}

			if( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked = time();
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';

			$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'edd' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version )
				);
			} else {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s">update now</a>.', 'edd' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version ),
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) )
				);
			}

			echo '</div></td></tr>';
		}
	}


	protected function update_license_status_option($api_response)
	{

		if ($api_response === false || (is_null($api_response) && !empty($this->api_data['license']))) {
			return;
			// Probably unable to reach server this time
		}

		$license_status = array('license_id' => $this->api_data['license']);

		// Problem such as missing license key?
		if (empty($this->api_data['license']) && is_null($api_response)) {
			$license_status['status'] = 'empty';
		}
		else if (isset($api_response->license_check)) { // Probably called get_version
			$license_status['status'] = $api_response->license_check;
		}
		else if (isset($api_response->error)) { // activate_license
			$license_status['status'] = $api_response->error;
		}
		else if (isset($api_response->license)) {
			$license_status['status'] = $api_response->license; // check_license
		}
		else { // Call was activate_license or check_license
			$license_status['status'] = isset($api_response->error) ? $api_response->error :
				(isset($api_response->success) && $api_response->success ? 'valid' : 'invalid');
		}

		if (!in_array($license_status['status'], array('valid', 'invalid', 'missing', 'item_name_mismatch', 'invalid_item_id', 'expired', 'site_inactive', 'inactive', 'disabled', 'empty'))) {
			$license_status['status'] = 'invalid';
		}

		$license_status['expires'] = null;
		$license_status['expires_time'] = null;
		$license_status['expires_day'] = null;
		$license_status['renewal_link'] = null;
		$license_status['download_link'] = null;
		if (isset($api_response->expires)) {
			$expires_time = strtotime($api_response->expires);

			if ($expires_time) {
				$license_status['expires'] = $api_response->expires;
				$license_status['expires_time'] = $expires_time;
				$license_status['expires_day'] = date("j M Y", $expires_time);
			}
		}
		if (isset($api_response->renewal_link)) {
			$license_status['renewal_link'] = $api_response->renewal_link;
		}
		if (isset($api_response->download_link)) {
			$license_status['download_link'] = $api_response->download_link;
		}

		// Compare to existing option if any
		$license_status['last_check_time'] = $license_status['first_check_time'] = time();
		$license_status['result_cleared'] = false;
		$old_license_status = get_site_option($this->license_status_optname, true);
		if (is_array($old_license_status) && isset($old_license_status['license_id'])
		    && isset($old_license_status['status'])) {
			if ($old_license_status['license_id'] == $license_status['license_id']
			    && $old_license_status['status'] == $license_status['status']
			    && (isset($old_license_status['expires']) ? $old_license_status['expires'] : 0) == $license_status['expires']) {
				if (isset($old_license_status['first_check_time'])) {
					$license_status['first_check_time'] = $old_license_status['first_check_time'];
				}
				if (isset($old_license_status['result_cleared'])) {
					$license_status['result_cleared'] = $old_license_status['result_cleared'];
				}
			}
		}

		update_site_option($this->license_status_optname, $license_status);
		$this->license_status_option = $license_status;

		return $license_status;
	}

	protected $license_status_option = null;
	protected function get_license_status_option() {
		if (!is_null($this->license_status_option)) {
			return $this->license_status_option;
		}
		$this->license_status_option = get_site_option($this->license_status_optname, Array());
		return $this->license_status_option;
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed   $_data
	 * @param string  $_action
	 * @param object  $_args
	 * @return object $_data
	 */
	function plugins_api_filter( $_data, $_action = '', $_args = null ) {


		if ( $_action != 'plugin_information' ) {

			return $_data;

		}

		if ( ! isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {

			return $_data;

		}

		$to_send = array(
			'slug'   => $this->slug,
			'is_ssl' => is_ssl(),
			'fields' => array(
				'banners' => false, // These will be supported soon hopefully
				'reviews' => false
			)
		);

		$cache_key = 'edd_api_request_' . md5( serialize( $this->slug . $this->api_data['license'] . ($this->api_data['beta'] ? '-beta' :'-reg') ) );

		$edd_api_request_transient = $this->get_cached_version_info( $cache_key );

		//If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
		if ( empty( $edd_api_request_transient ) ){

			$api_response = $this->api_request( 'get_version', $to_send );  // 'plugin_information'

			// Expires in 3 hours
			$this->set_version_info_cache( $api_response, $cache_key );

			if ( false !== $api_response ) {
				$_data = $api_response;
			}

		} else {
			// Convert sections into an assoc array
			if (isset($edd_api_request_transient->sections)) {
				$new_sections = array();
				foreach ($edd_api_request_transient->sections as $k => $v) {
					$new_sections[$k] = $v;
				}
				$edd_api_request_transient->sections = $new_sections;
			}

			$_data = $edd_api_request_transient;
		}

		return $_data;
	}


	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array   $args
	 * @param string  $url
	 * @return object $array
	 */
	function http_request_args( $args, $url ) {
		// If it is an https request and we are performing a package download, disable ssl verification
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd-sl/package_download' ) ) {
			$args['sslverify'] = false;
		}
		return $args;
	}


	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string  $_action The requested action.
	 * @param array   $_data   Parameters for the API action.
	 * @return false||object
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version;

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug )
			return;

		if ( empty( $data['license'] ) )
			return;

		if( $this->api_url == home_url() ) {
			return false; // Don't allow a plugin to ping itself
		}

		$api_params = array(
			'edd_action' => $_action,
			'license'    => $data['license'],
			'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
			'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
			'slug'       => $data['slug'],
			'author'     => $data['author'],
			'url'        => home_url(),
			'beta'       => $data['beta']
		);

		// '?XDEBUG_SESSION_START=7777'
		$request = wp_remote_post( $this->api_url, array( 'timeout' => $this->get_timeout(), 'sslverify' => false, 'body' => $api_params ) );

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		}

		if (is_object($request)) {
			if ( isset( $request->sections ) ) {
				$request->sections = maybe_unserialize( $request->sections );
			} else {
				$request->sections = array();
			}
			$request->last_updated = null; // For changelog page only to avoid error
		}

		return $request;
	}

	private function get_timeout() {
		$timeout = intval(get_site_option('eddsl_dsl_license_timeout', '10'));
		return empty($timeout) ? 10 : $timeout;
	}

	public function show_changelog() {

		if( empty( $_REQUEST['edd_sl_action'] ) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action'] ) {
			return;
		}

		if( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'You do not have permission to install plugin updates', 'edd' ), __( 'Error', 'edd' ), array( 'response' => 403 ) );
		}

		$cache_key    = md5( 'edd_plugin_' . sanitize_key( $_REQUEST['plugin'] ) . '_version_info' . ($this->api_data['beta'] ? '-beta' :'-reg') );
		$version_info = $this->get_cached_version_info( $cache_key );

		if( false === $version_info ) {

			$api_params = array(
				'edd_action' => 'get_version',
				'item_name'  => isset( $this->api_data['item_name'] ) ? $this->api_data['item_name'] : false,
				'item_id'    => isset( $this->api_data['item_id'] ) ? $this->api_data['item_id'] : false,
				'slug'       => $_REQUEST['slug'],
				'author'     => isset($this->api_data['author']) ? $this->api_data['author'] : '',
				'url'        => home_url(),
				'beta'       => $this->api_data['beta']
			);

			$request = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			if ( ! is_wp_error( $request ) ) {
				$version_info = json_decode( wp_remote_retrieve_body( $request ) );
			}


			if ( ! empty( $version_info ) && isset( $version_info->sections ) ) {
				$version_info->sections = maybe_unserialize( $version_info->sections );
			} else {
				$version_info = false;
			}

			if( ! empty( $version_info ) ) {
				foreach( $version_info->sections as $key => $section ) {
					$version_info->$key = (array) $section;
				}
			}

			$this->set_version_info_cache( $version_info, $cache_key );

		}

		if( ! empty( $version_info ) && isset( $version_info->sections['changelog'] ) ) {
			echo '<div style="background:#fff;padding:10px;">' . $version_info->sections['changelog'] . '</div>';
		}

		exit;
	}


	public function edd_license_activate() {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $this->api_data['license'],
			'item_id' => $this->api_data['item_id'],
			'beta' => $this->api_data['beta']
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ),
			array( 'timeout' => $this->get_timeout(), 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		return $this->update_license_status_option($license_data);
	}

	public function eddsl_license_notice() {
		$license_status = $this->get_license_status_option();
		$msg = '';

		$yes_link = $this->license_settings_url ? $this->license_settings_url : '';
		$yes_text = 'Enter License';
		$yes_target = '';

		if (isset($license_status['status']) && $license_status['status'] != 'valid'
		    && (!isset($license_status['result_cleared']) || !$license_status['result_cleared'])) {
			// Wait a couple of days before warning about the issue - give them time to finish setup first
			if (!isset($license_status['first_check_time']) || $license_status['first_check_time'] < time() - $this->license_warning_delay) {

				if ($license_status['license_id'] == '') {
					$license_status['status'] = 'empty';
				}

				// 'valid', 'invalid', 'missing', 'item_name_mismatch', 'invalid_item_id', 'expired', 'site_inactive', 'inactive', 'disabled', 'empty'
				switch ($license_status['status']) {
					case 'missing':
						$msg = 'Your license key is not found in our system at all.';
						break;
					case 'item_name_mismatch':
					case 'invalid_item_id':
						$msg = 'The license key you entered is for a different product.';
						break;
					case 'expired':
						$msg = 'Your license key has expired.';
						if (isset($license_status['renewal_link']) && $license_status['renewal_link']) {
							$yes_link = $license_status['renewal_link'];
							$yes_text = 'Renew License';
							$yes_target = ' target="_blank" ';
						}
						break;
					case 'site_inactive':
						$msg = 'Your license key is not active for this website.';
						break;
					case 'inactive':
						$msg = 'Your license key is not active.';
						break;
					case 'disabled':
						$msg = 'Your license key has been disabled.';
						break;
					case 'empty':
						$msg = 'You have not entered your license key.';
						break;
					case 'invalid':
					default:
						$msg = 'Your license key is invalid.';
						break;

				}
			}
		}
		else if (isset($license_status['status']) && $license_status['status'] == 'valid'
		         && (!isset($license_status['result_cleared']) || !$license_status['result_cleared'])) {
			// License valid, but will it expire soon?
			if (isset($license_status['expires_time']) && $license_status['expires_time'] < time() + 24*60*60*30) {
				$msg = 'License will expire '.(isset($license_status['expires_day']) ? 'on '.$license_status['expires_day'] : 'soon');
				$msg .= '. Save 50% by renewing in advance!';
				if (isset($license_status['renewal_link']) && $license_status['renewal_link']) {
					$yes_link = $license_status['renewal_link'];
					$yes_text = 'Renew License';
					$yes_target = ' target="_blank" ';
				}
			}
		}

		if ($msg != '') {
			$nothanks_url = add_query_arg( $this->license_status_optname.'_eddsl_action', 'no_thanks' );
			echo '<div class="error"><p>';
			if (isset($this->api_data['item_name'])) {
				echo htmlentities('Alert for '.urldecode($this->api_data['item_name']).': ');
			}
			echo htmlentities($msg);
			if ($yes_link) {
				echo ' &nbsp; <a href="'.esc_attr($yes_link).'" class="button-secondary" '.$yes_target
				     .'>'.htmlentities($yes_text).'</a>';
			}
			echo '&nbsp;<a href="' . esc_url( $nothanks_url ) . '" class="button-secondary">Ignore</a>';
			echo '</p></div>';
		}
	}

	private function get_cached_version_info( $cache_key = '' ) {

		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		return json_decode($cache['value']);

	}

	private function set_version_info_cache( $value = '', $cache_key = '' ) {

		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+3 hours', current_time( 'timestamp' ) ),
			'value'   => json_encode($value)
		);

		update_option( $cache_key, $data );

	}
}

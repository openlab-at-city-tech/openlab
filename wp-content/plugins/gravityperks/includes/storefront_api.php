<?php

if( ! class_exists( 'GP_EDD_SL_Plugin_Updater' ) ) {
    require_once( GWPerks::get_base_path() . '/includes/GP_EDD_SL_Plugin_Updater.php' );
}

/**
* Interface for interacting with GW Store API and EDD Updater.
*
*/
class GWAPI {

    private $license_key;
    private $product;
    private $updater;
    private $author;

    private $_perk_update_data = null;

    function __construct($args) {

        extract( wp_parse_args( $args, array(
            'version'     => GWPerks::get_version(),
            'license'     => GWPerks::get_license_key(),
            'item_name'   => 'Gravity Perks',
            'author'      => 'David Smith',
            'plugin_file' => null,
        ) ) );

        $this->license_key = $license;
        $this->slug        = basename($plugin_file, '.php');
        $this->product     = $item_name;
        $this->author      = $author;

        $this->updater     = new GP_EDD_SL_Plugin_Updater( GW_STORE_URL, $plugin_file, compact('version', 'license', 'item_name', 'author') );

        $this->hook();

    }

    private function hook() {
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ), 99 );
        add_filter( 'plugins_api', array( $this, 'perks_plugins_api_filter' ), 100, 3 );
    }

    /**
    * Get all available perks from the store. If the $with_download parameter is set to true,
    * the store will validate the license and return the download URLs for each perk as well.
    *
    * @param mixed $with_download
    */
	public function get_perks( $with_download = false ) {

		if( ! $with_download ) {
			$perks = get_transient( 'gperks_get_perks' );
			if( ! empty( $perks ) )
				return $perks;
		}

		$api_params = self::get_api_args( array(
			'edd_action'        => 'get_perks',
			'name'              => $this->product,
			'license'           => $this->license_key,
			'with_download'     => $with_download,
			'author'            => $this->author
		) );

		add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

		$request_url  = esc_url_raw( GW_STORE_URL );
		$request_args = self::get_request_args( array( 'body' => urlencode_deep( $api_params ) ) );
		$response     = wp_remote_post( $request_url, $request_args );

		GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );
		if( ! $response ) {
			return false;
		}

		$perks = array();

		foreach( $response as $plugin_file => $perk ) {

			if( property_exists( $perk, 'sections' ) ) {
				$perk->sections = maybe_unserialize( $perk->sections );
			}

			$perks[$plugin_file] = $perk;

		}

		if( ! $with_download ) {
			set_transient( 'gperks_get_perks', $perks, 60 * 60 * 12 );
		}

		return ! empty( $perks ) ? $perks : false;
	}

	/**
	 * Get Dashboard Announcements
	 */
	public function get_dashboard_announcements() {

		$announcements = get_transient( 'gperks_get_dashboard_announcements' );

		if( ! empty( $announcements ) )
			return $announcements;

		$api_params = self::get_api_args( array(
			'edd_action' => 'get_dashboard_announcements'
		) );

		add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

		$request_url  = esc_url_raw( GW_STORE_URL );
		$request_args = self::get_request_args( array( 'body' => urlencode_deep( $api_params ) ) );
		$response     = wp_remote_post( $request_url, $request_args );

		GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );
		if( ! $response || ! is_array( $response ) ) {
			return false;
		}

		set_transient( 'gperks_get_dashboard_announcements', $response, 60 * 60 * 12 );

		return ! empty( $response ) ? $response : false;

	}

	/**
	 * This is the function that let's WordPress know if there is an update avialable
	 * for one of our perks.
	 *
	 * @param mixed $_transient_data
	 */
	public function pre_set_site_transient_update_plugins_filter( $_transient_data ) {

		/* Reduce number of requests when installing plugin. */
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'install-plugin' ) {
			return $_transient_data;
		}

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() start. Retrieves download package for individual perk auto-updates.' );

		if( empty( $_transient_data->response ) )
			$_transient_data->response = array();

		// check if our run-time cache is populated, save a little hassle of having to loop through this over and over
		if( is_array( $this->_perk_update_data ) ) {
			$_transient_data->response = array_merge( $_transient_data->response, $this->_perk_update_data );
			GravityPerks::log_debug( 'Cached update data available.' );
			GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() end. Returning cached update data.' );
			return $_transient_data;
		}

		GravityPerks::log_debug( 'Retrieving perk data.' );

		$remote_perks = $this->get_perks( true );
		$perk_update_data = array();

		GravityPerks::log_debug( print_r( $remote_perks, true ) );

		if( ! is_array( $remote_perks ) ) {
			GravityPerks::log_debug( 'Failed to retrieve remote perk data.' );
			return $_transient_data;
		}

		foreach($remote_perks as $remote_perk_file => $remote_perk) {

			$is_perk_installed = $this->is_perk_installed($remote_perk_file);
			$local_perk_version = $this->get_local_perk_version($remote_perk_file);

			if( $is_perk_installed && version_compare( $local_perk_version, $remote_perk->new_version, '<' ) ) {
				GravityPerks::log_debug( 'Perk update found. Adding to local perk update data.' . print_r( $remote_perk, true ) );
				$perk_update_data[$remote_perk_file] = $remote_perk;
			}
		}

		$_transient_data->response = array_merge( (array) $_transient_data->response, $perk_update_data );
		$this->_perk_update_data   = $perk_update_data;

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() end. Returning update data.' . print_r( $_transient_data, true ) );

		return $_transient_data;
	}

    /**
     * Provides download package when installing and information on the "View version x.x details" page.
     *
     * @uses api_request()
     *
     * @param mixed $_data
     * @param string $_action
     * @param object $_args
     * @return object $_data
     */
    public function perks_plugins_api_filter( $_data, $_action = '', $_args = null ) {

	    GravityPerks::log_debug( 'perks_plugins_api_filter() start. Retrieves download package and plugin info.' );

        $plugin_file = isset( $_args->slug ) ? $_args->slug : gwget( 'plugin' );
	    if( strpos( $plugin_file, '/' ) === false ) {
		    $plugin_file = sprintf( '%1$s/%1$s.php', $plugin_file );
	    }

        $is_perk = gwget( 'from' ) == 'gwp';

        if( ! $is_perk ) {
            $is_perk = GWPerk::is_perk( $plugin_file );
        }

		GravityPerks::log_debug( print_r( compact( 'slug', 'is_perk' ), true ) );

        if ( $_action != 'plugin_information' || ! $is_perk || ! $plugin_file ) {
	        return $_data;
        }

	    GravityPerks::log_debug( 'Yes! This is a perk.' );

	    add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

	    $request_url = esc_url_raw( GW_STORE_URL );

        $api_params = self::get_api_args( array(
            'edd_action'  => 'get_perk',
            'plugin_file' => $plugin_file,
            'license'     => $this->license_key
        ) );

        $request_args = self::get_request_args( array(
            'body' => urlencode_deep( $api_params )
        ) );

        $response = wp_remote_post( $request_url, $request_args );

	    GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

        GravityPerks::log_debug( 'API Parameters: ' . print_r( $api_params, true ) );
        GravityPerks::log_debug( 'Request Arguments: ' . print_r( $request_args, true ) );
	    GravityPerks::log_debug( 'Response from GW API: ' . print_r( $response, true ) );

        if ( is_wp_error( $response ) ) {
	        GravityPerks::log_debug( 'Rats! There was an error with the GW API response' );
	        return $_data;
        }

        $perk = json_decode( wp_remote_retrieve_body( $response ) );
        if( ! $response ) {
	        GravityPerks::log_debug( 'Rats! There was an error with the GW API response' );
	        return $_data;
        }

	    GravityPerks::log_debug( 'Ok! Everything looks good. Let\'s build the response needed for WordPress.' );

        $perk->is_perk = true;
        $perk->sections = (array) maybe_unserialize( $perk->sections );
        $perk->sections['changelog'] = GWPerks::format_changelog( $perk->sections['changelog'] );

        // don't allow other plugins to override the $request this function returns, several plugins use the 'plugins_api'
        // filter incorrectly and return a hard 'false' rather than returning the $_data object when they do not need to modify
        // the request which results in our customized $request being overwritten (WPMU Dev Dashboard v3.3.2 is one example)
        remove_all_filters( 'plugins_api' );

        // remove all the filters causes an infinite loop so add one dummy function so the loop can break itself
        add_filter( 'plugins_api', array( new GP_Late_Static_Binding(), 'GWAPI_dummy_func' ) );

        // needed for testing on local
        add_filter( 'http_request_args', array( $this, 'allow_unsecure_urls_on_localhost' ) );

	    GWPerks::flush_license();

	    return $perk;
    }

    public function allow_unsecure_urls_on_localhost( $request ) {

        if( GWPerks::is_local() ) {
	        $request['reject_unsafe_urls'] = false;
        }

        return $request;
    }

    public function get_product_name() {
        return $this->product;
    }

    private function is_perk_installed($plugin_file) {
        $installed_perks = GWPerks::get_installed_perks();
        return isset($installed_perks[$plugin_file]);
    }

    private function get_local_perk_version($plugin_file) {
        $installed_perks = GWPerks::get_installed_perks();
        return isset($installed_perks[$plugin_file]) ? $installed_perks[$plugin_file]['Version'] : false;
    }

    public function get_license_data( $flush = false ) {

	    if( ! $flush ) {
		    $license_data = get_transient( 'gwp_license_data' );
		    if( $license_data !== false )
			    return $license_data;
	    }

	    $license = GWPerks::get_license_key();

	    // 'check_license' is a standard EDD API action
	    $api_params = self::get_api_args( array(
		    'edd_action' => 'check_license',
		    'license'    => $license,
		    'item_name'  => urlencode( $this->get_product_name() ),
	    ) );

	    add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

	    $request_url  = esc_url_raw( add_query_arg( $api_params, GW_STORE_URL ) );
	    $request_args = self::get_request_args();
	    $response     = wp_remote_get( $request_url, $request_args );

	    GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

	    $has_valid_license = false;
	    $license_data = false;

	    if ( ! is_wp_error( $response ) ) {

		    $license_data = json_decode( wp_remote_retrieve_body( $response ), ARRAY_A );

		    if( is_array( $license_data ) ) {

			    // at some point EDD added 'site_inactive' status which indicates the license has not been activated for this
			    // site even though it already might have been, go ahead and activate it and see if it is still active
			    if( in_array( $license_data['license'], array( 'inactive', 'site_inactive' ) ) ) {
				    $has_valid_license = $this->activate_license( $license );
			    } else {
				    $has_valid_license = $license_data['license'] == 'valid';
			    }

		    }

		    $license_data['valid'] = $has_valid_license;

	    }

	    set_transient( 'gwp_license_data', $license_data, 60 * 60 * 24 ); // cache license daily

	    return $license_data;

    }

    public function has_valid_license( $flush = false ) {

        if( ! $flush ) {
            $has_valid_license = get_transient( 'gwp_has_valid_license' );
            if( $has_valid_license !== false ) {
	            return $has_valid_license == true;
            }
        }

        $has_valid_license = false;

        if ( $license_data = $this->get_license_data( $flush ) ) {
	        $has_valid_license = isset( $license_data['valid'] ) && $license_data['valid'] ? 1 : 0;
        }

        set_transient( 'gwp_has_valid_license', $has_valid_license, 60 * 60 * 24 ); // cache license daily

        return $has_valid_license;
    }

    public function get_api_status() {

	    $api_params = self::get_api_args( array(
		    'edd_action' => 'get_api_status',
	    ) );

	    add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

	    $request_url  = esc_url_raw( add_query_arg( $api_params, GW_STORE_URL ) );
	    $request_args = self::get_request_args();
	    $response     = wp_remote_get( $request_url, $request_args );

	    GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

	    return wp_remote_retrieve_response_code( $response );
    }

    public function log_http_request_args( $args ) {
	    remove_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );
	    GravityPerks::log( print_r( compact( 'args' ), true ) );
	    return $args;
    }

    public function activate_license( $license ) {

        $api_params = self::get_api_args( array(
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_name'  => urlencode( $this->get_product_name() ),
        ) );

	    add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

        $request_url  = esc_url_raw( add_query_arg( $api_params, GW_STORE_URL ) );
        $request_args = self::get_request_args();
        $response     = wp_remote_get( $request_url, $request_args );

	    GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        return $license_data->license == 'valid';
    }

    public function deactivate_license() {

    	$gwp_settings = get_site_option('gwp_settings');
	    $license = rgar($gwp_settings, 'license_key');

	    if (!$license) {
	    	return false;
	    }

	    $api_params = self::get_api_args( array(
		    'edd_action' => 'deactivate_license',
		    'license'    => $license,
		    'item_name'  => urlencode( $this->get_product_name() ),
	    ) );

	    add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

	    $request_url  = esc_url_raw( add_query_arg( $api_params, GW_STORE_URL ) );
	    $request_args = self::get_request_args();
	    $response     = wp_remote_get( $request_url, $request_args );

	    GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

	    return $response;
    }

	public function register_perk( $perk_id ) {

		$license = GWPerks::get_license_key();

		$api_params = self::get_api_args( array(
			'edd_action' => 'register_perk',
			'json'       => true,
			'license'    => $license,
			'perk_id'    => $perk_id
		) );

		add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

		$request_url  = esc_url_raw( add_query_arg( $api_params, GW_STORE_URL ) );
		$request_args = self::get_request_args();
		$response     = wp_remote_get( $request_url, $request_args );

		GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		return rgar( $body, 'success' );
	}

    public function deregister_perk( $perk_id ) {

	    $license = GWPerks::get_license_key();

	    $api_params = self::get_api_args( array(
		    'edd_action' => 'deregister_perk',
		    'json'       => true,
		    'license'    => $license,
		    'perk_id'    => $perk_id
	    ) );

	    add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

	    $request_url  = esc_url_raw( add_query_arg( $api_params, GW_STORE_URL ) );
	    $request_args = self::get_request_args();
	    $response     = wp_remote_get( $request_url, $request_args );

	    GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

	    $body = json_decode( wp_remote_retrieve_body( $response ) );

	    return rgar( $body, 'success' );
    }

    public static function get_api_args( $args = array() ) {
        return wp_parse_args( $args, array(
            'url'     => self::get_site_url(),
	        'timeout' => 15
        ) );
    }

    public static function get_request_args( $args = array() ) {
	    return wp_parse_args( $args, array(
		    'user-agent' => 'Gravity Perks ' . GWPerks::get_version(),
		    'timeout'    => 15,
		    'sslverify'  => (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true ),
	    ) );
    }

    public static function get_site_url() {
        return site_url( '', 'http' );
    }

}

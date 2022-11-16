<?php

/**
 * Interface for interacting with GravityWiz.com
 *
 * This class is responsible for:
 *
 * * Validating licenses
 * * Updating GP & Perks
 * * Installing Perks
 * * Pulling Perk WP Plugin Info
 * * Registering Perks
 * * Deregistering Perks
 * * Getting Announcements
 *
 * @version 2.0
 */
class GWAPI {

	private $license_key;
	private $product;
	private $author;

	private $_product_update_data = null;

	const TRANSIENT_EXPIRATION = 43200; //60 * 60 * 12 = 12 hours

	function __construct( $args ) {

		/**
		* @var $license
		* @var $plugin_file
		* @var $item_name
		* @var $author
		* @var $versiion
		*/
		extract( wp_parse_args( $args ) );

		$this->license_key = $license;
		$this->slug        = basename( $plugin_file, '.php' );
		$this->product     = $item_name;
		$this->author      = $author;

		$this->hook();

	}

	private function request( $args ) {

		/**
		* @var $action
		* @var $api_params
		* @var $callback
		* @var $method
		* @var $cache
		* @var $flush
		* @var $transient
		* @var $cache_expiration
		* @var $output
		*/
		extract( wp_parse_args( $args, array(
			'action'           => '',
			'api_params'       => array(),
			'callback'         => null,
			'method'           => 'GET',
			'cache'            => true,
			'flush'            => false,
			'transient'        => null,
			'cache_expiration' => self::TRANSIENT_EXPIRATION,
			'output'           => ARRAY_A,
		) ) );

		if ( ! $transient ) {
			$transient = 'gwapi_' . $action;
		}

		if ( $cache && ! $flush ) {
			$cached = get_site_transient( $transient );

			if ( $cached !== false ) {
				return $cached;
			}
		}

		$request_url = esc_url_raw( GWAPI_URL );

		$api_params = self::get_api_args( array_merge( array(
			'edd_action' => $action,
		), $api_params ) );

		/* This filter is automatically removed after running */
		add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

		switch ( strtoupper( $method ) ) {
			case 'POST':
				$request_args = self::get_request_args( array( 'body' => urlencode_deep( $api_params ) ) );
				$response     = wp_remote_post( $request_url, $request_args );
				break;

			case 'GET':
			default:
				$request_args = self::get_request_args();
				$request_url  = add_query_arg( $api_params, $request_url );
				$response     = wp_remote_get( $request_url, $request_args );
				break;
		}

		GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

		if ( is_wp_error( $response ) ) {
			if ( $cache ) {
				set_site_transient( $transient, null, $cache_expiration );
			}

			return false;
		}

		if ( $output === 'code' ) {
			return wp_remote_retrieve_response_code( $response );
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response      = json_decode( $response_body, $output === ARRAY_A );

		/**
		* We check that the response is not an array as an empty array evaluates as false when it is a valid response
		* in this situation.
		*/
		if ( ! $response && ! is_array( $response ) ) {
			if ( $cache ) {
				set_site_transient( $transient, null, $cache_expiration );
			}

			return false;
		}

		if ( is_callable( $callback ) ) {
			$response = call_user_func( $callback, $response );
		}

		if ( $cache ) {
			set_site_transient( $transient, $response, $cache_expiration );
		}

		return $response;

	}

	private function hook() {

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ), 99999 );
		add_filter( 'plugins_api', array( $this, 'products_plugins_api_filter' ), 100, 3 );
		add_filter( 'http_request_host_is_external', array( $this, 'allow_gwiz_external_redirects' ), 15, 3 );

	}

	public function allow_gwiz_external_redirects( $allow, $host, $url ) {

		if ( $host === GW_DOMAIN ) {
			return true;
		}

		return $allow;

	}

	/**
	* Get all available perks from the store.
	*/
	public function get_products( $flush = false ) {

		return $this->request( array(
			'action'   => 'get_products',
			'output'   => OBJECT,
			'callback' => array( __CLASS__, 'process_get_products' ),
			'flush'    => $flush,
		) );

	}

	public static function process_get_products( $response ) {

		$perks = array();

		foreach ( $response as $plugin_file => $perk ) {

			if ( property_exists( $perk, 'sections' ) ) {
				$perk->sections = maybe_unserialize( $perk->sections );
			}

			$license = GravityPerks::get_license_data();

			$perk->package = str_replace(
				array(
					'%URL%',
					'%LICENSE_ID%',
					'%LICENSE_HASH%',
				),
				array(
					rawurlencode( GWAPI::get_site_url() ),
					rawurlencode( isset( $license['ID'] ) ? $license['ID'] : '' ),
					rawurlencode( md5( GWPerks::get_license_key() ) ),
				),
				$perk->package
			);

			$perk->download_link = $perk->package;

			$perks[ $plugin_file ] = $perk;

		}

		return ! empty( $perks ) ? $perks : false;

	}

	/**
	* Get Dashboard Announcements
	*/
	public function get_dashboard_announcements() {

		return $this->request( array(
			'action' => 'get_dashboard_announcements',
			'output' => OBJECT,
		) );

	}

	/**
	* This is the function that let's WordPress know if there is an update avialable
	* for one of our products.
	*
	* @param mixed $_transient_data
	*/
	public function pre_set_site_transient_update_plugins_filter( $_transient_data ) {

		/* Make sure this is only ran once. */
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ), 99 );

		$force_check = rgget( 'force-check' ) == 1;

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() start. Retrieves download package for individual prodyct auto-updates.' );

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if ( empty( $_transient_data->response ) ) {
			$_transient_data->response = array();
		}

		// check if our run-time cache is populated, save a little hassle of having to loop through this over and over
		if ( is_array( $this->_product_update_data ) && ! $force_check ) {
			$_transient_data->response = array_merge( $_transient_data->response, $this->_product_update_data );
			GravityPerks::log_debug( 'Cached update data available.' );
			GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() end. Returning cached update data.' );

			return $_transient_data;
		}

		GravityPerks::log_debug( 'Retrieving product data.' );

		$remote_products     = $this->get_products( $force_check );
		$product_update_data = array();

		GravityPerks::log_debug( print_r( $remote_products, true ) );

		if ( ! is_array( $remote_products ) ) {
			GravityPerks::log_debug( 'Failed to retrieve remote product data.' );

			return $_transient_data;
		}

		foreach ( $remote_products as $remote_product_file => $remote_product ) {
			$local_product_version = $this->get_local_product_version( $remote_product_file );

			if ( $local_product_version && version_compare( $local_product_version, $remote_product->new_version, '<' ) ) {
				GravityPerks::log_debug( 'Product update found. Adding to local product update data.' . print_r( $remote_product, true ) );
				$product_update_data[ $remote_product_file ] = $remote_product;
			}
		}

		$_transient_data->response  = array_merge( (array) $_transient_data->response, $product_update_data );
		$this->_product_update_data = $product_update_data;

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
	*
	* @return object $_data
	*/
	public function products_plugins_api_filter( $_data, $_action = '', $_args = null ) {

		GravityPerks::log_debug( 'products_plugins_api_filter() start. Retrieves download package and plugin info.' );

		$plugin_file = isset( $_args->slug ) ? $_args->slug : gwget( 'plugin' );
		if ( strpos( $plugin_file, '/' ) === false ) {
			$plugin_file = sprintf( '%1$s/%1$s.php', $plugin_file );
		}

		if ( $_action != 'plugin_information' || ! $plugin_file ) {
			return $_data;
		}

		GravityPerks::log_debug( 'Yes! This is a Gravity Wiz product.' );

		$remote_products = $this->get_products();

		if ( ! $remote_products ) {
			GravityPerks::log_debug( 'Rats! There was an error with the GW API response' );

			return $_data;
		}

		$product = rgar( $remote_products, $plugin_file );

		if ( ! $product ) {
			return $_data;
		}

		$product->sections['changelog'] = GWPerks::format_changelog( $product->sections['changelog'] );

		GravityPerks::log_debug( 'Ok! Everything looks good. Let\'s build the response needed for WordPress.' );

		// don't allow other plugins to override the $request this function returns, several plugins use the 'plugins_api'
		// filter incorrectly and return a hard 'false' rather than returning the $_data object when they do not need to modify
		// the request which results in our customized $request being overwritten (WPMU Dev Dashboard v3.3.2 is one example)
		remove_all_filters( 'plugins_api' );

		// remove all the filters causes an infinite loop so add one dummy function so the loop can break itself
		add_filter( 'plugins_api', array( new GP_Late_Static_Binding(), 'GWAPI_dummy_func' ) );

		// @TODO only do this if the product is being installed
		GWPerks::flush_license();

		return $product;

	}

	public function get_product_name() {
		return $this->product;
	}

	private function get_local_product_version( $plugin_file ) {
		$installed_plugins = GWPerks::get_plugins();

		return isset( $installed_plugins[ $plugin_file ] ) ? $installed_plugins[ $plugin_file ]['Version'] : false;
	}

	public function get_license_data( $flush = false ) {

		return $this->request( array(
			'action'     => 'check_license',
			'method'     => 'POST',
			'transient'  => 'gwp_license_data',
			'flush'      => $flush,
			'cache'      => true,
			'callback'   => array( $this, 'process_license_data' ),
			'api_params' => array(
				'license'   => GWPerks::get_license_key(),
				'item_name' => urlencode( $this->get_product_name() ),
			),
		) );

	}

	public function process_license_data( $response ) {

		$license           = GWPerks::get_license_key();
		$has_valid_license = false;

		if ( is_array( $response ) ) {

			// at some point EDD added 'site_inactive' status which indicates the license has not been activated for this
			// site even though it already might have been, go ahead and activate it and see if it is still active
			if ( in_array( $response['license'], array( 'inactive', 'site_inactive' ) ) ) {
				$has_valid_license = $this->activate_license( $license );
			} else {
				$has_valid_license = $response['license'] == 'valid';
			}
		}

		$response['valid'] = $has_valid_license;

		return $response;

	}

	public function has_valid_license( $flush = false ) {

		$license_data = $this->get_license_data( $flush );

		return isset( $license_data['valid'] ) && $license_data['valid'];

	}

	public function get_api_status() {

		return $this->request( array(
			'action' => 'get_api_status',
			'cache'  => false,
			'output' => 'code',
		) );

	}

	public function log_http_request_args( $args ) {
		remove_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );
		GravityPerks::log( print_r( compact( 'args' ), true ) );

		return $args;
	}

	public function activate_license( $license ) {

		$response = $this->request( array(
			'action'     => 'activate_license',
			'api_params' => array(
				'license'   => $license,
				'item_name' => urlencode( $this->get_product_name() ),
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

		return rgar( $response, 'license' ) === 'valid';

	}

	public function deactivate_license() {

		$gwp_settings = get_site_option( 'gwp_settings' );
		$license      = rgar( $gwp_settings, 'license_key' );

		if ( ! $license ) {
			return false;
		}

		return $this->request( array(
			'action'     => 'deactivate_license',
			'api_params' => array(
				'license'   => $license,
				'item_name' => urlencode( $this->get_product_name() ),
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

	}

	public function register_perk( $perk_id ) {

		$response = $this->request( array(
			'action'     => 'register_perk',
			'api_params' => array(
				'license' => GWPerks::get_license_key(),
				'perk_id' => $perk_id,
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

		return rgar( $response, 'success' );

	}

	public function deregister_perk( $perk_id ) {

		$response = $this->request( array(
			'action'     => 'deregister_perk',
			'api_params' => array(
				'license' => GWPerks::get_license_key(),
				'perk_id' => $perk_id,
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

		return rgar( $response, 'success' );

	}

	public static function get_api_args( $args = array() ) {
		return wp_parse_args( $args, array(
			'url'     => self::get_site_url(),
			'timeout' => 15,
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

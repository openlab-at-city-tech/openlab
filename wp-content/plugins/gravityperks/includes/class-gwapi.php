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
	private $gcgs_upgrade_successful = false;
	private $should_activate_gcgs = false;

	private $_product_update_data = array(
		'loaded'    => false,
		'response'  => array(),
		'no_update' => array(),
	);

	/**
	 * @var string The slug of the plugin sending the request.
	 */
	public $slug;

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

		// Suffix transient with the current Gravity Perks version in case behavior changes between versions.
		$transient = $transient . '_' . GRAVITY_PERKS_VERSION;

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
		add_filter( 'upgrader_package_options', array( $this, 'upgrader_package_options_filter' ) );
		add_filter( 'upgrader_post_install', array( $this, 'gpgs_to_gcgs_upgrader_post_install' ), 10, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'gpgs_to_gcgs_upgrader_process_complete' ), 10, 2 );

	}

	public function gpgs_to_gcgs_upgrader_post_install( $response, $hook_extra, $result ) {
		if ( rgar( $hook_extra, 'plugin' ) !== 'gp-google-sheets/gp-google-sheets.php' ) {
			return $response;
		}

		if ( is_plugin_active( 'gp-google-sheets/gp-google-sheets.php' ) ) {
			// Unhook Action Scheduler during this request to prevent errors.
			if ( class_exists( 'ActionScheduler_QueueRunner' ) ) {
				remove_action( 'shutdown', array( ActionScheduler_QueueRunner::instance(), 'maybe_dispatch_async_request' ) );
			}

			deactivate_plugins( 'gp-google-sheets/gp-google-sheets.php' );

			/*
			 * I don't know the technical reasoning behind this, but if we try activating GCGS here, it doesn't
			 * end up getting activated. To work around this, we set a property and do it later during
			 * upgrader_process_complete.
			 */
			$this->should_activate_gcgs = true;
		}

		$this->gcgs_upgrade_successful = true;

		return $response;
	}

	public function gpgs_to_gcgs_upgrader_process_complete( $upgrader, $hook_extra ) {
		if ( rgar( $hook_extra, 'action' ) !== 'update' || rgar( $hook_extra, 'type' ) !== 'plugin' ) {
			return;
		}

		if ( ! is_array( rgar( $hook_extra, 'plugins' ) ) || ! in_array( 'gp-google-sheets/gp-google-sheets.php', $hook_extra['plugins'], true ) ) {
			return;
		}

		if ( ! $this->gcgs_upgrade_successful ) {
			return;
		}

		$upgrader->skin->feedback( __( '<strong>Important Note!</strong> GP Google Sheets has been converted to GC Google Sheets', 'gravityperks' ) );

		if ( $this->should_activate_gcgs ) {
			activate_plugin( 'gc-google-sheets/gc-google-sheets.php' );
			$upgrader->skin->feedback( __( 'GC Google Sheets has been activated.', 'gravityperks' ) );
		}
	}

	public function upgrader_package_options_filter( $options ) {

		if ( ! isset( $options['package'] ) || ! is_string( $options['package'] ) ) {
			return $options;
		}

		if ( strpos( $options['package'], GW_DOMAIN ) === false ) {
			return $options;
		}

		$plugin_file     = rgars( $options, 'hook_extra/plugin' );
		$product_version = '';

		if ( $plugin_file ) {
			$product_version = $this->get_local_product_version( $plugin_file );

			/*
			* If the plugin name starts with anything outside of "gw" or "gp-" and is not "gravityperks"
			* bail as we do not want to conflict with GSPC or anything else.
			*/
			if ( ! preg_match( '/^(gw|gp-)/', $plugin_file ) && $plugin_file !== 'gravityperks/gravityperks.php' ) {
				return $options;
			}
		}

		$license = GravityPerks::get_license_data();

		$options['package'] = str_replace(
			array(
				'%URL%',
				'%LICENSE_ID%',
				'%LICENSE_HASH%',
				'%PRODUCT_VERSION%',
			),
			array(
				rawurlencode( GWAPI::get_site_url() ),
				rawurlencode( isset( $license['ID'] ) ? $license['ID'] : '' ),
				rawurlencode( md5( GWPerks::get_license_key() ) ),
				$product_version,
			),
			$options['package']
		);

		/*
		 * If we're installing a new perk, flush the license info. We know we're installing a new perk if
		 * $abort_if_destination_exists is true.
		 */
		if ( rgar( $options, 'abort_if_destination_exists' ) ) {
			GravityPerks::flush_license();
		}

		return $options;

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
			'callback' => array( $this, 'process_get_products' ),
			'flush'    => $flush,
		) );

	}

	public function process_get_products( $response ) {

		$perks = array();

		foreach ( $response as $plugin_file => $perk ) {

			if ( property_exists( $perk, 'sections' ) ) {
				$perk->sections = maybe_unserialize( $perk->sections );
			}

			$perk->download_link = $perk->package;

			// If GC Google Sheets is not installed, convert GCGS to be GPGS to provide an upgrade path.
			if ( $plugin_file === 'gc-google-sheets/gc-google-sheets.php' && ! class_exists( 'GC_Google_Sheets' ) ) {
				$plugin_file = 'gp-google-sheets/gp-google-sheets.php';
				$perk->slug = 'gp-google-sheets';
				$perk->plugin = 'gp-google-sheets';
				$perk->plugin_file = 'gp-google-sheets/gp-google-sheets.php';
			}

			if ( isset( $perk->icons ) ) {
				$icons = maybe_unserialize( $perk->icons );

				if ( is_array( $icons ) && ! empty( $icons ) ) {
					$perk->icons = $icons;
				}
			}

			if ( isset( $perk->banners ) ) {
				$banners = maybe_unserialize( $perk->banners );

				if ( is_array( $banners ) && ! empty( $banners ) ) {
					$perk->banners = $banners;
				}
			}

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
	* This is the function that lets WordPress know if there is an update available
	* for one of our products.
	*
	* @param mixed $_transient_data
	*/
	public function pre_set_site_transient_update_plugins_filter( $_transient_data ) {
		// Force check on initial request, but not subsequent requests through this filter.
		static $force_check = null;

		if ( $force_check === null ) {
			$force_check = rgget( 'force-check' ) == 1;
		}

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() start. Retrieves download package for individual product auto-updates.' );

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if ( empty( $_transient_data->response ) ) {
			$_transient_data->response = array();
		}

		// check if our run-time cache is populated, save a little hassle of having to loop through this over and over
		if ( $this->_product_update_data['loaded'] && ! $force_check ) {
			$_transient_data->response  = array_merge( (array) $_transient_data->response, $this->_product_update_data['response'] );

			if ( ! isset( $_transient_data->no_update ) ) {
				$_transient_data->no_update = array();
			}

			$_transient_data->no_update = array_merge( (array) $_transient_data->no_update, $this->_product_update_data['no_update'] );

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

			/* Handle legacy versions if available */
			if ( $this->should_use_legacy_version( $remote_product, $local_product_version ) ) {
				GravityPerks::log_debug( "Local product version: {$local_product_version}" );

				// Modify the remote product and swap the legacy version in for the new version
				$remote_product->new_version = $remote_product->legacy_version;
				$remote_product->version     = $remote_product->legacy_version;

				if ( isset( $remote_product->sections['legacy_changelog'] ) ) {
					$remote_product->sections['changelog'] = $remote_product->sections['legacy_changelog'];
				}
			}

			/*
			 * Unset needed keys from the product update data. Keys like changelog will be fetched using the
			 * `plugins_api` filter.
			 */
			$keys_to_remove = array(
				'sections',
				'download_link',
				'categories',
				'documentation',
				'changelog',
				'legacy_version',
				'legacy_changelog',
				'legacy_version_requirement',
				'version',
				'author',
				'last_updated',
			);

			foreach ( $keys_to_remove as $key ) {
				if ( isset( $remote_product->$key ) ) {
					unset( $remote_product->$key );
				}
			}

			// Change the 'homepage' key to 'url' to match the format of the WP.org API response.
			if (
				isset( $remote_product->homepage )
				&& ! isset( $remote_product->url )
			) {
				$remote_product->url = $remote_product->homepage;
				unset( $remote_product->homepage );
			}

			if ( $local_product_version ) {
				GravityPerks::log_debug( 'Product update found. Adding to local product update data.' . print_r( $remote_product, true ) );

				if ( version_compare( $local_product_version, $remote_product->new_version, '<' ) ) {
					$this->_product_update_data['response'][ $remote_product_file ] = $remote_product;
				} else {
					$this->_product_update_data['no_update'][ $remote_product_file ] = $remote_product;
				}
			}
		}

		$_transient_data->response  = array_merge( (array) $_transient_data->response, $this->_product_update_data['response'] );

		if ( ! isset( $_transient_data->no_update ) ) {
			$_transient_data->no_update = array();
		}

		// https://make.wordpress.org/core/2020/07/30/recommended-usage-of-the-updates-api-to-support-the-auto-updates-ui-for-plugins-and-themes-in-wordpress-5-5/
		$_transient_data->no_update = array_merge( (array) $_transient_data->no_update, $this->_product_update_data['no_update'] );

		$this->_product_update_data['loaded'] = true;

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() end. Returning update data.' . print_r( $_transient_data, true ) );

		$force_check = false;

		return $_transient_data;
	}

	/**
	 * Check if the product has a legacy version and meets the requirements
	 */
	public function should_use_legacy_version( $remote_product, $local_product_version ) {
		$has_legacy_version             = property_exists( $remote_product, 'legacy_version' ) && $remote_product->legacy_version;
		$has_legacy_version_requirement = property_exists( $remote_product, 'legacy_version_requirement' ) && $remote_product->legacy_version_requirement;

		if ( $has_legacy_version_requirement ) {
			preg_match( '/^([<>]=?)(.*)$/', $remote_product->legacy_version_requirement, $legacy_version_requirement_matches );
		}

		return $local_product_version
			&& $has_legacy_version
			&& $has_legacy_version_requirement
			&& ! empty( $legacy_version_requirement_matches )
			&& version_compare( $local_product_version, $legacy_version_requirement_matches[2], $legacy_version_requirement_matches[1] );
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

		if ( $this->should_use_legacy_version( $product, $this->get_local_product_version( $plugin_file ) ) ) {
			if ( isset( $product->legacy_changelog ) ) {
				$product->sections['changelog'] = $product->legacy_changelog;
			}

			$product->version = $product->legacy_version;
		}

		if ( rgar( $product->sections, 'changelog' ) ) {
			$product->sections['changelog'] = GWPerks::format_changelog( $product->sections['changelog'], $product );
		}

		GravityPerks::log_debug( 'Ok! Everything looks good. Let\'s build the response needed for WordPress.' );

		// don't allow other plugins to override the $request this function returns, several plugins use the 'plugins_api'
		// filter incorrectly and return a hard 'false' rather than returning the $_data object when they do not need to modify
		// the request which results in our customized $request being overwritten (WPMU Dev Dashboard v3.3.2 is one example)
		remove_all_filters( 'plugins_api' );

		// remove all the filters causes an infinite loop so add one dummy function so the loop can break itself
		add_filter( 'plugins_api', array( new GP_Late_Static_Binding(), 'GWAPI_dummy_func' ) );

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
		$default_args = array(
			'user-agent' => 'Gravity Perks ' . GWPerks::get_version(),
			'timeout'    => 15,
			'sslverify'  => (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true ),
		);

		if ( defined( 'GW_BASIC_AUTH_USERNAME' ) && defined( 'GW_BASIC_AUTH_PASSWORD' ) ) {
			$default_args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( GW_BASIC_AUTH_USERNAME . ':' . GW_BASIC_AUTH_PASSWORD ),
			);
		}

		return wp_parse_args( $args, $default_args );
	}

	public static function get_site_url() {
		return site_url( '', 'http' );
	}

}

<?php

/**
 * Allows plugins to use their own update API.
 * 
 * @author Pippin Williamson
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class GP_EDD_SL_Plugin_Updater {
	private $api_url = '';
	private $api_data = array();
	private $name = '';
	private $slug = '';

    private $_plugin_latest_version = null;

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
	function __construct( $_api_url, $_plugin_file, $_api_data = null ) {
		$this->api_url = trailingslashit( $_api_url );
		$this->api_data = $_api_data; //urlencode_deep( $_api_data );
		$this->name = plugin_basename( $_plugin_file );
		$this->slug = basename( $_plugin_file, '.php');
		$this->version = $_api_data['version'];
	
		// Set up hooks.
		$this->hook();
	}
	
	/**
	 * Set up Wordpress filters to hook into WP's update process.
	 * 
	 * @uses add_filter()
	 * 
	 * @return void
	 */
	private function hook() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ), 99 );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3);
	}

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
		
		if( ! $this->_plugin_latest_version ) {
			
			$to_send = array( 'slug' => $this->slug );
			$api_response = $this->api_request( 'plugin_latest_version', $to_send );
			$this->_plugin_latest_version = $api_response;

		}
		
		if( $this->is_valid_api_response( $this->_plugin_latest_version ) ) {
			if( version_compare( $this->version, $this->_plugin_latest_version->new_version, '<' ) )
				$_transient_data->response[$this->name] = $this->_plugin_latest_version;
		}
		
		return $_transient_data;
	}
	
	function is_valid_api_response( $response ) {
		return false !== $response && is_object( $response );
	}
	
	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 * 
	 * @uses api_request()
	 * 
	 * @param mixed $_data
	 * @param string $_action
	 * @param object $_args
	 * @return object $_data
	 */
	function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		if ( ( $_action != 'plugin_information' ) || !isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) return $_data;
	
		$to_send = array( 'slug' => $this->slug );

		$api_response = $this->api_request( 'plugin_information', $to_send );
		if ( false !== $api_response ) {
			$_data = $api_response;
			$_data->sections['changelog'] = GWPerks::format_changelog( $_data->sections['changelog'] );
		}

		return $_data;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 * 
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 * 
	 * @param string $_action The requested action.
	 * @param array $_data Parameters for the API action.
	 * @return false||object
	 */
	private function api_request( $_action, $_data ) {
		global $wp_version;

		$data = array_merge( $this->api_data, $_data );
		if( $data['slug'] != $this->slug )
			return false;
		
		$api_params = GWAPI::get_api_args( array( 
			'edd_action' 	=> 'get_version',
			'license' 		=> $data['license'], 
			'name' 			=> $data['item_name'],
			'slug' 			=> $this->slug,
			'author'		=> $data['author']
		) );

		$request_args = GWAPI::get_request_args( array(
			'body' => $api_params
		) );

		$request = wp_remote_post( $this->api_url, $request_args );
		
		if ( !is_wp_error( $request ) ):
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			if( $request )
				$request->sections = maybe_unserialize( $request->sections );
			return $request;
		else:
			return false;
		endif;
	}
}
<?php 

if ( ! defined( 'ABSPATH' ) )
	exit;
	
class WonderPlugin_Gallery_Update {

	private $controller, $slug, $version, $api_url, $update_response, $wp_version;
	
	function __construct($controller) {
		
		global $wp_version;
		
		$this->controller = $controller;
		$this->product = 'gallery';
		$this->slug = 'wonderplugin-gallery';
		$this->plugin = WONDERPLUGIN_GALLERY_PLUGIN;
		$this->version = WONDERPLUGIN_GALLERY_PLUGIN_VERSION;
		$this->wp_version = $wp_version;
		$this->domain = ( isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '' );
		$this->api_url = 'https://www.wonderplugin.com/update-check.php';
		$this->update_response = null;
		
		$settings = $this->controller->get_settings();
		if ($settings['disableupdate'] == 0)
			$this->init();
	}
	
	function init() {
		
		// check update on every page request, testing mode only
		// set_site_transient('update_plugins', null);
		
		// check for plugin update
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ) );
			
		// get  plugin information
		add_filter( 'plugins_api', array( $this, 'check_info'), 20, 3 );
		
		// display extra message in plugins page
		add_action( 'in_plugin_update_message-' . $this->plugin, array( $this, 'show_plugin_update_message' ), 10, 2 );
		
		// admin notice
		add_action( 'admin_notices', array( $this, 'plugin_update_notice' ) );
	}
	
	function plugin_update_notice() {
		
		$info = $this->controller->get_plugin_info();		
		if ( !empty($info->notice) )
			echo '<div class="error notice is-dismissible"><p>'. esc_html($info->notice) . '</p></div>';
				
		global $pagenow;
		
		if( $pagenow === 'update-core.php' && !empty($info))
		{
			if ( !isset($info->key_status) || $info->key_status != 'valid')
			{
				if ( isset($info->key_status) && $info->key_status == 'expired')
				{
					echo '<div class="error notice is-dismissible"><p><strong>' . $info->name . '</strong>: Your free upgrade period has expired. To update the plugin, please <a href="https://www.wonderplugin.com/renew/" target="_blank">renew your license</a>, otherwise, you will get the <strong>Update package not available</strong> error.</p></div>';
				}
				else
				{
					if (WONDERPLUGIN_GALLERY_VERSION_TYPE == 'F')
					{
						echo '<div class="error notice is-dismissible"><p><strong>' . $info->name . '</strong>: To update the plugin in WordPress backend, please <a href="https://www.wonderplugin.com/wordpress-gallery/order/" target="_blank">upgrade to a commercial license</a>, otherwise, you will get the <strong>Update package not available</strong> error.</div>';				
					}
					else
					{
						echo '<div class="error notice is-dismissible"><p><strong>' . $info->name . '</strong>: To update the plugin, please <a href="'. admin_url('admin.php?page=wonderplugin_gallery_register') . '">enter your license key</a>, otherwise, you will get the <strong>Update package not available</strong> error.</div>';
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * @param $action: basic_check, register, deregister
	 */
	function get_update_data($action, $key = '', $force = false) {
		
		global $pagenow;
		
		if (($action == 'register' || $action == 'deregister') && empty($key))
			return false;
		
		if (($action == 'basic_check') && !empty($this->update_response))
			return $this->update_response;
		
		if( $pagenow === 'update-core.php' && isset( $_GET['force-check'] ) )
			$force = true;
		
		$info = $this->controller->get_plugin_info();
		if (!$force && ($action == 'basic_check') && !empty($info) && isset($info->last_checked) && is_int($info->last_checked) && ((time() - $info->last_checked) < 2 * 60 * 60))
			return $info;
		
		$api_args = array(
			'action'	=> $action,
			'product'	=> $this->product,
			'version' 	=> $this->version,
			'wp_version'=> $this->wp_version,
			'key'		=> $key
		);
		
		if ($action == 'register' || $action == 'deregister')
			$api_args['domain'] = $this->domain;
		
		if ( empty($key) && !empty($info->key) )
			$api_args['key'] = $info->key;
		
		$request_params = array(
			'method'	=> 'POST',
			'body'		=> $api_args
		);

		$raw_response = wp_remote_post($this->api_url, $request_params);
		if ( !is_wp_error( $raw_response ) && ($raw_response['response']['code'] == 200) )
		{
			$response = unserialize( $raw_response['body'] );			
			if ( !empty( $response ) )
			{	
				if ($action == 'register' || $action == 'basic_check')
				{
					$response->slug = $this->slug;
					$response->plugin = $this->plugin;	
					$response->last_checked = time();
					$this->update_response = $response;
					$this->controller->save_plugin_info($response);
				}
				return $response;
			}
		}

		return false;
	}
	
	function check_for_plugin_update( $data ) {
				
		if ( empty( $data ) ) 
			return $data;
				
		// check for update
		$update_data = $this->get_update_data('basic_check');
						
		if( $update_data === false ) 
			return $data;
				
		// update
		if ( version_compare( $this->version, $update_data->version, '<' ) ) {
			$data->response[$this->plugin] = $update_data;	
		}
			
		return $data;
	}
	
	function check_info( $data, $action = '', $args = null ) {
				
		if ( $action !== 'plugin_information' || ! isset( $args->slug ) || $args->slug !== $this->slug )
			return $data;
		
		$update_data = $this->controller->get_plugin_info();
		if( $update_data === false )
		{			
			$update_data = $this->get_update_data('basic_check');
			if( $update_data === false )
				return $data;
		}
		
		return $update_data;
	}
	
	function show_plugin_update_message( $plugin_data, $r ) {
		
		$info = $this->controller->get_plugin_info();
				
		if ( isset($info->key_status) && $info->key_status == 'valid')
		{
			if (isset($info->key_expire) && $info->key_expire > 0)
			{
				if ($info->key_expire < 120)
				{
					echo ' Your license key will expire in ' . $info->key_expire . ' days.';
					echo ' To keep the plugin updated, log into the membership area and renew with 50% off discount: <a href="https://www.wonderplugin.com/renew/" target="_blank">renew your license</a>.';
				}
			}
			return;
		}
		else if ( isset($info->key_status) && $info->key_status == 'expired')
		{
			echo '<br>Your free upgrade period has expired. To get automatic update, please <a href="https://www.wonderplugin.com/renew/" target="_blank">renew your license</a>.';
		}
		else
		{
			if (WONDERPLUGIN_GALLERY_VERSION_TYPE == 'F')
			{
				echo '<br>To get automatic update, please <a href="https://www.wonderplugin.com/wordpress-gallery/order/" target="_blank">upgrade to a commercial license</a>.';
			}
			else
			{
				echo '<br>To get automatic update, please <a href="'. admin_url('admin.php?page=wonderplugin_gallery_register') . '">enter your license key</a> or <a href="https://www.wonderplugin.com/register-faq/#manualupdate" target="_blank">update the plugin manually</a>.';
			}
		}
	}
}
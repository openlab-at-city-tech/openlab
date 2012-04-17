<?php
//Plugin Upgrade Class
if ( !class_exists( "PluginUpgrade" ) ) :
class PluginUpgrade {
			private $plugin_url = false;
			private $remote_url = false;
			private $version = false;
			private $plugin_slug = false;
			private $plugin_path = false;
			private $time_upgrade_check = false;
			private $plugins = '';
			
			function __construct( $args = array() ) {
				//Load defaults
				extract( wp_parse_args( $args, array( 
					'remote_url' => false,
					'version' => false,
					'plugin_slug' => false,
					'plugin_path' => false,
					'plugin_url' => false,
					'time' => 43200
				) ) );
				$this->plugin_url = $plugin_url;
				$this->remote_url = $remote_url;
				$this->version = $version;
				$this->plugin_slug = $plugin_slug;
				$this->plugin_path = $plugin_path;
				$this->time_upgrade_check = apply_filters( "pu_time_{$plugin_slug}", $time );
				
				//Get plugins for upgrading
				$this->plugins = $this->get_plugin_options();
				
				add_action( 'admin_init', array( &$this, 'init' ), 1 );
				add_action( "after_plugin_row_{$plugin_path}", array( &$this, 'plugin_row' ) );
				
				
			} //end constructor
			public function init() {
				//Set up update checking and hook in the filter for automatic updates
				//Do upgrade stuff
				//todo check is_admin() ?
				if (current_user_can("administrator")) {
					$this->check_periodic_updates();
					if ( isset( $this->plugins[ $this->plugin_slug ]->new_version ) ) {
						if( !version_compare( $this->version, $this->plugins[ $this->plugin_slug ]->new_version, '>=' ) ) {
							add_filter( 'site_transient_update_plugins', array( &$this, 'update_plugins_filter' ),1000 );
						}
					}
				}
			}
			//Performs a periodic upgrade check to see if the plugin needs to be upgraded or not
			private function check_periodic_updates() {	
				$last_update = isset( $this->plugins[ $this->plugin_slug ]->last_update ) ? $this->plugins[ $this->plugin_slug ]->last_update : false;
				if ( !$last_update || is_object( $last_update ) ) { $last_update = $this->check_for_updates(); }
				if( ( time() - $last_update ) > $this->time_upgrade_check ){
						$this->check_for_updates();
				}
			} //end check_periodic_updates
			public function get_remote_version() {
				if ( isset( $this->plugins[ $this->plugin_slug ]->new_version ) ) {
					return $this->plugins[ $this->plugin_slug ]->new_version;
				}
				return false;
			}
			private function get_plugin_options() {
				//Get plugin options
				if ( is_multisite() ) {
					$options = get_site_option( 'pu_plugins' );
				} else {
					$options = get_option( 'pu_plugins' );
				}
				if ( !$options ) {
					$options = array();
				}
				return $options;
			}
			private function save_plugin_options() {
				//Get plugin options
				if ( is_multisite() ) {
					//For some reason, MS doesn't update the option
					global $wpdb;
					$value = sanitize_option( 'pu_plugins', $this->plugins );
					wp_cache_set( "{$wpdb->siteid}:pu_plugins", $this->plugins, 'site-options' );
					$value = maybe_serialize( $value );
					$result = $wpdb->update( $wpdb->sitemeta, array( 'meta_value' => $value), array( 'site_id' => $wpdb->siteid, 'meta_key' => 'pu_plugins' ) );
				} else {
					$options = update_option( 'pu_plugins', $this->plugins );
				}
			}
			public function check_for_updates( $manual = false ) {
				if ( !is_array( $this->plugins ) ) return false;
				//Check to see that plugin options exist
				if ( !isset( $this->plugins[ $this->plugin_slug ] ) ) {

					$plugin_options = new stdClass;
					$plugin_options->url = $this->plugin_url;
					$plugin_options->slug = $this->plugin_slug;
					$plugin_options->package = '';
					$plugin_options->new_version = $this->version;
					$plugin_options->last_update = time();
					$plugin_options->id = "0";
					
					$this->plugins[ $this->plugin_slug ] = $plugin_options;
					$this->save_plugin_options();
				}
												

				$current_plugin = $this->plugins[ $this->plugin_slug ];
				if( ( time() - $current_plugin->last_update ) > $this->time_upgrade_check || $manual ) {
					//Check for updates
					$version_info = $this->perform_remote_request( 'get_version' );
					if ( is_wp_error( $version_info ) ) return false;
					//$version_info should be an array with keys ['version'] and ['download_url'] 
					if ( isset( $version_info->version ) && isset( $version_info->download_url ) ) {
						$current_plugin->new_version = $version_info->version;
						$current_plugin->package = $version_info->download_url;
						$current_plugin->remote_response = $version_info;
						$this->plugins[ $this->plugin_slug ] = $current_plugin;
						$this->save_plugin_options();
					}
				}
				return $this->plugins[ $this->plugin_slug ];
			} //end check_for_updates
			
			public function plugin_row( $plugin_name ) {
				do_action( "pu_plugin_row_{$this->plugin_slug}", $plugin_name );
			}
			public function perform_remote_request( $action, $body = array(), $headers = array(), $return_format = 'json', $remote_url = false ) {
			
				$body = wp_parse_args( $body, array( 
					'action' => $action,
					'wp-version' => get_bloginfo( 'version' ),
					'referer' => site_url()
				) ) ;
				$body = http_build_query( apply_filters( "pu_remote_body_{$this->plugin_slug}", $body ) );
				
				$headers = wp_parse_args( $headers, array( 
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Content-Length' => strlen( $body )
				) );
				$headers = apply_filters( "pu_remote_headers_{$this->plugin_slug}", $headers );
				
				$post = array( 'headers' => $headers, 'body' => $body );
				//Retrieve response
				$remote_url = $remote_url ? $remote_url : $this->remote_url;
				$response = wp_remote_post( esc_url_raw( $remote_url ), $post );
				$response_code = wp_remote_retrieve_response_code( $response );
				$response_body = wp_remote_retrieve_body( $response );
				
				if ( $response_code != 200 || is_wp_error( $response_body ) ) {
					return false;
				}
				
				if ( $return_format != 'json' ) {
					return $response_body;
				} else {
					return json_decode( $response_body );
				}
				return false;
			} //end perform_remote_request
		
		//Return an updated version to WordPress when it runs its update checker
		public function update_plugins_filter( $value ) {
			if ( isset( $this->plugins[ $this->plugin_slug ] ) && $this->plugin_path ) {
				$value->response[ $this->plugin_path ] = $this->plugins[ $this->plugin_slug ];
			}
			return apply_filters( "pu_plugin_info_{$this->plugin_slug}", $value, $this->plugin_path, $this->plugin_slug );
		}
		
} //end class
endif;
?>